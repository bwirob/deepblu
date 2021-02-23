define([
    "jquery",
    'mage/translate',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    "jquery/ui"
], function ($, $t, mageTemplate, alert) {
    'use strict';
    $.widget('mage.crosssellProduct', {
        options: {
            backUrl: ''
        },
        _create: function () {
            var self = this;
            var indexValue = 0;
            var crosssellProductData = $.parseJSON(self.options.crosssellProducts);
            if ($.isArray(crosssellProductData)) {
                $(document).ajaxComplete(function( event, request, settings ) {
                    var responseData = $.parseJSON(request.responseText);
                    var currentAjaxUrl = settings.url;
                    if (currentAjaxUrl.indexOf("marketplace_crosssell_product_listing") && responseData.totalRecords>0) {
                        setTimeout(function() {
                            if ($('#crosssell-product-block-wrapper .data-row').length) {
                                crosssellProductData.each(function (index, value) {
                                    var indexId = index;
                                    $("#crosssellIdscheck"+indexId).trigger("click");
                                    crosssellProductData = $.grep(crosssellProductData, function(arrValue) {
                                        return indexId !== arrValue;
                                    });
                                });
                                $("#crosssell-product-block-loader").hide();
                            } else {
                                setTimeout(function() {
                                    if ($('#crosssell-product-block-wrapper .data-row').length) {
                                        crosssellProductData.each(function (index, value) {
                                            var indexId = index;
                                            $("#crosssellIdscheck"+indexId).trigger("click");
                                        });
                                        $("#crosssell-product-block-loader").hide();
                                    } else {
                                        $("#crosssell-product-block-loader").hide();
                                    }
                                }, 2000);
                            }
                        }, 2000);
                    } else {
                        $("#crosssell-product-block-loader").hide();
                    }
                });
            }
            $(this.element).delegate(self.options.gridCheckbox, 'change', function(){
                var productId = $(this).val();
                var parentDivId = $(this).parents('div.admin__data-grid-wrap').parents('div').parents('div').attr('id');
                if (parentDivId == 'crosssell-product-block-wrapper') {
                    if($(this).is(":checked")) {
                        if (productId == 'on') {
                            $('#crosssell-product-block-wrapper .data-row').each(function () {
                                var trElement = $(this);
                                var progressTmpl = mageTemplate(self.options.templateId),
                                    tmpl;
                                tmpl = progressTmpl({
                                    data: {
                                        index: indexValue,
                                        id: trElement.find('.netbaseteam-marketplace-grid-id-cell').find('div').text(),
                                        name: trElement.find('.netbaseteam-marketplace-grid-name-cell').find('div').text(),
                                        status: trElement.find('.netbaseteam-marketplace-grid-status-cell').find('div').text(),
                                        attribute_set: trElement.find('.netbaseteam-marketplace-grid-attributeset-cell').find('div').text(),
                                        sku: trElement.find('.netbaseteam-marketplace-grid-sku-cell').find('div').text(),
                                        price: trElement.find('.netbaseteam-marketplace-grid-price-cell').find('div').text(),
                                        thumbnail: trElement.find('.data-grid-thumbnail-cell').find('img').attr('src'),
                                        position: indexValue+1,
                                        record_id: trElement.find('.netbaseteam-marketplace-grid-id-cell').find('div').text()
                                    }
                                });
                                indexValue++;
                                $(self.options.crosssellProductId).after(tmpl);
                            });
                        } else {
                            var trElement = $(this).parents('tr');
                            var progressTmpl = mageTemplate(self.options.templateId),
                                tmpl;
                            tmpl = progressTmpl({
                                data: {
                                    index: indexValue,
                                    id: trElement.find('.netbaseteam-marketplace-grid-id-cell').find('div').text(),
                                    name: trElement.find('.netbaseteam-marketplace-grid-name-cell').find('div').text(),
                                    status: trElement.find('.netbaseteam-marketplace-grid-status-cell').find('div').text(),
                                    attribute_set: trElement.find('.netbaseteam-marketplace-grid-attributeset-cell').find('div').text(),
                                    sku: trElement.find('.netbaseteam-marketplace-grid-sku-cell').find('div').text(),
                                    price: trElement.find('.netbaseteam-marketplace-grid-price-cell').find('div').text(),
                                    thumbnail: trElement.find('.data-grid-thumbnail-cell').find('img').attr('src'),
                                    position: indexValue+1,
                                    record_id: trElement.find('.netbaseteam-marketplace-grid-id-cell').find('div').text()
                                }
                            });
                            indexValue++;
                            $(self.options.crosssellProductId).after(tmpl);
                        }
                    } else {
                        $('#crosssell-product-record'+productId).remove();
                    }
                }
            });
        }
    });
    return $.mage.crosssellProduct;
});
