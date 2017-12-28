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
                template       : 'Firebear_ShapeShift/payment/form',
                currencyCode   : '',
                returnAddress  : '',
                deposit        : '',
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

            getData                    : function () {
                return {
                    'method'         : this.item.method,
                    'additional_data': {
                        'currency_code' : this.currencyCode(),
                        'return_address': this.returnAddress()
                    }
                };
            },
            afterPlaceOrder            : function () {
                jQuery.ajax({
                    url       : url.build('shapeshift/api/saveTransaction'),
                    type      : 'POST',
                    dataType  : 'json',
                    showLoader: true,
                    data      : {"depoAmount": this.deposit.amount, "depoAddress": this.deposit.address}
                });
                /*window.location.replace(url.build('shapeshift/page/success/'));*/
            },
            placeOrder                 : function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }
                if (this.currencyCode()) {
                    jQuery.ajax({
                        url       : url.build('shapeshift/api/index'),
                        type      : 'POST',
                        dataType  : "json",
                        showLoader: true,
                        data      : {"returnAddress": this.returnAddress(), "currencyCode": this.currencyCode()},
                        success   : function (data) {
                            self.deposit = data;
                            if (self.deposit.error) {
                                self.isPlaceOrderActionAllowed(true);
                                self.newErrorMessage('Message: ' + self.deposit.error + ' Request: ' + self.deposit.url);
                            }
                            else {
                                if (self.validate() && additionalValidators.validate()) {
                                    self.isPlaceOrderActionAllowed(false);
                                    self.getPlaceOrderDeferredObject()
                                        .fail(
                                            function () {
                                                self.isPlaceOrderActionAllowed(true);
                                            }
                                        ).done(function () {
                                        self.afterPlaceOrder();

                                        if (self.redirectAfterPlaceOrder) {
                                            redirectOnSuccessAction.execute();
                                        }
                                    });

                                    return true;
                                }
                            }
                        }
                    });
                }
                else 
                {
                    self.isPlaceOrderActionAllowed(true);
                    self.newErrorMessage('Message: Please select Currency code');
                }

                return false;

            },
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                );
            },

            getAvailableCurrency: function () {
                return _.map(window.checkoutConfig.payment.shape_shift.currencyCode, function (value, key) {
                    return {
                        'value'        : key,
                        'currency_code': value
                    }
                });
            },

            getPaymentDescription: function () {
                return window.checkoutConfig.payment.shape_shift.paymentDescription;
            }
        });
    }
);