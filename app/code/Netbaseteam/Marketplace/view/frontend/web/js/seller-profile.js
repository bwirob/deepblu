define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    $.widget('mage.sellerProfile', {
        options: {
            sellerProfileContent: '#seller-profile-content'
        },
        _create: function () {
            var self = this;
            this.initObserve();
        },
        initObserve: function () {
            var self = this;

            $('.sidebar_left a').bind('click', function (e) {
                var submitUrl = $(this).attr('href');
                self.ajaxSubmit(submitUrl);

                e.stopPropagation();
                e.preventDefault();
            });
            $('#mp-profile-view').bind('click', function (e) {
                var submitUrl = $(this).attr('href');
                self.ajaxSubmit(submitUrl);

                e.stopPropagation();
                e.preventDefault();
            });

            $(window).on('popstate', function (e) {
                location.reload();
            });
        },

        ajaxSubmit: function (submitUrl) {
            var self = this;

            $.ajax({
                url: submitUrl,
                data: {isAjax: 1},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    $('.loading_overlay').show();
                    if (typeof window.history.pushState === 'function') {
                        history.pushState({}, '', submitUrl);
                    }
                },
                success: function (res) {
                    if (res.content) {
                        $(self.options.sellerProfileContent).replaceWith(res.content);
                        $(self.options.sellerProfileContent).trigger('contentUpdated');
                    }
                    $('.loading_overlay').hide();
                },
                error: function () {
                    window.location.reload();
                }
            });
        }
    });
    return $.mage.sellerProfile;
});
