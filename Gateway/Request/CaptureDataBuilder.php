<?php

namespace Yedpay\YedpayMagento\Gateway\Request;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Helper\Formatter;
use Yedpay\YedpayMagento\Gateway\SubjectReader;
use Yedpay\YedpayMagento\Observer\DataAssignObserver;

/**
 * Payment Data Builder
 */
class CaptureDataBuilder implements BuilderInterface
{
    const TRANSACTION_ID = 'transaction_id';

    private $subjectReader;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);

        $payment = $paymentDO->getPayment();
        $transactionId = $payment->getTransactionId();

        $result = [
            self::TRANSACTION_ID => $transactionId,
        ];

        return $result;
    }
}