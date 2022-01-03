<?php

namespace Yedpay\YedpayMagento\Model\Config\Source;

class Wallet implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => 'All',
            'HK' => 'Hong Kong Wallet',
            'CN' => 'China Wallet',
        ];
    }
}