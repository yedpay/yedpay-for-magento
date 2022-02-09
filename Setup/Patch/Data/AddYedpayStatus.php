<?php

namespace Yedpay\YedpayMagento\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order;

class AddYedpayStatus implements DataPatchInterface
{
    const ORDER_STATUS_YEDPAY_CONFIRMED_CODE = 'yedpay_confirmed';
    const ORDER_STATUS_YEDPAY_CONFIRMED_LABEL = 'Payment Confirmed (Yedpay)';

    const ORDER_STATUS_YEDPAY_REFUNDED_CODE = 'yedpay_refunded';
    const ORDER_STATUS_YEDPAY_REFUNDED_LABEL = 'Payment Refunded (Yedpay)';

    const ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_CODE = 'yedpay_partial_refunded';
    const ORDER_STATUS_YEDPAY_PARTIAL_REFUNDED_LABEL = 'Payment Partially Refunded (Yedpay)';

    private $moduleDataSetup;

    public function __construct(
       ModuleDataSetupInterface $moduleDataSetup

     ) {

        $this->moduleDataSetup = $moduleDataSetup;

    }
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $setup = $this->moduleDataSetup;

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

         $setup->getConnection()->insertArray(
            $setup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );

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
    public function getAliases()
    {
        return [];
    }
    public static function getDependencies()
    {
        return [];
    }
}
