define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'yedpay',
                component: 'Yedpay_YedpayMagento/js/view/payment/method-renderer/yedpay-renderer'
            }
        );
        return Component.extend({});
    }
);