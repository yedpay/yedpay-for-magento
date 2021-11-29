<?php

namespace Yedpay\YedpayMagento\Gateway\Http\Client;

use Exception;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Yedpay\Client;
use Yedpay\Response\Error;
use Yedpay\YedpayMagento\Gateway\Config\Config;
use Yedpay\YedpayMagento\Gateway\Request\OnlinePaymentDataBuilder;
use Yedpay\YedpayMagento\Logger\YedpayLogger;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;

class RefundByCustomIdTransaction extends AbstractTransaction
{
    protected $config;
    protected $storeManager;
    protected $ori;
    protected $yedpayLogger;


    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        OrderRepositoryInterface $ori,
        YedpayLogger $yedpayLogger
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->ori = $ori;
        $this->yedpayLogger = $yedpayLogger;
    }

    protected function process(TransferInterface $transferObject)
    {

        $response = [];

        $data = $transferObject->getBody();
        $storeId = $this->storeManager->getStore()->getId();

        $customId = $data[OnlinePaymentDataBuilder::CUSTOM_ID];
        $apiKey = $this->config->getApiKey($storeId);
        $environment = $this->config->getEnvironment($storeId);

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($customId);
        $payment = $order->getPayment();
        $grandTotal = $payment->getAdditionalInformation('grand_total_before_create_creditmemo');

        if ($grandTotal < 0){
            $this->yedpayLogger->error('Yedpay Refund amount cannot be negative. Custom ID: ' . $customId);
            $errorMsg = "Yedpay Refund amount cannot be negative.";
            throw new \Exception($errorMsg);
            return;
        }

        try {
            $client = new Client($environment, $apiKey, false);
            $refundResponse = $client->refundByCustomId($customId, null, $grandTotal);
        } catch (Exception $e) {
            $this->yedpayLogger->error($e->getMessage(), $e);
            throw new \Exception($e->getMessage());
        }
        
        if ($refundResponse instanceof Error) {
            $errorMsg = "Cannot refund transaction $customId";
            $this->yedpayLogger->error($errorMsg);
            throw new \Exception($errorMsg);
        } else {
            $response = json_decode(json_encode($refundResponse->getData()), true);
            $response['success'] = true;
        }

        return $response;
    }
}
