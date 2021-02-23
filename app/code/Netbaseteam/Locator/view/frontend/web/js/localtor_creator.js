define([
    'jquery',
    'localtor_init',
    'localtor_creator'
    ], 
    function($,init,creator) {        

        return {
            
            createPopupContent: function(info) {
                var html = '<div class="marker-popup">';
                html += '<h2>'+info.store_name+'</h2>';
                if (info.address) {
                    html += '<b>'+'Address: '+'</b><span>'+info.address+'</span>';
                    html += '<br/>';
                }
                if (info.description) {
                    html += '<b>'+'Description: '+'</b><span>'+info.description+'</span>';
                    html += '<br/>';
                }

                if (info.phone_number) {
                    html += '<b>'+'P: '+'</b><span>'+info.phone_number+'</span>';
                    html += '<br/>';
                }
                if (info.fax_number) {
                    html += '<b>'+'Fax: '+'</b><span>'+info.fax_number+'</span>';
                    html += '<br/>';
                }
                if (info.store_link) {
                    html += '<a href="'+info.store_link+'">'+info.store_link+'</a>';
                    html += '<br/>';
                }
                html += '</div>';
                return html;
            },
            createCircleOption : function(map,location,radius) {

               return {
                    strokeColor: '#867979',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#ffcccc',
                    fillOpacity: 0.35,
                    map: map,
                    center: location,
                    radius: radius
               };
            },
            getZoomByDistance : function(radius) {
                var zoomLv = 2;
                if(300000<radius){
                    zoomLv = 6;
                }else if(180000<radius&&radius<=300000){
                    zoomLv = 7;
                }else if(100000<radius&&radius<=180000){
                    zoomLv = 8;
                }else if(50000<radius&&radius<=100000){
                    zoomLv = 9;
                }else if(15000<radius&&radius<=50000){
                    zoomLv = 10;
                }else if(10000<radius&&radius<=15000){
                    zoomLv = 11;
                }else if (5000<radius&&radius<=10000){
                    zoomLv = 12;
                }else if (2000<radius&&radius<=5000){
                    zoomLv = 13;
                }else if (1300<radius&&radius<=2000){
                    zoomLv = 14;
                }else if (1000<=radius&&radius<=1300){
                    zoomLv = 15;
                }
                return zoomLv;
            },

            checkStoreContain : function(radius,point,center) {
                var distance =  google.maps.geometry.spherical.computeDistanceBetween(point,center);
                if(distance<=radius){
                    return true;
                }
                return false;
            },

            showhideStoreContain :function(num_store,style,stt) {
                if(style == 'hide'){
                    $( "ul.list-store-container li.store-item" ).eq(num_store).css({'display':'none'});
                    $( "ul.list-store-container li.store-item" ).eq(num_store).attr("item-number",stt);
                }else{
                    $( "ul.list-store-container li.store-item" ).eq(num_store).css({'display':'block'});
                    $( "ul.list-store-container li.store-item" ).eq(num_store).attr("item-number",stt);
                    $( "ul.list-store-container li.store-item" ).eq(num_store).removeClass('hide');
                }
            },
            updateCountStore :function(t) {
                $('.list-title span.number-store').html(t+'Stores');
            },
            showSliderRange: function(value,unit) {
                if (unit=='mile') {
                    $('span.slider-range-amount').html(value+' Miles');
                    var distance = parseFloat(value*1.609344*1000).toFixed(2)
                    $('span.slider-range-amount').attr('range-data',distance);
                } else {
                    $('span.slider-range-amount').html(value+' Km');
                    $('span.slider-range-amount').attr('range-data',value*1000);  
                }
                
            },
            resetSearchRange: function(unit) {
                if(unit=='mile'){
                    $('span.slider-range-amount').html('1 Mile');
                    $('#auto-fill').val('');
                    $('span.slider-range-amount').attr('range-data',1000*1.609344);
                    $('#slider-range-min .ui-slider-handle').css({'left':'0%'});
                }else{
                    $('span.slider-range-amount').html('1 Km');
                    $('#auto-fill').val('');
                    $('span.slider-range-amount').attr('range-data',1000);
                    $('#slider-range-min .ui-slider-handle').css({'left':'0%'});
                }
                
            },
            createDirectionOj :function(storeListData){
                var directions = new Array();
                var input;
                var idItem;
                var self = this;
                $.each(storeListData,function(key,item){
                    idItem = 'director-'+key;
                    input = document.getElementById(idItem);
                    directions[key] = new google.maps.places.Autocomplete(input);
                });
                return directions;
            },
            handleErrorSearch: function(direc){
               if (!direc.getPlace()||direc.getPlace() == null) {
                    alert("No details available for this location!");
                    return true;
                }
                if(direc.getPlace().geometry == null||!direc.getPlace().geometry.location){
                    alert("No details available for this location!");
                    return true;
                }
                return false;
            },

            handleErrDirecResult: function(status){
               if (status!='OK') {
                    console.log('Directions request failed due to'+status);
                    alert('At least one of origin, destination or waypoint could not be geocode');
                    return true;
                }
                return false;
            },
            getPanelDirection: function(key){
                var idPanel =document.getElementById('directions-panel-'+key);
                return idPanel;
            },
            getTravelMode: function(){
                var travelData = $('.popup-active .travel-active').attr('travel-data');
                return travelData;
            },
            validEmptySearch: function(){
                var fields = $('#form-search-area .form-control');
                var empty = true;
                $.each(fields,function(key,item){
                    var fieldVal = $(item).val();
                    if(fieldVal){
                        empty = false;
                        return false;
                    }
                });

                return empty;
            },
            onloadPage : function(style){
                if(style == 'hide'){
                    $('#page-loading').addClass('hide');
                }else{
                    $('#page-loading').removeClass('hide');
                }
            }
            
        }

    }                
);
