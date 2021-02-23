define([
    'jquery',
    'magnificPopup'
], function ($, magnificPopup) {
    "use strict";

    return {
        displayContent: function(prodUrl, updateCartUrl,qvTitle, qvBackground) {
            if (!prodUrl.length) {
                return false;
            }
            $.magnificPopup.open({
                items: {
                    src: prodUrl
                },
                type: 'iframe',
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">'+
                    '<div class="mfp-close"></div>'+
                    '<iframe class="mfp-iframe" frameborder="0" allowfullscreen>' +
                    '</iframe>'+ 
                    '</div>'
                },
                closeOnBgClick: true,
                preloader: true,
                tLoading: '',
                callbacks: {
                    open: function() {
                        $('.mfp-preloader').css('display', 'block'); 
                    },
                    beforeClose: function() {
                        $.ajax({
                            url: updateCartUrl,
                            method: "POST",
                            success: function(res) {
                                $('[data-block="minicart"]').trigger('contentLoading');
                            }
                        });
                    },
                    close: function() {
                        $('.mfp-preloader').css('display', 'none');
                    }
                }
            });
        }
    };

});