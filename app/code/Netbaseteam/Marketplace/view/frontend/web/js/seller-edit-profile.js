define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    $.widget('mage.sellerEditProfile', {
        options: {
            sellerShopLogo: '.netbaseteam-logo',
            sellerShopBanner: '.netbaseteam-banner',
            setimageSelector: '.setimage'
        },
        _create: function () {
            var self = this;
            this.initObserve();
            this.imagePreview();

            $('input#shop_id').change(function () {
                var len = $('input#shop_id').val();
                var len2 = len.length;
                if (len2 === 0) {
                    alert({
                        content: self.options.errorMessageSku
                    });
                    $('div#shopidavail').css('display', 'none');
                    $('div#shopidnotavail').css('display', 'none');
                } else {
                    self.callVerifyShopIdAjaxFunction();
                }
            });
        },

        callVerifyShopIdAjaxFunction: function () {
            var self = this;
            $.ajax({
                url: self.options.checkShopIdAjaxUrl,
                type: "POST",
                data: {shop_id: $('input#shop_id').val()},
                dataType: 'html',
                success: function ($data) {
                    $data = JSON.parse($data);
                    if ($data.avialability == 1) {
                        $('div#shopidavail').css('display', 'block');
                        $('div#shopidnotavail').css('display', 'none');
                        $('div#shopidnotavail').css('color', 'red');
                    } else {
                        $('div#shopidnotavail').css('display', 'block');
                        $('div#shopidavail').css('display', 'none');
                        $("input#shop_id").attr('value', '');
                    }
                },
                error: function (response) {
                    alert({
                        content: self.options.ajaxErrorMessage
                    });
                }
            });
        },

        imagePreview: function () {
            var self = this;
            $("#shop-banner").change(function () {
                self.readURL1(this);
                $('.netbaseteam-banner').show();
            });
            $("#shop-logo").change(function () {
                self.readURL2(this);
                $('.netbaseteam-logo').show();
            });
        },

        readURL1: function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.netbaseteam-banner').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        },

        readURL2: function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('.netbaseteam-logo').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        },

        initObserve: function () {

        }

    });
    return $.mage.sellerEditProfile;
});
