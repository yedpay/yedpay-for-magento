<?php

namespace Yedpay\YedpayMagento\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Yedpay\YedpayMagento\Gateway\Config\Config;
use Yedpay\YedpayMagento\Model\Ui\ConfigProvider;

class AdditionalConfigProvider implements ConfigProviderInterface {

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Config $config
     * @param SessionManagerInterface $session
     */
    public function __construct(Config $config) 
    {
        $this->config = $config;
    }
    
    public function getConfig() {
        return [
            'payment' => [
                ConfigProvider::CODE => [
                    'logoSrc' => 'https://www.yedpay.com/images/main-logo-dark.svg',
                    'description' => $this->config->getDescription(),
                    'gatewayLogoImgSrc' => $this->config->getGatewayLogo(),
                ],
            ],
        ];
    }       
}