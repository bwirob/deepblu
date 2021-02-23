define(['jquery', 'uiComponent', 'ko', 'mage/storage', 'Magento_Catalog/js/price-utils', 'Netbaseteam_Opc/js/view/summary/gift-wrap', 'Netbaseteam_Opc/js/action/reload-shipping-method', 'Magento_Checkout/js/action/get-payment-information', 'Netbaseteam_Opc/js/model/gift-wrap'], function ($, Component, ko, storage, priceUtils, giftWrap, reloadShippingMethod, getPaymentInformation, giftWrapModel) {
    'use strict';
    var isGiftWrap = window.checkoutConfig.enable_giftwrap;
    if(isGiftWrap == 0) {
        isGiftWrap = null;
    }
    return Component.extend({
        initialize: function () {
            this._super();
            var self = this;
            //this.giftWrapAmountPrice = ko.computed(function () {
            //    var priceFormat = window.checkoutConfig.priceFormat;
            //    return priceUtils.formatPrice(self.giftWrapValue(), priceFormat)
            //});
        },

        isGiftWrap: ko.observable(isGiftWrap),

        giftWrapValue: ko.computed(function () {
            return giftWrapModel.getGiftWrapAmount();
        }),

        defaults: {template: 'Netbaseteam_Opc/gift-wrap'}, formatPrice: function (amount) {
            amount = parseFloat(amount);
            var priceFormat = window.checkoutConfig.priceFormat;
            return priceUtils.formatPrice(amount, priceFormat)
        },
        giftWrapAmountPrice: function() {
            var priceFormat = window.checkoutConfig.priceFormat;
            return priceUtils.formatPrice(this.giftWrapValue(), priceFormat)
        },
        setGiftWrapValue: function (amount) {
            this.giftWrapValue(amount);
        }, showOverlay: function () {
            $('.update-loader').show();
            $('#control_overlay_shipping').show();
            $('#control_overlay_payment').show();
        },
        hideOverlay: function () {
            $('.update-loader').hide();
            $('#control_overlay_shipping').hide();
            $('#control_overlay_payment').hide();
        }, addGiftWrap: function () {
            var params = {isChecked: !this.isChecked()};
            var self = this;
            this.showOverlay();
            $.ajax({
                url: giftwrap_url,
                type: 'POST',
                data: params,
                dataType: 'json'
            }).done(function (result) {
                if (self.isChecked()) {
                    $('tr.totals.discount.cmsmart-wrapper-gift-wrap').css('display','table-row');
                } else {
                    $('tr.totals.discount.cmsmart-wrapper-gift-wrap').css('display','none');
                }
                window.checkoutConfig.giftwrap_amount_final = result;
                reloadShippingMethod();
                self.showOverlay();
                getPaymentInformation().done(function () {
                    if (self.isChecked()) {
                        giftWrapModel.setGiftWrapAmount(result);
                        giftWrapModel.setIsWrap(true);
                    } else {
                        giftWrapModel.setIsWrap(false);
                    }
                    self.hideOverlay();
                });
            }).fail(function (result) {
            }).always(function (result) {
                self.hideOverlay();
            });
            return true;
        }, isChecked: ko.observable(window.checkoutConfig.has_giftwrap)
    });
});