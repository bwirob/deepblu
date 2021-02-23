define(['jquery'], function($){ 
    "use strict";
    $(document).ready(function(){
        /*
            Toggle Click to FAQ
        */

        var faqHeading = $('.panel .panel-heading');

        $(faqHeading).click(function(){
            var icon = $(this).children().find('i');

            if($(icon).hasClass('icon-plus-1')){
                $(icon).removeClass('icon-plus-1');
                $(icon).addClass('icon-minus-1');
            }else{
                $(icon).removeClass('icon-minus-1');
                $(icon).addClass('icon-plus-1');
            }   
            $(this).next().fadeToggle('500',function(){
                
            });
        });
                   
    });
});