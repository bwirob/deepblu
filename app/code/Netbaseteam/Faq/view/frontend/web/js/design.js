// define(['jquery'], function($){ 
//     "use strict";
//     $(document).ready(function(){
//         /*
//             Style Custom Design
//         */
//         var categoryItem = $('.category-item ');
//         $.each(categoryItem,function(key,item){
//             var dataDesign = $(item).attr("data-design");
//             var j = $.parseJSON(dataDesign);
//             $(item).children().find('h3.category-title').css({
//                                                             "color":j.category.color,
//                                                             'fontSize':j.category.fontsize+'px'
//                                                         });

//             $(item).children().find('.category-title i.line').css("border",'1px solid '+j.category.color);
//             $(item).children().find('.category-title i.line').css("border",'1px solid '+j.category.color);
//             $(item).children().find('.cate-description').css('fontSize',j.faq.fontsize);
//             $(item).children().find('.panel-heading').css({
//                                                             "border":'solid '+j.faq.border_width+'px '+j.faq.border_color,
//                                                             'background-color':j.faq.background_color
                                                            
//                                                         });
//             $(item).children().find('.panel-heading .panel-title a').css({
//                                                             "fontSize":j.faq.fontsize+'px',
//                                                             'color':j.faq.color
                                                            
//                                                         });
//             $(item).children().find('.panel-heading .panel-title i').css({
//                                                             "fontSize":j.faq.fontsize+'px',
//                                                             'color':j.faq.color
                                                            
//                                                         });

//             $(item).children().find('.panel-body .panel-content').css({
//                                                             "fontSize":j.faq.fontsize+'px'
//                                                         });

//         });

//         var faqHeading = $('.faq_listing .panel .panel-heading'); 
//         $(faqHeading).click(function(){
//             var dataDesign = $(this).parents('.category-item').attr('data-design');
//             var j = $.parseJSON(dataDesign);
//             if($(this).hasClass('panel-active')){
//                $(this).children().find('a').css({
//                     'color':j.faq.color
//                 });
//                 $(this).children().find('i').css({
//                     'color':j.faq.color
//                 });
//                 $(this).css({
//                     'background-color':j.faq.background_color
//                 });
//                 $(this).removeClass('panel-active');
//             }else{
//                 $(this).addClass('panel-active');
//                 $(this).css({
//                     'background-color':j.faq.active_background
//                 });
//                 $('.panel-active').children().find('a').css({
//                     'color':j.faq.active_color
//                 });
//                 $('.panel-active').children().find('i').css({
//                     'color':j.faq.active_color
//                 });

//             }
//         });
//     });
// });