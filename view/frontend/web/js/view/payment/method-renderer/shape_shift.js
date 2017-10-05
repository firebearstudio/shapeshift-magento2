/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'jquery',
        'jquery/ui'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Firebear_ShapeShift/payment/form',
                transactionResult: '',
                returnAddress: ''
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'transactionResult', 'returnAddress'
                    ]);
                return this;
            },

            getCode: function() {
                return 'shape_shift';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transaction_result': this.transactionResult(),
                        'return_address': this.returnAddress()
                    }
                };
            },
            afterPlaceOrder: function () {
                jQuery.ajax( {
                    url: url.build('shapeshift/api/index'),
                    type: 'POST',
                    dataType: "json",
                    data: {"returnAddress": this.returnAddress()}
                });
            },

            getTransactionResults: function() {
                return _.map(window.checkoutConfig.payment.shape_shift.transactionResults, function(value, key) {
                    return {
                        'value': key,
                        'transaction_result': value
                    }
                });
            }
        });
    }
);