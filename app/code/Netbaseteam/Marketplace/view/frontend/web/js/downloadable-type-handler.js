/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true expr:true*/
define([
    'jquery'
], function ($) {
    'use strict';

    return {
        $checkbox: $('[data-action=change-type-product-downloadable]'),
        $items: $('#product_info_tabs_downloadable_items'),
        $tab: null,

        /**
         * Show
         */
        show: function () {
            this.$checkbox.prop('checked', true);
            this.$items.show();
        },

        /**
         * Hide
         */
        hide: function () {
            this.$checkbox.prop('checked', false);
            this.$items.hide();
        },

        /**
         * Constructor component
         * @param {Object} data - this backend data
         */
        'Netbaseteam_Marketplace/js/downloadable-type-handler': function (data) {
            this.$tab = $('[data-tab=' + data.tabId + ']');
            this.isDownloadable = data.isDownloadable;
            this._initType();
        },

        /**
         * Bind all
         */
        bindAll: function () {
            this.$checkbox.on('change', function (event) {
                $(document).trigger('setTypeProduct', $(event.target).prop('checked') ? 'downloadable' : null);
            });
        },

        /**
         * Init type
         * @private
         */
        _initType: function () {
            if (this.isDownloadable === 'true') {
                this.show();
            } else {
                this.hide();
            }
            this.$checkbox.on('change', function (event) {
                if ($(event.target).prop('checked')) {
                    $('#product_info_tabs_downloadable_items').show();
                } else {
                    $('#product_info_tabs_downloadable_items').hide();
                }
            });

        }
    };
});
