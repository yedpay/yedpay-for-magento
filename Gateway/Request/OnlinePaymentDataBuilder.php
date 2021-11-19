<?php

namespace Yedpay\YedpayMagento\Gateway\Request;

use Yedpay\YedpayMagento\Gateway\SubjectReader;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Yedpay\YedpayMagento\Observer\DataAssignObserver;

/**
 * Payment Data Builder
 */
class OnlinePaymentDataBuilder implements BuilderInterface
{
    use Formatter;

    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    const CUSTOM_ID = 'custom_id';

    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $result = [
            self::AMOUNT => $this->formatPrice($this->subjectReader->readAmount($buildSubject)),
            self::CURRENCY => $order->getCurrencyCode(),
        ];

        return $result;
    }
}