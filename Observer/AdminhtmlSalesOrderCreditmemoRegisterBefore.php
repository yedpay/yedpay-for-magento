<?php

namespace Yedpay\YedpayMagento\Observer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Yedpay\YedpayMagento\Logger\YedpayLogger;

class AdminhtmlSalesOrderCreditmemoRegisterBefore implements \Magento\Framework\Event\ObserverInterface
{

    protected $yedpayLogger;

    public function __construct(
        YedpayLogger $yedpayLogger
    ) {
        $this->yedpayLogger = $yedpayLogger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        $payment = $order->getPayment();
        $grandTotal = $creditmemo->getGrandTotal();

        $payment->setAdditionalInformation('grand_total_before_create_creditmemo', $grandTotal)->save();
    }
}
