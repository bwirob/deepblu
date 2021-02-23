define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    $.widget('mage.openUpsellProductDialog', {

        /**
         * * Fired when widget initialization start
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            $(this.element).on('click', this.showModal.bind(this));
        },

        /**
         * Open dialog for external video
         * @private
         */
        _onOpenDialog: function (e, imageData) {
            this.showModal();
        },

        /**
         * Fired on trigger "openModal"
         */
        showModal: function () {

            $('#upsell-to-product-list').modal('openModal');
        }
    });

    return $.mage.openUpsellProductDialog;
});
