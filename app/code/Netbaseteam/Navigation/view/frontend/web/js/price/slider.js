define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'jquery/ui',
    'Netbaseteam_Navigation/js/layer'
], function($, ultil) {
    "use strict";

    $.widget('cmsmart.layerSlider', $.cmsmart.layer, {
        options: {
            sliderElement: '#cmsmart_nav_price_slider',
            minInput: '#slider_min',
            maxInput: '#slider_max'
        },
        _create: function () {
            var self = this;
            $(this.options.sliderElement).slider({
                min: self.options.minValue,
                max: self.options.maxValue,
                values: [self.options.selectedFrom, self.options.selectedTo],
                slide: function( event, ui ) {
                    self.displayText(ui.values[0], ui.values[1]);
                },
                change: function(event, ui) {
                    self.ajaxSubmit(self.getUrl(ui.values[0], ui.values[1]));
                }
            });
            $(this.options.minInput).change(function(event, ui) {
                var $minerror = $("#filter-by-Price .filter-options-content #min-error");
                if ($(this).val() < self.options.minValue) {
                    $('#slider_min').css('border-color', 'red');
                    if($minerror.length < 1){
                        $( "#filter-by-Price .filter-options-content" ).append( "<span id='min-error' style='color: red'>Please enter a valid price range</span>" );
                    }
                    $('#max-error').css('display', 'none');
                }else {
                    $('#cmsmart_nav_price_slider').slider('values',0,$(this).val());
                    self.ajaxSubmit(self.getUrl(ui.values[0], ui.values[1]));
                }
            });
            $(this.options.maxInput).change(function(event, ui) {
                var $maxerror = $("#filter-by-Price .filter-options-content #max-error");
                if ($(this).val() > self.options.maxValue) {
                    $('#slider_max').css('border-color', 'red'); 
                    alert($maxerror.length);
                    if($maxerror.length < 1){
                        $( "#filter-by-Price .filter-options-content" ).append( "<span id='max-error' style='color: red'>Please enter a valid price range</span>" );
                    } 
                    $('#min-error').css('display', 'none');
                }else {
                    $('#cmsmart_nav_price_slider').slider('values',1,$(this).val());
                    self.ajaxSubmit(self.getUrl(ui.values[0], ui.values[1]));
                }
            });

            this.displayText(this.options.selectedFrom, this.options.selectedTo);
        },

        getUrl: function(from, to){
            return this.options.ajaxUrl.replace(encodeURI('{price_start}'), from).replace(encodeURI('{price_end}'), to);
        },

        displayText: function(from, to){
            $(this.options.minInput).val(from);
            $(this.options.maxInput).val(to);
        },

        formatPrice: function(value) {
            return ultil.formatPrice(value, this.options.priceFormat);
        }
    });

    return $.cmsmart.layerSlider;
});
