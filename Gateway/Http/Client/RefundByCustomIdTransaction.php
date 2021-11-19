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
use Yedpay\YedpayMagento\Setup\InstallData;

class RefundByCustomIdTransaction extends AbstractTransaction
{
    protected $config;
    protected $storeManager;


    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        YedpayLogger $yedpayLogger
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
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

        try {
            $client = new Client($environment, $apiKey, false);
            $refundResponse = $client->refundByCustomId($customId);
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

            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($customId);
            $order->setState(Order::STATE_CLOSED)->setStatus(InstallData::ORDER_STATUS_YEDPAY_REFUNDED_CODE);
            $order->save();
            $this->yedpayLogger->info("[OnlinePayment Notification]: Transaction [{$customId}] payment status changed to {$order->getStatus()}");

            $response['success'] = true;
        }

        return $response;
    }
}
