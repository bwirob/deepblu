define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Netbaseteam_Opc/js/model/gift-wrap'
], function ($, ko, Component, giftWrap) {
    return Component.extend({
        getPureValue: ko.observable(window.checkoutConfig.giftwrap_amount_final),
        initialize: function () {
            this._super();
            var self = this;
            this.isGiftWrapDisplay = ko.computed(function () {
                return (giftWrap.getIsWrap());
            });
        },
        defaults: {template: 'Netbaseteam_Opc/summary/gift-wrap'},
        getValue: function () {
            return this.getFormattedPrice(giftWrap.getGiftWrapAmount());
        }
    });
});