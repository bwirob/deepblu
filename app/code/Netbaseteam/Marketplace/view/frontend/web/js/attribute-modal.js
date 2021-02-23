define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    $.widget('mage.attributeModal', {

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
            $('.gallery.ui-sortable').on('openDialog', $.proxy(this._onOpenDialog, this));
        },

        /**
         * Open dialog for external video
         * @private
         */
        _onOpenDialog: function (e, imageData) {

            if (imageData['media_type'] !== 'external-video') {
                return;
            }
            this.showModal();
        },

        /**
         * Fired on trigger "openModal"
         */
        showModal: function () {

            $('#new-attribute').modal('openModal');
        }
    });

    return $.mage.attributeModal;
});
