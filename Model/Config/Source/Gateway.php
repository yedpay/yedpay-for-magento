<?php

namespace Yedpay\YedpayMagento\Model\Config\Source;

class Gateway implements \Magento\Framework\Option\ArrayInterface
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
            '4_2' => 'Alipay Online',
            '8_2' => 'WeChat Pay Online',
        ];
    }
}