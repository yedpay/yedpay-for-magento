<?php
namespace Yedpay\YedpayMagento\Sales\Model;

class Order extends \Magento\Sales\Model\Order
{
    public function canCancel()
    {
        $now = time();
        $created = strtotime($this->getCreatedAt());
        $can_cancel = ($now - $created) > (60*60*3);

        if ($can_cancel && $this->isPaymentReview()) {
            return true;
        }

        return parent::canCancel();
    }
}
