<?php
namespace Yedpay\YedpayMagento\Gateway\Request;

use Exception;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Yedpay\YedpayMagento\Gateway\SubjectReader;
use Yedpay\YedpayMagento\Logger\YedpayLogger;
use Yedpay\YedpayMagento\Observer\DataAssignObserver;

class RefundByCustomIdDataBuilder implements BuilderInterface
{
    private $subjectReader;
    private $sci;
    private $ori;
    private $yedpayLogger;

    /**
     * Constructor
     *
     * @param SubjectReader $subjectReader
     */
    public function __construct(
        SubjectReader $subjectReader,
        OrderRepository $ori,
        SearchCriteriaBuilder $sci,
        YedpayLogger $yedpayLogger
    ) {
        $this->subjectReader = $subjectReader;
        $this->sci = $sci;
        $this->ori = $ori;
        $this->yedpayLogger = $yedpayLogger;
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
        $customId = $payment->getAdditionalInformation(DataAssignObserver::CUSTOM_ID);

        $amountPaid = $this->getAmountPaid($customId);
        if (bccomp($amountPaid, $buildSubject['amount']) !== 0) {
            throw new InputException('Refund amount should be same as order amount');
        }

        $result = [
            OnlinePaymentDataBuilder::CUSTOM_ID => $customId,
        ];

        return $result;
    }

    /**
     * @param string $customId
     * @return mixed
     */
    private function getAmountPaid(string $customId)
    {
        $searchCriteria = $this->sci->addFilter('increment_id', $customId, 'eq')->create();
        $orderList = $this->ori->getList($searchCriteria)->getItems();

        if (!$orderList) {
            $message = "No orders with CustomId [$customId] was found.";
            $this->yedpayLogger->error($message);
            throw new NotFoundException($message);
        }

        $order = reset($orderList);
        return $order->getPayment()->getAmountPaid();
    }
}
