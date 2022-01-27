<?php

namespace Yedpay\YedpayMagento\Model\Ui;

use Yedpay\YedpayMagento\Gateway\Config\Config;
use Magento\Braintree\Gateway\Request\PaymentDataBuilder;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'yedpay';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SessionManagerInterface
     */
    private $session;
    /**
     * Constructor
     *
     * @param Config $config
     * @param SessionManagerInterface $session
     */
    public function __construct(Config $config, SessionManagerInterface $session)
    {
        $this->config = $config;
        $this->session = $session;
    }
    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $storeId = $this->session->getStoreId();
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive($storeId),
                    'title' => $this->config->getTitle(),
                    'paymentAction' => $this->config->getPaymentAction($storeId),
                    'currency' => $this->config->getCurrency($storeId),
                    'environment' => $this->config->getEnvironment($storeId),
                ],
            ],
        ];
    }
}
