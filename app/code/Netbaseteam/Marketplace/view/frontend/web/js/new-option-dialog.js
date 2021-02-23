define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
], function ($) {
    'use strict';

    /**
     */
    $.widget('mage.newOptionDialog', {

        /**
         * Build widget
         * @private
         */
        _create: function () {
            var widget = this;

            this.element.modal({
                type: 'slide',
                modalClass: 'mage-new-attribute-dialog form-inline',
                title: $.mage.__('Import'),
                buttons: [
                    {
                        text: $.mage.__('Select Product'),
                        class: 'action-primary option-create-button',
                        click: $.proxy(widget._onSelect, widget)
                    },
                    {
                        text: $.mage.__('Cancel'),
                        class: 'option-cancel-button',
                        click: $.proxy(widget._onCancel, widget)
                    }
                ],

                /**
                 * @returns {null}
                 */
                opened: function () {
                    var modalTitleElement;

                    modalTitleElement = $('.mage-new-attribute-dialog .modal-title');
                    modalTitleElement.text($.mage.__('Import Options'));
                    
                    return null;

                }
            });

        },
        
        /**
         * Fired when click on create video
         * @private
         */
        _onSelect: function () {
            
        },
        
        /**
         * Fired when clicked on cancel
         * @private
         */
        _onCancel: function () {
            this.close();
        },

        /**
         * Close slideout dialog
         */
        close: function () {
            this.element.trigger('closeModal');
        }        

    });

    return $.mage.newOptionDialog;
});
