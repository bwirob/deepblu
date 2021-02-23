/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/sidebar'
    ],
    function(ko, $, Component, quote, sidebarModel) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Netbaseteam_Opc/place-order-button'
            },

            isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null),

            initialize: function () {
                this._super();

                if(this.isVisible()) {
                    this.injectStyles('.payment-method-content .actions-toolbar button.action.checkout{display: none;}');
                    quote.billingAddress.subscribe(function(address) {
                        this.isPlaceOrderActionAllowed((address !== null));
                    }, this);
                }

                return this;
            },

            isVisible: function() {
                return true;
            },

            placeOrder: function() {
                if(this.getCode() === null) {
                    alert('Select a payment method');
                    return false;
                }

                $('.payment-method #' + this.getCode())
                    .parents('.payment-method')
                    .find('.payment-method-content .actions-toolbar button.action.checkout')
                    .trigger('click');
            },

            getCode: function () {
                return quote.paymentMethod() ? quote.paymentMethod().method : null;
            },

            injectStyles: function(rule) {
                var div = $("<div />", {
                    html: '&shy;<style>' + rule + '</style>'
                }).appendTo("body");
            }
        });
    }
);