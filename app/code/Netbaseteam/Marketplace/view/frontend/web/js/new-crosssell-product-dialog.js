define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
], function ($) {
    'use strict';

    /**
     */
    $.widget('mage.newCrosssellProductDialog', {

        /**
         * Build widget
         * @private
         */
        _create: function () {
            var widget = this;

            this.element.modal({
                type: 'slide',
                modalClass: 'mage-crosssell-product-list-dialog form-inline',
                title: $.mage.__('Products'),
                buttons: [
                    {
                        text: $.mage.__('Select Product'),
                        class: 'action-primary add-crosssell-product-button',
                        click: $.proxy(widget._onSelect, widget)
                    },
                    {
                        text: $.mage.__('Cancel'),
                        class: 'crosssell-product-cancel-button',
                        click: $.proxy(widget._onCancel, widget)
                    }
                ],

                /**
                 * @returns {null}
                 */
                opened: function () {
                    var modalTitleElement;

                    modalTitleElement = $('.mage-crosssell-product-list-dialog .modal-title');
                    modalTitleElement.text($.mage.__('Add Cross-Sell Products'));
                    
                    return null;

                }
            });

        },
        
        /**
         * Fired when click on select
         * @private
         */
        _onSelect: function () {
            var widget = this;
            var arr = $('#crosssell-to-product-list input[data-action="select-row"]:checked').map(function(){
                return this.value;
            }).get();
            var position = $('input[name="crosssell_position"]').last().val();
            var getUrl = window.location;
            var submitUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1] + "/marketplace/crosssellproduct/selected";
            $.ajax({
                url: submitUrl,
                data: {data: arr,position: position},
                type: 'post',
                showLoader: true,
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (res) {
                    if (res.content) {
                        if($('#empty_crosssell_product').length) $('#empty_crosssell_product').remove();
                        $(".crosssell_products tbody").append(res.content);
                        $(".crosssell_products").show();
                        $('.crosssell_products tbody').sortable({
                            placeholder: 'sort-placeholder',
                            forcePlaceholderSize: true,
                            start: function(e, ui) {
                                ui.item.data('start-pos', ui.item.index() + 1);
                            },
                            change: function(e, ui) {
                                var seq, startPos = ui.item.data('start-pos'),
                                    $index, correction;

                                correction = startPos <= ui.placeholder.index() ? 0 : 1;

                                ui.item.parent().find('tr.data-row').each(function(idx, el) {
                                    var $this = $(el),
                                        $index = $this.index();

                                    if (($index + 1 >= startPos && correction === 0) || ($index + 1 <= startPos && correction === 1)) {
                                        $index = $index + correction;
                                        $this.find('input[name="crosssell_position"]').val($index - 1);
                                    }

                                });

                                seq = ui.item.parent().find('tr.sort-placeholder').index() + correction;
                                ui.item.find('input[name="crosssell_position"]').val(seq - 1);
                            }
                        });

                        widget.close();
                    }
                },
                error: function () {
                    window.location.reload();
                }
            });
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

    return $.mage.newCrosssellProductDialog;
});
