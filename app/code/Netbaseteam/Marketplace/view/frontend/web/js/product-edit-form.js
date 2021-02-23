define([
    "jquery",
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    "jquery/ui",
    'mage/calendar'
], function ($, $t, alert) {
    'use strict';
    $.widget('mage.productEditForm', {
        options: {
            errorMessageSku: $t("SKU can\'t be left empty"),
            errorMessagePrice: $t("Special Price can\'t be  higher than Base Price"),
            ajaxErrorMessage: $t('There was error during fetching results.')
        },
        _create: function () {
            var self = this;

            $('.datepicker').datepicker({
                    prevText: '&#x3c;zurück', prevStatus: '',
                    prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
                    nextText: 'Vor&#x3e;', nextStatus: '',
                    nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
                    showMonthAfterYear: false,
                    dateFormat: 'mm/dd/yy'
                }
            );

            $('#price').change(function(){
                if ($('#special_price').val() !== '' && $('#special_price').val() > $('#price').val() ) {
                    alert({
                        content: self.options.errorMessagePrice
                    });
                    $('#special_price').val('');
                }
            });

            $('#special_price').change(function(){
                if ($('#price').val() !== '' && Number($('#special_price')).val() > Number($('#price').val()) ) {
                    alert({
                        content: self.options.errorMessagePrice
                    });
                    $('#special_price').val('');
                }
            });

            $('#special_from_date').change(function(){
                $('#special_to_date').datepicker('destroy');
                $('#special_to_date').datepicker({
                    minDate: $('#special_from_date').val()
                });
            });

            $('#special_to_date').change(function(){
                $('#special_from_date').datepicker('destroy');
                $('#special_from_date').datepicker({
                    maxDate: $('#special_to_date').val()
                });
            });


            if ($('#check-weight option:selected').val() == 0) {
                $('input[name^="product[weight]"]').attr('disabled', 'disabled');
                $('input[name^="product[weight]"]').addClass('input-disabled');
                $('input[name^="is_downloadable"]').removeAttr('disabled', 'disabled');
                $('#downloadable-notification').hide();
            } else if ($('#check-weight option:selected').val() == 1) {
                $('input[name^="product[weight]"]').removeAttr('disabled', 'disabled');
                $('input[name^="product[weight]"]').removeClass('input-disabled');
            } else if ($('input[name^="product[weight_type]"]').length && $('input[name^="product[weight_type]"]').val() == 1) {
                $('input[name^="product[weight]"]').removeAttr('disabled', 'disabled');
            } else if ($('input[name^="product[weight_type]"]').length && $('input[name^="product[weight_type]"]').val() == 0) {
                $('input[name^="product[weight]"]').attr('disabled', true);
            }

            if ($('input[name^="is_downloadable"]').prop('checked')) {
                $('input[name^="product[weight]"]').attr('disabled', 'disabled');
                $('input[name^="product[weight]"]').addClass('input-disabled');
            }
            $('#check-weight').on('change', function () {
                if (this.value == '0') {
                    $('input[name^="product[weight]"]').attr('disabled', 'disabled');
                    $('input[name^="product[weight]"]').addClass('input-disabled');
                    $('input[name^="is_downloadable"]').removeAttr('disabled');
                    $('#downloadable-notification').hide();
                } else if (this.value == '1') {
                    $('input[name^="product[weight]"]').removeAttr('disabled', 'disabled');
                    $('input[name^="product[weight]"]').removeClass('input-disabled');
                    $('input[name^="is_downloadable"]').removeAttr('checked','checked');
                    $('#downloadable-notification').show();
                    $('#product_info_tabs_downloadable_items').hide();

                }
            });

            $('input#sku').change(function() {
                var len=$('input#sku').val();
                var len2=len.length;
                if (len2 === 0) {
                    alert({
                        content: self.options.errorMessageSku
                    });
                    $('div#skuavail').css('display','none');
                    $('div#skunotavail').css('display','none');
                } else {
                    self.callVerifySkuAjaxFunction();
                }
            });

            $('.switch-bundle input').change(function() {
                if ($(this).val() == '0') {
                    $(this).val(1);
                    if ($(this).attr('name') == 'product[weight_type]') {
                        $('input[name="product[weight]"]').removeAttr('disabled');
                    }

                    if ($(this).attr('name') == 'product[price_type]') {
                        $('input[name="product[price]"]').removeAttr('disabled');
                        $('select[name="product[tax_class_id]"]').removeAttr('disabled');
                    }

                } else {
                    $(this).val(0);
                    if ($(this).attr('name') == 'product[weight_type]') {
                        $('input[name="product[weight]"]').attr('disabled', true);
                    }

                    if ($(this).attr('name') == 'product[price_type]') {
                        $('input[name="product[price]"]').attr('disabled', true);
                        $('select[name="product[tax_class_id]"]').attr('disabled', true);
                    }
                }
            });
        },
        callVerifySkuAjaxFunction: function () {
            var self = this;
            $.ajax({
                url: self.options.checkSkuAjaxUrl,
                type: "POST",
                data: {sku:$('input#sku').val()},
                dataType: 'html',
                success:function($data){
                    $data=JSON.parse($data);
                    if ($data.avialability==1) {
                        $('div#skuavail').css('display','block');
                        $('div#skunotavail').css('display','none');
                        $('div#skunotavail').css('color','red');
                    } else {
                        $('div#skunotavail').css('display','block');
                        $('div#skuavail').css('display','none');
                        $("input#sku").attr('value','');
                    }
                },
                error: function (response) {
                    alert({
                        content: self.options.ajaxErrorMessage
                    });
                }
            });
        }
    });
    return $.mage.productEditForm;
});
