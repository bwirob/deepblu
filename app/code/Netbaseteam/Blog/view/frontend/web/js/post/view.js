define(['jquery','owlCarousel'], function($,owlCarousel){ 
"use strict";
$(document).ready(function(){
    $( '<em> *</em>').insertAfter("#form-content .captcha .label span");
    var items = jQuery('.realted-content ul.related-list li.item');
                
                jQuery('.realted-content ul.related-list').owlCarousel({
                    items: 2,
                    itemsCustom: [ 
                        [320, 1], 
                        [480,3], 
                        [768, 3], 
                        [1024, 3], 
                        [1200, 3]
                    ],
                    // pagination: true,
                    slideSpeed : 800,
                    addClassActive: true,
                    scrollPerPage: true,
                    touchDrag: true,
                    nav: false,
                    autoPlay: true,
                    afterAction: function (e) {
                        if(this.$owlItems.length > this.options.items){
                        }
                    }
                        
                });


                var productitems = jQuery('.products-related ol.product-items li.product-item');
                
                jQuery('.products-related ol.product-items').owlCarousel({
                    productitems: 2,
                    itemsCustom: [ 
                        [320, 1], 
                        [480,3], 
                        [768, 3], 
                        [1024, 3], 
                        [1200, 3]
                    ],
                    pagination: true,
                    slideSpeed : 800,
                    addClassActive: true,
                    scrollPerPage: true,
                    touchDrag: true,
                    autoPlay: true,
                    afterAction: function (e) {
                        if(this.$owlItems.length > this.options.items){
        
                        }
                    }
                        
                });

});


});