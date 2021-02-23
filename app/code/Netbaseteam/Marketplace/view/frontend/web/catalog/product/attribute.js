define([
    "jquery",
    "jquery/ui",
    "mage/translate"
], function ($) {
    'use strict';
    $.widget('mage.productAttribute', {
        _create: function () {
            var self = this;
            
            $("#frontend_input").change(function () {
                if ($(this).val() == "text") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();   
                    $('#mp-default-value-textarea').children().remove();
                    $('#mp-default-value-date').children().remove();                 
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();

                    $('#mp-default-value-text').show();
                    $('#mp-default-value-text').append('<label>Default Value : </label>');
                    $('#mp-default-value-text').append('<input name="default_value_text"/>');
                }
                if ($(this).val() == "textarea") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();
                    $('#mp-default-value-textarea').children().remove();
                    $('#mp-default-value-date').children().remove();
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();

                    $('#mp-default-value-textarea').show();
                    $('#mp-default-value-textarea').append('<label>Default Value : </label>');
                    $('#mp-default-value-textarea').append('<textarea name="default_value_texteara"/>');
                }
                if ($(this).val() == "date") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();
                    $('#mp-default-value-textarea').children().remove();
                    $('#mp-default-value-date').children().remove();
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();

                    $('#mp-default-value-date').show();
                    $('#mp-default-value-date').append('<label>Default Value : </label>');
                    $('#mp-default-value-date').append('<input class="datepicker input-text" name="default_value_date"/>');
                    
                    $('.datepicker').datepicker({
                        prevText: '&#x3c;zurück', prevStatus: '',
                        prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
                        nextText: 'Vor&#x3e;', nextStatus: '',
                        nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
                        showMonthAfterYear: false
                    });
                }
                if ($(this).val() == "boolean") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();
                    $('#mp-default-value-textarea').children().remove();
                    $('#mp-default-value-date').children().remove();
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();

                    $('#mp-default-value-yesno').show();
                    $('#mp-default-value-yesno').append('<label>Default Value : </label>');
                    $('#mp-default-value-yesno').append('<select name="default_value_yesno" title="Default Value" class="control-select"><option value="1">Yes</option><option value="0" selected="selected">No</option></select>');
                    
                }
                if ($(this).val() == "multiselect") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();   
                    $('#mp-default-value-textarea').children().remove();
                    $('#mp-default-value-date').children().remove();                 
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();
                    
                    $('#mp-product-attribute-option').show();
                    $('#mp-product-attribute-option').append('<legend class="legend"><span>Manage Options (Values of Your Attribute)</span></legend><div id="manage-options-panel" data-index="attribute_options_select_container"><table class="form-table" id="attribute-table"><thead><tr id="attribute-options-table"><th scope="row"><label>Is Default</label></th><th scope="row"><label>Admin</label></th><th scope="row"><label>Default Store View</label></th></tr></thead><tbody></tbody></table></div>');

                    if ($('.addSelect').length > 0) {
                        $('.addSelect').parent().parent().parent().remove();
                    }

                    $("#attribute-table").append('<tfoot><tr><td><a href="javascript:void(0);" class="addMultiselect">Add</a></td></tr></tfoot>');
                    var i = 0;
                    $(".addMultiselect").click(function () {
                        $("#attribute-table").append('<tr><td><input type="checkbox" class="input-radio" name="default[]" value="option_' + i + '"/></td><td><input type="text" class="input-text required-option" name="option[value][option_' + i + '][0]" value /></td><td><input type="text" class="input-text" name="option[value][option_' + i + '][1]" value /></td><td><a href="javascript:void(0);" class="remCF">Remove</a></td></tr>');
                        i++;
                    });
                }
                if ($(this).val() == "select") {
                    $('tbody').remove();
                    $('#mp-default-value-text').children().remove();   
                    $('#mp-default-value-textarea').children().remove();                 
                    $('#mp-product-attribute-option').children().remove();
                    $('#mp-default-value-yesno').children().remove();

                    $('#mp-product-attribute-option').show();
                    $('#mp-product-attribute-option').append('<legend class="legend"><span>Manage Options (Values of Your Attribute)</span></legend><div id="manage-options-panel" data-index="attribute_options_select_container"><table class="form-table" id="attribute-table"><thead><tr id="attribute-options-table"><th scope="row"><label>Is Default</label></th><th scope="row"><label>Admin</label></th><th scope="row"><label>Default Store View</label></th></tr></thead><tbody></tbody></table></div>');
                    if ($('.addMultiselect').length > 0) {
                        $('.addMultiselect').parent().parent().parent().remove();
                    }

                    $("#attribute-table").append('<tfoot><tr><td><a href="javascript:void(0);" class="addSelect">Add</a></td></tr></tfoot>');
                    var j = 0;
                    $(".addSelect").click(function () {
                        $("#attribute-table").append('<tr><td><input type="radio" class="input-radio" name="default[]" value="option_' + j + '"/></td><td><input type="text" class="input-text required-option" name="option[value][option_' + j + '][0]" value /></td><td><input type="text" class="input-text" name="option[value][option_' + j + '][1]" value /></td><td><a href="javascript:void(0);" class="remCF">Remove</a></td></tr>');
                        j++;
                    });

                }
            });

            $("#attribute-table").on('click', '.remCF', function () {
                $(this).parent().parent().remove();
            });

        }
    });
    return $.mage.productAttribute;
});
