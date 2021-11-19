<?php
namespace Yedpay\YedpayMagento\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Yedpay\YedpayMagento\Gateway\SubjectReader;

class RefundByCustomIdHandler implements HandlerInterface
{
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
    public function handle(array $handlingSubject, array $response)
    {
        if (!$response['success']) {
            return;
        }

        $paymentDO = $this->subjectReader->readPayment($handlingSubject);

        if ($paymentDO->getPayment() instanceof Payment) {
            $transactionId = $response[CaptureDataBuilder::TRANSACTION_ID];
            $payment = $paymentDO->getPayment();
            $shouldCloseParentTransaction = !$payment->getCreditmemo()->getInvoice()->canRefund();
            
            $payment->setTransactionId($transactionId);
            $payment->setIsTransactionClosed(true);
            $payment->setShouldCloseParentTransaction($shouldCloseParentTransaction);
        }
    }
}