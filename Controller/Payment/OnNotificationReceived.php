<?php

namespace Yedpay\YedpayMagento\Controller\payment;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\StoreManagerInterface;
use Yedpay\YedpayMagento\Gateway\Config\Config;
use Yedpay\YedpayMagento\Observer\DataAssignObserver;
use Yedpay\YedpayMagento\Logger\YedpayLogger;
use Yedpay\YedpayMagento\Setup\InstallData;
use Magento\Sales\Model\Order;
use Yedpay\Client;
use Magento\Framework\Webapi\Rest\Request;

class OnNotificationReceived extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_VOID = 'void';

    protected $context;
    protected $config;
    protected $cri;
    protected $ori;
    protected $sci;
    protected $yedpayLogger;
    protected $storeManager;
    protected $request;
    protected $httpRequest;

    public function __construct(
        Context $context,
        Config $config,
        CartRepositoryInterface $cri,
        OrderRepository $ori,
        SearchCriteriaBuilder $sci,
        YedpayLogger $yedpayLogger,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        Request $httpRequest
    ) {
        $this->request = $request;
        parent::__construct($context);
        $this->config = $config;
        $this->cri = $cri;
        $this->ori = $ori;
        $this->sci = $sci;
        $this->yedpayLogger = $yedpayLogger;
        $this->storeManager = $storeManager;
        $this->httpRequest = $httpRequest;
    }

    public function execute()
    {
        if ($this->httpRequest->getHttpMethod() !== 'POST') {
            throw new InvalidRequestException(null, ['message' => "Invalid Method"]);
        }

        // Handle online payment and refund

        $data = $this->getRequest()->getParams();
        $transaction = $data['transaction'];

        // Handle refund (adyen only)

        if (
            $transaction['status'] == self::PAYMENT_STATUS_REFUNDED ||
            $transaction['status'] == self::PAYMENT_STATUS_VOID
        ) {

            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $order = $objectManager
                ->create(\Magento\Sales\Model\Order::class)
                ->loadByIncrementId($transaction['custom_id']);
            $orderGrandTotal = $order->getGrandTotal();
            $refundedAmount = $transaction['refunded'];

            if ($orderGrandTotal != $refundedAmount) {
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus(InstallData::ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_CODE);
            } else {
                $order->setState(Order::STATE_CLOSED)->setStatus(InstallData::ORDER_STATUS_YEDPAY_REFUNDED_CODE);
            }

            $order->addStatusHistoryComment($this->getRefundInformation($data['transaction']));
            $order->addStatusHistoryComment("Order status changed to {$order->getStatus()}.");
            $this->yedpayLogger->info(
                "[OnlinePayment Notification]:
                        Transaction [{$transaction['transaction_id']}] status changed to {$transaction['status']}"
            );
            $this->ori->save($order);
            $this->getResponse()->setBody('success');
        }

        $storeId = $this->storeManager->getStore()->getId();
        $signKey = $this->config->getSignKey($storeId);
        $newOrderStatus = $this->config->getOrderStatusAfterNotification($storeId);

        if (
            !isset($transaction['custom_id']) ||
            !isset($transaction['status']) ||
            !isset($transaction['transaction_id'])
        ) {
            $this->yedpayLogger->info('[OnlinePayment Notification]: Invalid notification format');
            return;
        }

        if ($transaction['status'] != self::PAYMENT_STATUS_PAID) {
            $this->yedpayLogger->warning(
                "Transaction [{$transaction['transaction_id']}] not paid. Payment status: {$transaction['status']}"
            );
            return;
        }

        $customId = $transaction['custom_id'];
        $transactionId = $transaction['transaction_id'];
        $paymentMethod = $transaction['payment_method'];

        try {
            $client = new Client();
            $isSignValid = $client->verifySign($data, $signKey);
        } catch (Exception $e) {
            $this->yedpayLogger->error($e->getMessage(), $e);
            return;
        }

        if (!$isSignValid) {
            $this->yedpayLogger->error(
                "[OnlinePayment Notification]: Invalid sign. Order ID / Custom ID: $customId"
            );
            return;
        }

        $searchCriteria = $this->sci->addFilter('increment_id', $customId, 'eq')->create();
        $orderList = $this->ori->getList($searchCriteria)->getItems();

        if (!$orderList) {
            $this->yedpayLogger->error(
                "[OnlinePayment Notification]: No orders with CustomId $customId was found."
            );
            return;
        }

        $order = reset($orderList);

        $payment = $order->getPayment();
        $paymentCustomId = $payment->getAdditionalInformation(DataAssignObserver::CUSTOM_ID);
        if ($customId != $paymentCustomId) {
            $this->yedpayLogger->warning(
                '[OnlinePayment Notification]:
                        No order with ' . DataAssignObserver::CUSTOM_ID . ' ' . $customId . ' was found'
            );
            return;
        }

        $payment->setIsTransactionPending(false);
        $payment->setTransactionId($transactionId);
        $payment->setParentTransactionId($payment->getTransactionId());
        $transaction = $payment->addTransaction(TransactionInterface::TYPE_CAPTURE, null, true, "");
        $transaction->setIsClosed(false);

        $additionalInformation = $payment->getAdditionalInformation();
        $additionalInformation['payment_method'] = $paymentMethod;
        $payment->setAdditionalInformation($additionalInformation);
        $payment->update();

        $order->setState($newOrderStatus)->setStatus(InstallData::ORDER_STATUS_YEDPAY_CONFIRMED_CODE);
        $order->addStatusHistoryComment($this->getTransactionInformation($data['transaction']));

        $transaction->save();
        $this->ori->save($order);
        $this->getResponse()->setBody('success');
    }

    /**
     * Create exception in case CSRF validation failed.
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Show Transaction Information
     *
     * @param array $payment_data
     * @return string
     */
    protected function getTransactionInformation($payment_data)
    {
        return  '<b>Yedpay Transaction Information: </b><br>
                Order ID: ' . $payment_data['custom_id'] . '<br>
                Yedpay Transaction ID: ' . $payment_data['transaction_id'] . '<br>
                Transaction ID: ' . $payment_data['id'] . '<br>
                Gateway: ' . $payment_data['payment_method'] . '<br>
                Status: ' . $payment_data['status'] . '<br>
                Amount: ' . $payment_data['amount'] . '<br>
                Currency: ' . $payment_data['currency'] . '<br>
                Paid Time: ' . $payment_data['paid_at'];
    }

    /**
     * Show Refund Information
     *
     * @param array $refund_data
     * @return string
     */
    protected function getRefundInformation($refund_data)
    {
        return  '<b>Yedpay Refund Information:</b><br>
                Order ID: ' . $refund_data['custom_id'] . '<br>
                Yedpay Transaction ID: ' . $refund_data['transaction_id'] . '<br>
                Transaction ID: ' . $refund_data['id'] . '<br>
                Gateway: ' . $refund_data['payment_method'] . '<br>
                Status: ' . $refund_data['status'] . '<br>
                Refunded Amount: ' . $refund_data['refunded'] . '<br>
                Currency: ' . $refund_data['currency'] . '<br>
                Refund Time: ' . $refund_data['refunded_at'];
    }
}
