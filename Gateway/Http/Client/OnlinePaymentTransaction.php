<?php

namespace Yedpay\YedpayMagento\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\TransferInterface;

class OnlinePaymentTransaction extends AbstractTransaction
{

    protected function process(TransferInterface $transferObject)
    {
        return $transferObject->getBody();
    }
}
