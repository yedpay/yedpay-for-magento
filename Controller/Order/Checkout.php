<?php

namespace Yedpay\YedpayMagento\Controller\Order;

use Exception;
use Magento\Framework\App\Action\Context;
use Yedpay\YedpayMagento\Logger\YedpayLogger;


class Checkout extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $yedpayLogger;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        YedpayLogger $yedpayLogger
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->yedpayLogger = $yedpayLogger;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }
}
