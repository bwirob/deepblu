define([
    "jquery",
    'mage/translate',
    "jquery/ui",
    'mage/calendar'
], function ($, $t) {
    'use strict';
    $.widget('mage.vacationInfo', {
        options: {
            errorMessageSku: $t("SKU can\'t be left empty"),
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
                minDate: new Date()
            });

            $('#date_from').change(function(){
                $('#date_to').datepicker('destroy');
                $('#date_to').datepicker({
                    minDate: $('#date_from').val()
                });
            });

            $('#date_to').change(function(){
                $('#date_from').datepicker('destroy');
                $('#date_from').datepicker({
                    maxDate: $('#date_to').val()
                });
            });

            if ($("#disable_type").val() == "add_to_cart_disable") {
                $("#add_to_cart_label").show();
            }

            $("#disable_type").change(function () {
                if ($(this).val() == "add_to_cart_disable") {
                    $("#add_to_cart_label").show();
                } else {
                    $("#add_to_cart_label").hide();
                }
            });
        }

    });
    return $.mage.vacationInfo;
});
