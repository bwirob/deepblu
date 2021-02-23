define(['ko', 'Magento_Catalog/js/price-utils'], function (ko, priceUtils) {
    'use strict';
    var giftWrapAmountFinal = ko.observable(window.checkoutConfig.giftwrap_amount_final);
    var hasWrap = ko.observable(window.checkoutConfig.has_giftwrap);
    return {
        giftWrapAmount: giftWrapAmountFinal, hasWrap: hasWrap, getGiftWrapAmount: function () {
            return this.giftWrapAmount();
        }, getIsWrap: function () {
            return this.hasWrap();
        }, setGiftWrapAmount: function (amount) {
            this.giftWrapAmount(amount);
        }, setIsWrap: function (isWrap) {
            return this.hasWrap(isWrap);
        }
    };
});