require(['jquery'], function($){ 
"use strict";

var j = jQuery.noConflict();

j(document).ready(function(){
	
	var template = j('#nb_suggest');
	if(template!=null){

		if(searchByCates==0){
			
			j('.block-search').addClass( "nb_categories-none" );
		}
		
		var a;
		var c = false;
		var style = 'red';
		var url =baseUrl+'ajaxsearch'+'/suggest/';
				
		template.css("display", "none");
	
		j('#search').keyup(function(){
			clearTimeout(a);
			a = setTimeout(function(){
				var q_str = j('#search').val();

				if(searchByCates!=0){
					var listCate = j('#nb_button_cate').attr('data-bind');
					var data = {
						q : q_str,
						listCategories:listCate
					};
				}else{
					var data = {
						q : q_str
					};
				}

				j("span.nb_remove").css({'display':'none'});

				if (q_str.length>=minchar) {
					j('span.nb_loading').css('display','block');
					
					j.ajax({
		                url : url,
		                type: 'GET',
		                dataType: "json",
		                data: data,
		                success: function(result){
		                	template.css("display", "block");
			                j('.nb_suggest_word').empty();
			                j('#nb_product').empty();
			                j('#nb_cate').empty();
			                j("span.nb_remove").css({'display':'block'});
		                	if(result.hasOwnProperty('product')||result.hasOwnProperty('cate')){              	
			                	var html;
			             		if(result.hasOwnProperty('product')){
			             			j('#nb_suggest_product').css('display','block');
			             			
			             			html = '<ul class="nb_pro_list">';
			             			var count =0;
				                	j.each(result.product,function(key,item){
				   	
				                		/*break if number item thoughout config value*/
					                	if(count>=itemConfig['num_show']){
					                		return false; 
					                	}
					                		 /*create item*/
					                	html += productElement(item,q_str);
					                	count++;
				                		 
				                	});
				                	html += '</ul>';


				                	var numProduct = Object.keys(result.product).length; 
				                	
				  					var numProHtml = '<span>Products'+' ('+numProduct+')</span>';
				  					
				                	if(count<numProduct&&itemConfig['view_all']==1){
				                		var hrefAll;
				                		if(searchByCates!=0){
				                			var cats = j('#nb_button_cate').attr('data-bind');
				                			if(cats != ""){
				                				hrefAll = searchUrl+'?q='+j('#search').val()+'&cat='+cats+'&nbsearch=true';
				                			}else{
				                				hrefAll = baseUrl+'catalogsearch/result/?q='+q_str; 
				                			}
				                		}else{
				                			hrefAll = baseUrl+'catalogsearch/result/?q='+q_str;
				                		}
				                		html +='<a class="nb_more_product" href="'+hrefAll+'" >All Products</a>';
				                	}
				                					                	
				                	
				                	j('#nb_suggest_product').append(numProHtml);
				                	j('#nb_product').append(html);				                					                	
				                	
									
			                	}else{
			                		j('#nb_suggest_product').css('display','none');
			                	}

			                	if(result.hasOwnProperty('cate')){
			                		j('#nb_suggest_cate').css('display','block');
			                		var numCate = Object.keys(result.cate).length;
			                		var numCateHtml = '<span>Categories'+' ('+numCate+')</span>';
			             			
			             			html = '<ul class="nb_cate_list">';
			             			
				                	j.each(result.cate,function(key,item){

					                		html +=cateElement(item,q_str); 
					          	 
				                	});
				                	html += '</ul>';

				                	j('#nb_cate').empty();
				                	j('#nb_cate').append(html);
				                	j('#nb_suggest_cate').append(numCateHtml);
			                	}else{

			                		j('#nb_suggest_cate').css('display','none');
			                	}
			                	
			                	/*add croll css*/
			                	var h = window.innerHeight-window.innerHeight*0.22;
			                	
			                	template.css({
				        				'height':'auto'
				        			});

				        		if(template.height()>h){
				        			template.css({
				        				'height':h,
				        				'overflow-x':'hidden'
				        			});
				        		}

				        		if(j(window).width()<768){
									var w = j('#search_mini_form #search').innerWidth();
									template.css({
										'width':w
									});

								}else{
									
									template.css({
										'width':itemConfig['width_popup']
									 });
								}

								template.removeClass('no-result');
				                
			                	j('span.nb_loading').css('display','none');
			                }else{
			                	//reset height style
			                	template.css({
				        				'height':'auto',
				        			});

								var w = j('#search_mini_form #search').innerWidth();
									template.css({
										'width':w
									});

			                	j('.nb_suggest_word').css('display','none');
			                	template.addClass('no-result');
			                	j('#nb_product').append(NoResultText);
			                	j('span.nb_loading').css('display','none');
			                }
		                    
		                }
		            });
				}else{
					template.css("display", "none");
					j('span.nb_loading').css('display','none');
				}

			 },t_request);
		});
	
	}
	

	j('body').click(function(event) {
	    if (!j(event.target).closest('span.nb_load_cate_icon').length&&!j(event.target).closest('div.nb_list_cate').length) {
	        j('div.nb_list_cate').hide();
	    };

	});

	/*rewrite search result block if all serach by category*/

	j('#search').keypress(function(e) {
		if(searchByCates!=0){
		  	var cats = j('#nb_button_cate').attr('data-bind');
		  	if(e.which == 13) {	
		   		if(cats != ""){
		    		window.location = searchUrl+'?q='+j('#search').val()+'&cat='+jQuery('#nb_button_cate').attr('data-bind')+'&nbsearch=true';
		    		return false;
		   		}
		   		
		  	}
	  	}


 	});

 	/*rewrite search result block if all serach by category*/

 	j('#search_mini_form button.search').click(function() {
	 	if(searchByCates!=0){
		  	var cats = j('#nb_button_cate').attr('data-bind');
		   	if(cats != ""){
		    	window.location = searchUrl+'?q='+j('#search').val()+'&cat='+jQuery('#nb_button_cate').attr('data-bind')+'&nbsearch=true';
		    	return false;
		   	}
		}	   		
 	});



    j('span.nb_load_cate_icon').click(function(){
		j('div.nb_list_cate').slideToggle('fast');
	});

 	

	j("span.nb_remove").click(function() {
        j("#search").val("");
 		j('#nb_suggest').css({'display':'none'});
 		j(this).css({'display':'none'});
        
    });

	j("#nb_suggest").mouseout(function() {
        var c = false;

        a = setTimeout(function() {
            if (c != true) {
            	
                j("#nb_suggest").fadeOut("slow");
            } else {
                clearTimeout(a);
                c = true;
            }
        }, 1100)
    }).mouseover(function() {
        clearTimeout(a);
        c = true;
    });

    j("#search").mouseout(function() {
        c = false;

        a = setTimeout(function() {
            if (c != true) {	
                j("#nb_suggest").fadeOut("slow");
            } else {
                clearTimeout(a);
                c = true;
            }
        }, 1100)
    }).mouseover(function() {
        clearTimeout(a);
        c = true;
    });

    j("#search").click(function() {
        if (j("#search").val()!=""&&j("#search").val().length>=minchar) {
            var c = true;
            j("#nb_suggest").css({
                display: "block"
            }).fadeIn("fast");
     
        } else {
            j("#search").val("")
        }
    });

    /*
		reload search when fisrt click in result page
    */
    j("#search").one("click", function(){
    	if (j("#search").val()!=""&&j("#search").val().length>=minchar) {
	        var url = window.location.href;
	        if(url.indexOf('catalogsearch/result/?q')!=-1){
	            j('#search').trigger('keyup');
	        }
        }
     });

    //Show search box reponsive
    j(".block-search label.label").click(function() {
        if (j('.block-search').hasClass('active')) {
            j('.block-search').removeClass('active');
            j('.nb_remove').css('display','none');
        }else{
        	j('.block-search').addClass('active');
        }
    });

    /*
		trigger search when click checkbox and charNumber> minchar

    */

    j(".mytree-l-tcb").click(function(){
    	setTimeout(function(){
    		var cates = j('#nb_button_cate').attr('data-bind');
    		var CateName;
    		if(cates&&cates.indexOf(',')==-1){
    			CateName = j('.mytree-l-tcb[value='+cates+']').next().children().text();
    			j('.nb_load_cate_icon').text(CateName);
    		}else if(cates&&cates.indexOf(',')!=-1){
    			var count = (cates.match(/,/g) || []).length +1;
    			CateName = count+' Categories';

    			j('.nb_load_cate_icon').text(CateName);
    		}else{
    			j('.nb_load_cate_icon').text('Categories');
    		}
    	},10)
    	

    	if(j('#search').val()>=minchar){
    		j('#search').trigger('keyup');
    	}

    });

    j(".nb_cate_tab").click(function(){
    	j(".nb_result_list li").removeClass('active');
    	j(this).addClass('active');
    	j('.nb_cate_result').css('display','block');
    	j('.results').css('display','none');
    	return false;

    });

    j(".nb_product_tab").click(function(){
    	j(".nb_result_list li").removeClass('active');
    	j(this).addClass('active');
    	if(j('.nb_cate_result')!=null){
    		j('.nb_cate_result').css('display','none');
    	}
    	j('.results').css('display','block');
    	return false;
    });

});

});



function productElement(value,q){
	
	var reg = new RegExp('' + q + '', 'gi');
	
	var product_name = value.name.replace(reg,function myFunction(x){return '<span style="color:'+itemConfig['hightlight_text_color']+';">'+x+'</span>'});
	var mediaUrl = jQuery('.ajaxsearch-media-url').length ? jQuery('.ajaxsearch-media-url').text() : getBaseMediaUrl;
	var imgSrc = mediaUrl + 'catalog/product' + value.small_image;
	var element ='<li class="item">';
	if(itemConfig['show_image']==1){
		element += 
			'<a href="'+value.product_url+'">'+'<img style="width:90px;height:110px;" src="'+imgSrc+'"/></a>';
	}
	element += '<div class="meta-data">';

	element += '<a href="'+value.product_url+'" class="name">'+product_name+'</a>';
	

	if(itemConfig['show_rating']==1&&value.rating){
		
		element +=' <div class="rating-summary">';
		element +='<div class="rating-result" title="'+value.rating+'%">';
		element +='<span style="width:'+value.rating+'%"><span><span itemprop="ratingValue">'+value.rating+'</span>% of <span itemprop="bestRating">100</span></span></span>';
		element +='</div>';
		element +='</div>';
		
	}

	if(itemConfig['show_review']==1&&value.review){
		element += '<div class="review">';
		element += '<span>'+value.review+' Review(s)</span>';
		element +='</div>';
	}

	if(itemConfig['show_price']==1){
		element += '<div class="price">';
		element += '<span>$'+value.final_price+'</span>';
		element +='</div>';
	}

	


	element +='</div>';
		if (itemConfig['show_description']==1) {
			var descript = '';
			if(value.short_description){
				if(value.short_description.length>itemConfig['NumDescription']){
					descript = value.short_description.substring(0,itemConfig['NumDescription'])+'...';
				}else{
					descript =value.short_description;
				}
				descript = descript.replace(reg,function myFunction(x){return '<span style="color:'+itemConfig['hightlight_text_color']+';">'+x+'</span>'});

				element += '<div class="description">'+descript+'</div>';

			}else if(value.description){
				if(value.description.length>itemConfig['NumDescription']){
					descript = value.description.substring(0,itemConfig['NumDescription'])+'...';
				}else{
					descript =value.description;
				}
				descript = descript.replace(reg,function myFunction(x){return '<span style="color:'+itemConfig['hightlight_text_color']+';">'+x+'</span>'});
				// console.log(reg);
				element += '<div class="description">'+descript+'</div>';
			}

		}

	element += '</li>';
	return element;
}




function cateElement(value,q){
	var reg = new RegExp('' + q + '', 'gi');
	var cate_name = value.name.replace(reg,function myFunction(x){return '<span style="color:'+itemConfig['hightlight_text_color']+';">'+x+'</span>'});

	var element = '<li>';
	if(value.level=='2'){
		element +='<a href = "'+value.url+'">'+cate_name+'</a>';
	}else{
		element +='<a href = "'+value.url+'"><span>'+value.parent_cate+' > </span>'+cate_name+'</a>';
	}
	element +='</li>';

	return element;
}