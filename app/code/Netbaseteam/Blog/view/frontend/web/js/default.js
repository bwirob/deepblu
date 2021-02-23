define(['jquery','owlCarousel'], function($,owlCarousel){ 
    "use strict";
    $(document).ready(function(){
        jQuery(".post-list .list-content .post-item .post-wrapper .share .share-title,.meta-btn .share-list > span.share-title").click(function(){
            // jQuery(this).next().css("visibility","visible");
            jQuery(this).next().toggleClass("active");
        });
       $('#search_form').submit(function(){
       		var q = $('.search_box').val();
       		if(q.length == 0){
       			return false;
       		}
       }); 
       $('.toolbar-amount').css('display','none');
       addGridStyle(postlistStyle);
       	function addGridStyle(postlistStyle){
       		var postItem = $('.post-list .post-item');       		
       		switch(postlistStyle) {
			    case 'grid-2':
			        $('.post-list .post-item:even').css({
			       		'margin-right':'20px',
			       });
			       $.each(postItem,function(key,item){
			       		$(item).addClass('grid-2');
			       		if(key%2==0){
			       			$(item).addClass('clear');
			       		}
			       });
			       break;
			    case 'grid-3':
			    	var w = $(window).width();
			    	if (480<w && w<640){
			    		$('.post-list .post-item:even').css({
			       			'margin-right':'20px',
			       		});

			       		$.each(postItem,function(key,item){
			       			$(item).addClass('grid-2');
			       			if(key%2==0){
			       				$(item).addClass('clear');
			       			}
			       		});
					}else{
						
				       	$.each(postItem,function(key,item){
				       		$(item).addClass('grid-3');
				       		if(key%3==0){
			       				$(item).addClass('clear');
			       			}	

				       		if (key==1||key==4||key==7) {
				       			$(item).css({
					       			'margin-right':'10px',
					       			'margin-left':'10px'
			       				});
				       		} 
				       		
				       	});
					}
			        
			       break;

			    default:
			        break;
			}
		}

		require(['jquery'], function($) {
        jQuery(document).ready(function() {		
			$('.slider .owl-carousel').owlCarousel({
				items: 6,
				itemsCustom: [ 
					[320, 1], 
					[480, 4], 
					[768, 3], 
					[1024, 4], 
					[1200, 4]
				],
				pagination: false,
				autoPlay: true,
				slideSpeed : 800,
				addClassActive: true,
				scrollPerPage: false,
				touchDrag: true,
				afterAction: function (e) {
					if(this.$owlItems.length > this.options.items){
						$('.slider .navslider').show();
					}else{
						$('.slider .navslider').hide();
					}
				}
				//scrollPerPage: true,
			});
			$('.blog-navslider .prev').on('click', function(e){
				e.preventDefault();
				$('.owl-carousel').trigger('owl.prev');
			});
			$('.blog-navslider .next').on('click', function(e){
				e.preventDefault();
				$('.owl-carousel').trigger('owl.next');
			});
		});
    }); 
    });
});