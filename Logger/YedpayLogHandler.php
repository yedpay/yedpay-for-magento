<?php
namespace Yedpay\YedpayMagento\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class YedpayLogHandler extends Base
{
    protected $fileName = '/var/log/yedpay.log';
    protected $loggerType = Logger::INFO;
}
