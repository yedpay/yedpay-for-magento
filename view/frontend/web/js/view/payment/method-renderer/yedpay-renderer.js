/*browser:true*/
/*global define*/
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url'
], function (
    $,
    Component,
    quote,
    fullScreenLoader,
    url
) {
    'use strict';

    return Component.extend({
        defaults: {
            redirectAfterPlaceOrder: false,
            template: 'Yedpay_YedpayMagento/payment/yedpay',
            controllerUrl: url.build('yedpay/payment/getpaymenturl'),
            checkoutUrl: '',
            customId: ''
        },

        /**
         * Get payment name
         *
         * @returns {String}
         */
        getCode: function () {
            return 'yedpay';
        },

        getData: function () {
            var data = {
                'method': this.getCode(),
                'additional_data': {
                    'custom_id': this.customId,
                    'payment_url': this.checkoutUrl
                }
            };

            return data;
        },

        getPaymentUrl: function () {
            var self = this;
            var paymentAmount = quote.getTotals()().grand_total;
            var currency = quote.getTotals()().base_currency_code;

            $.ajax({
                url: this.controllerUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    amount: paymentAmount,
                    currency: currency
                },
                success: function (response) {
                    self.checkoutUrl = response.checkout_url;
                    self.customId = response.custom_id;
                    
                    self.placeOrder();
                },
                error: function (response) {
                    alert(response.responseJSON.message);
                }
            });
        },

        afterPlaceOrder: function() {
            $.mage.redirect(this.checkoutUrl);
            return false;
        },

        getYedpayLogoSrc: function () {
            return window.checkoutConfig.payment.yedpay.logoSrc;
        },

        getYedpayDescription: function () {
            return window.checkoutConfig.payment.yedpay.description;
        }

    });
    
});
