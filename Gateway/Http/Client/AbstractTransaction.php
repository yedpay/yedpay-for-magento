<?php

namespace Yedpay\YedpayMagento\Gateway\Http\Client;

use Yedpay\YedpayMagento\Gateway\Config\Config;
use Magento\Framework\App\Action\Context;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

abstract class AbstractTransaction implements ClientInterface
{
    protected $context;
    protected $logger;
    protected $customLogger;
    protected $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param Config $config
     */
    public function __construct(Context $context, LoggerInterface $logger, Logger $customLogger, Config $config)
    {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->config = $config;
    }
    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        // $data = $transferObject->getBody();
        // $log = [
        //     'request' => $data,
        //     'client' => static::class
        // ];
        // $response['object'] = [];
        // try {
        //     $response['object'] = $this->process($data);
        // } catch (\Exception $e) {
        //     $message = __($e->getMessage() ?: 'Sorry, but something went wrong');
        //     $this->logger->critical($message);
        //     throw new ClientException($message);
        // } finally {
        //     $log['response'] = (array) $response['object'];
        //     $this->customLogger->debug($log);
        // }
        // return $response;
        return $this->process($transferObject);
    }
    
    /**
     * Process http request
     * @param array $data
     * @return
     */
    abstract protected function process(TransferInterface $transferObject);
}
