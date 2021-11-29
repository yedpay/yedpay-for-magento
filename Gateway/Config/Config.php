<?php

namespace Yedpay\YedpayMagento\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
/**
 * Class Config
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_TITLE = 'title';
    const KEY_PAYMENT_ACTION = 'payment_action';
    const KEY_CURRENCY = 'currency';
    const KEY_ENVIRONMENT = 'environment';
    const KEY_SIGN_KEY = 'sign_key';
    const KEY_API_KEY = 'api_key';
    const KEY_ORDER_STATUS_BEFORE_NOTIFICATION = 'order_status';
    const KEY_ORDER_STATUS_AFTER_NOTIFICATION = 'order_status_after_notification';
    const KEY_DESCRIPTION = 'description';
    const KEY_GATEWAY = 'gateway';
    const KEY_WALLET = 'wallet';
    const KEY_EXPIRY_TIME = 'expiry_time';

    /**
     * YedpayMagento config constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param null|string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * Gets Payment configuration status.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return (bool) $this->getValue(Config::KEY_ACTIVE, $storeId);
    }

    /**
     * Get title of payment
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getValue(Config::KEY_TITLE);
    }

    /**
     * Gets payment action configuration.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPaymentAction($storeId = null)
    {
        return $this->getValue(Config::KEY_PAYMENT_ACTION, $storeId);
    }

    /**
     * Gets currency configuration.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getCurrency($storeId = null)
    {
        return $this->getValue(Config::KEY_CURRENCY, $storeId);
    }

    /**
     * Gets test mode configuration.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function getEnvironment($storeId = null)
    {
        return $this->getValue(Config::KEY_ENVIRONMENT, $storeId);
    }

    /**
     * Gets Yedpay sign_key.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSignKey($storeId = null)
    {
        return $this->getValue(Config::KEY_SIGN_KEY, $storeId);
    }

    /**
     * Gets Yedpay api_key.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getApiKey($storeId = null)
    {
        return $this->getValue(Config::KEY_API_KEY, $storeId);
    }

    // /**
    //  * Gets order status configuration.
    //  *
    //  * @param int|null $storeId
    //  * @return string
    //  */
    // public function getOrderStatusBeforeNotification($storeId = null)
    // {
    //     return $this->getValue(Config::KEY_ORDER_STATUS_BEFORE_NOTIFICATION, $storeId);
    // }

    /**
     * Gets order process configuration.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOrderStatusAfterNotification($storeId = null)
    {
        return $this->getValue(Config::KEY_ORDER_STATUS_AFTER_NOTIFICATION, $storeId);
    }

    public function getDescription()
    {
        return $this->getValue(Config::KEY_DESCRIPTION);
    }

    public function getGateway()
    {
        return $this->getValue(Config::KEY_GATEWAY);
    }

    public function getWallet()
    {
        return $this->getValue(Config::KEY_WALLET);
    }

    public function getExpiryTime()
    {
        return $this->getValue(Config::KEY_EXPIRY_TIME);
    }

}