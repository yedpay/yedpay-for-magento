<?php

namespace Yedpay\YedpayMagento\Controller\payment;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session;
use Magento\Store\Model\StoreManagerInterface;
use Yedpay\YedpayMagento\Gateway\Config\Config;
use Yedpay\YedpayMagento\Logger\YedpayLogger;
use Yedpay\Client;
use Yedpay\Response\Error;

class GetPaymentUrl extends \Magento\Framework\App\Action\Action
{
    const CUSTOM_ID_LENGTH = 20;
    const RESPONSE_SUCCESS = 200;
    const RESPONSE_BAD_REQUEST = \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST;

    protected $config;
    protected $yedpayLogger;
    protected $storeManager;
    protected $checkoutSession;
    protected $quoteRepository;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Config $config,
        YedpayLogger $yedpayLogger,
        StoreManagerInterface $storeManager,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->yedpayLogger = $yedpayLogger;
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->resultJsonFactory = $resultJsonFactory;

        $this->yedpay_version = '1.0.0';
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $amount = $data['amount'];
        $currency = $data['currency'];
        $gatewayCode = $this->config->getGateway();
        $wallet = $this->config->getWallet();

        $storeId = $this->storeManager->getStore()->getId();
        $customId = $this->getCustomId($this->checkoutSession->getQuoteId());
        $returnUrl = $this->buildReturnUrl();
        $notifyUrl = $this->buildNotifyUrl();
        $apiKey = $this->config->getApiKey($storeId);
        $environment = $this->config->getEnvironment($storeId);

        if ($currency != Client::CURRENCY_HKD) {
            $this->yedpayLogger->warning("Yedpay: Currency not support. Expect HKD, got $currency." . $currency);
            return $this->jsonResponse(self::RESPONSE_BAD_REQUEST, ['message' => "$currency not supported"]);
        }

        try {
            $client = new Client($environment, $apiKey, false);
            $client->setCurrency(Client::INDEX_CURRENCY_HKD)
                ->setNotifyUrl($notifyUrl)
                ->setReturnUrl($returnUrl)
                ->setExpiryTime($this->config->getExpiryTime())
                ->setMetadata(json_encode([
                    'yedpay_for_magento' => $this->yedpay_version,
                    'magento' => '2.4.3',
                ]))
                ;
            if ($gatewayCode) {
                $client->setGatewayCode($gatewayCode);
                if ($wallet) {
                    $client->setWallet($wallet);
                }
            }

            $onlinePayment = $client->onlinePayment($customId, round($amount, 2));

        } catch (Exception $e) {
            $this->yedpayLogger->error($e->getMessage(), $e);
            return $this->jsonResponse(self::RESPONSE_BAD_REQUEST, ['message' => $e->getMessage()]);
        }

        if ($onlinePayment instanceof Error) {
            $message = "Unable to process with gateway. Please contact store owner.";
            $logMessage = "[OnlinePayment] Server message: ";
            $this->yedpayLogger->error(
                $onlinePayment->getMessage() ? "$logMessage {$onlinePayment->getMessage()}" : $logMessage,
                $onlinePayment->getErrors() ?? []
            );
            return $this->jsonResponse(self::RESPONSE_BAD_REQUEST, ['message' => $message]);
        }
        $serverResponseData = json_decode(json_encode($onlinePayment->getData()), true);
        $checkoutUrl = $serverResponseData['checkout_url'];

        $responseData = ['checkout_url' => $checkoutUrl, 'custom_id' => $customId];

        return $this->jsonResponse(self::RESPONSE_SUCCESS, $responseData);
    }

    /**
     * @param int $statusCode
     * @param Array $data
     * @return ResultInterface
     */
    function jsonResponse(int $statusCode, array $data)
    {
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setHttpResponseCode($statusCode);
        $resultJson->setData($data);

        return $resultJson;
    }

    /**
     * Build returnUrl for redirection after payment
     * @return string
     */
    function buildReturnUrl(): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $url = $baseUrl . 'yedpay/order/checkout';
        return $url;
    }

    /**
     * Build notifyUrl for receiving notification from gateway server
     * @return string
     */
    function buildNotifyUrl(): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $url = $baseUrl . 'yedpay/payment/onnotificationreceived';
        return $url;
    }

    /**
     * Randomly generate a customId for future identification purpose with payment gateway
     * @return string
     */
    function getCustomId($quoteId): string
    {
        $quote = $this->quoteRepository->getActive($quoteId);
        $quote->reserveOrderId();
        $quote->save();

        return $quote->getReservedOrderId();
    }
}
