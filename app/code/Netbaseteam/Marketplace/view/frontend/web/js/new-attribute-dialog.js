define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
], function ($) {
    'use strict';

    /**
     */
    $.widget('mage.newAttributeDialog', {

        /**
         * Build widget
         * @private
         */
        _create: function () {
            var widget = this;

            this.element.modal({
                type: 'slide',
                modalClass: 'mage-new-attribute-dialog form-inline',
                title: $.mage.__('Select'),
                buttons: [
                    {
                        text: $.mage.__('Select Attribute'),
                        class: 'action-primary attribute-create-button',
                        click: $.proxy(widget._onSelect, widget)
                    },
                    {
                        text: $.mage.__('Cancel'),
                        class: 'attribute-cancel-button',
                        click: $.proxy(widget._onCancel, widget)
                    }
                ],

                /**
                 * @returns {null}
                 */
                opened: function () {
                    var modalTitleElement;

                    modalTitleElement = $('.mage-new-attribute-dialog .modal-title');
                    modalTitleElement.text($.mage.__('Attributes'));
                    
                    return null;

                }
            });

        },
        
        /**
         * Fired when click on create video
         * @private
         */
        _onSelect: function () {
            var arr = $('input[name="attribute_mass_select[]"]:checked').map(function(){
                return this.value;
            }).get();
            var getUrl = window.location;
            var submitUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/marketplace/product_attribute/update";
            var pid = $('input[name="product_id"]').val();
            $.ajax({
                url: submitUrl,
                data: {data: arr,pid:pid},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (res) {
                    if (res.content) {
                        $("#vendor-attribute-block").replaceWith(res.content);
                        $("#vendor-attribute-block").trigger('contentUpdated');
                    }
                },
                error: function () {
                    window.location.reload();
                }
            });
            this.element.trigger('closeModal');            
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

    return $.mage.newAttributeDialog;
});
