/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'jquery',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/redirect-on-success'
    ],
    function (ko, Component, quote, $, placeOrderAction, selectPaymentMethodAction, customer, checkoutData, additionalValidators, url, fullScreenLoader, redirectOnSuccessAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Firebear_ShapeShift/payment/form',
                currencyCode: '',
                returnAddress: '',
                depositAddress: '',
                newErrorMessage: ko.observable(false)
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'currencyCode', 'returnAddress'
                    ]);
                return this;
            },

            getCode: function () {
                return 'shape_shift';
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'currency_code': this.currencyCode(),
                        'return_address': this.returnAddress()
                    }
                };
            },
            afterPlaceOrder: function () {
                console.log(this.depositAddress);
            },
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }
                jQuery.ajax({
                    url: url.build('shapeshift/api/index'),
                    type: 'POST',
                    dataType: "json",
                    showLoader: true,
                    data: {"returnAddress": this.returnAddress(), "currencyCode": this.currencyCode()},
                    success: function (data) {
                        self.depositAddress = data;
                        if (self.validate() && additionalValidators.validate()) {
                            self.isPlaceOrderActionAllowed(false);

                            self.getPlaceOrderDeferredObject()
                                .fail(
                                    function () {
                                        self.isPlaceOrderActionAllowed(true);
                                    }
                                ).done(
                                function () {
                                    self.afterPlaceOrder();

                                    if (self.redirectAfterPlaceOrder) {
                                        redirectOnSuccessAction.execute();
                                    }
                                }
                            );

                            return true;
                        }
                    },
                    error: function(data){
                        self.isPlaceOrderActionAllowed(true);
                        self.newErrorMessage(data.responseText);
                    }
                });

                return false;

            },
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                );
            },

            getAvailableCurrency: function () {
                console.log(window.checkoutConfig.payment.shape_shift.currencyCode);
                return _.map(window.checkoutConfig.payment.shape_shift.currencyCode, function (value, key) {
                    return {
                        'value': key,
                        'currency_code': value
                    }
                });
            }
        });
    }
);