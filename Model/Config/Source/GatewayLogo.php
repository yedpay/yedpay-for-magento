<?php

namespace Yedpay\YedpayMagento\Model\Config\Source;

class GatewayLogo implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '0' => 'Default (Logo change depends on gateway selected)',
            'ALL' => 'All (include Alipay Online, WeChat Pay Online, UnionPay ExpressPay/UPOP, Visa/mastercard)',
            '4' => 'Alipay Online (All Wallet)',
            '4HK' => 'Alipay Online (Hong Kong Wallet)',
            '4CN' => 'Alipay Online (China Wallet)',
            '8' => 'WeChat Pay Online',
            '9' =>  'UnionPay (ExpressPay and UPOP)',
            '12VM' => 'Visa / Mastercard',
            '4_8_9' => 'Alipay Online, WeChat Pay Online and UnionPay',
        ];
    }
}
