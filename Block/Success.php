<?php

namespace Yedpay\YedpayMagento\Block;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    protected $_template = 'Yedpay_YedpayMagento::checkout/success.phtml';
    public $order = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->order = $this->_checkoutSession->getLastRealOrder();
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getPaymentMethod()
    {
        if ($this->order && $this->order->getPayment()) {
            $paymentMethod = $this->order->getPayment()->getAdditionalInformation('payment_method');
            return $paymentMethod;
        } else {
            return null;
        }
    }
}
