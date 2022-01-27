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
            '4_2' => 'Alipay Online Only',
            '8_2' => 'WeChat Pay Online Only',
            '9_1' => 'UnionPay ExpressPay Only',
            '9_5' => 'UnionPay UPOP Only',
            '12_1' => 'VISA / Mastercard Only',
        ];
    }
}
