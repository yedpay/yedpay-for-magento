<?php
/* File: app/code/Atwix/OrderFlow/Setup/InstallData.php */
namespace Yedpay\YedpayMagento\Setup;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

/**
 * Install Yedpay Status and State
 */
class InstallData implements InstallDataInterface
{
    const ORDER_STATUS_YEDPAY_CONFIRMED_CODE = 'yedpay_confirmed';
    const ORDER_STATUS_YEDPAY_CONFIRMED_LABEL = 'Payment Confirmed (Yedpay)';

    const ORDER_STATUS_YEDPAY_REFUNDED_CODE = 'yedpay_refunded';
    const ORDER_STATUS_YEDPAY_REFUNDED_LABEL = 'Payment Refunded (Yedpay)';

    const ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_CODE = 'yedpay_partial_refunded';
    const ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_LABEL = 'Payment Partially Refunded (Yedpay)';

    protected $statusFactory;
    protected $statusResourceFactory;

    /**
     * InstallData constructor
     *
     * @param StatusFactory $statusFactory
     * @param StatusResourceFactory $statusResourceFactory
     */
    public function __construct(
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     *
     * @throws Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $data[] = [
            'status' => self::ORDER_STATUS_YEDPAY_CONFIRMED_CODE,
            'label' => self::ORDER_STATUS_YEDPAY_CONFIRMED_LABEL
        ];

        $data[] =[
            'status' => self::ORDER_STATUS_YEDPAY_REFUNDED_CODE,
            'label' => self::ORDER_STATUS_YEDPAY_REFUNDED_LABEL
        ];

        $data[] = [
            'status' => self::ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_CODE,
            'label' => self::ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_LABEL
        ];

        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);

        $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status_state'),
            ['status', 'state', 'is_default', 'visible_on_front'],
            [
                [self::ORDER_STATUS_YEDPAY_CONFIRMED_CODE, Order::STATE_PROCESSING, '0', '1'],
                [self::ORDER_STATUS_YEDPAY_REFUNDED_CODE, Order::STATE_CLOSED, '0', '1'],
                [self::ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_CODE, Order::STATE_PROCESSING, '0', '1']
            ]
        );
        $setup->endSetup();
    }
}
