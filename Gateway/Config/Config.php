<?php

namespace Yedpay\YedpayMagento\Gateway\Config;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Serialize\Serializer\Json;
use Yedpay\YedpayMagento\Logger\YedpayLogger;

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
    const KEY_GATEWAY_LOGO = 'gateway_logo';

    protected $_assetRepo;
    protected $dir;
    protected $yedpayLogger;

    /**
     * YedpayMagento config constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param null|string $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DirectoryList $dir,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN,
        YedpayLogger $yedpayLogger,
        RequestInterface $request,
        Repository $assetRepo
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->yedpayLogger = $yedpayLogger;
        $this->dir = $dir;
        $this->request = $request;
        $this->assetRepo = $assetRepo;
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
     * Returns Logo Image Path
     *
     * @return string
     */
    public function getImagePath()
    {
        $path = $this->dir->getRoot() . '/app/code/Yedpay/YedpayMagento/Images/';
        return $path;
        // return WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/images/';
    }

    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (Exception $e) {
            $this->yedpayLogger->info($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
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

    public function getGatewayLogo()
    {
        switch ($this->getValue(Config::KEY_GATEWAY_LOGO)) {
            case 'ALL':
                $icon_path = $this->getLogoPath('all');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_all.svg");
                break;

            case '4':
                $icon_path = $this->getLogoPath('4_2_all');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay.svg");
                break;

            case '4HK':
                $icon_path = $this->getLogoPath('4_2_HK');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_hk.svg");
                break;

            case '4CN':
                $icon_path = $this->getLogoPath('4_2_CN');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_cn.svg");
                break;

            case '8':
                $icon_path = $this->getLogoPath('8_2');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_wechatpay.svg");
                break;

            case '9':
                $icon_path = $this->getLogoPath('9_1');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_unionpay.svg");
                break;

            case '12VM':
                $icon_path = $this->getLogoPath('12_1');
                // $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_vm.svg");
                break;

            case '4_8_9':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_uqaw.svg");
                break;

            case '0':
            default:
                $icon_path = $this->getLogoPath();
                break;
        }
        return $icon_path;
    }

    /**
     * function to get default logo path
     *
     * @return string
     */
    protected function getLogoPath($gateway = null)
    {
        if (!$gateway) {
            $gateway = $this->getGateway();
        }

        switch ($gateway) {
            case '4_2':
                if ($this->getWallet() == 'CN') {
                    $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_cn.svg");
                } elseif ($this->getWallet() == 'HK') {
                    $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_hk.svg");
                } else {
                    $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay.svg");
                }
                break;

            case '4_2_all':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay.svg");
                break;

            case '4_2_HK':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_hk.svg");
                break;

            case '4_2_CN':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_alipay_cn.svg");
                break;

            case '8_2':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_wechatpay.svg");
                break;

            case '9_1':
            case '9_5':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_unionpay.svg");
                break;

            case '12_1':
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_vm.svg");
                break;

            case 'all':
            default:
                $icon_path = $this->getViewFileUrl("Yedpay_YedpayMagento::img/methods/yedpay_all.svg");
                break;
        }
        return $icon_path;
    }


    public function getExpiryTime()
    {
        return $this->getValue(Config::KEY_EXPIRY_TIME);
    }
}
