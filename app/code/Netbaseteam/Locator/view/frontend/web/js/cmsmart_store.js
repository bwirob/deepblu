define([
    'jquery',
    'jquery/ui',
    'localtor_creator'
], function($,_,creator){ 
    return function (config) {
        $(document).ready(function(){
            var map;
            var panorama;
            var sv = new google.maps.StreetViewService();
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var infowindow = new google.maps.InfoWindow();
            var storeLocation  = {lat: parseFloat(config.lat),lng:parseFloat(config.lng)};
            var zoomLv = parseInt(config.zoomLv);
            var mapOptions = {
                zoom:zoomLv,
                center: storeLocation,
                streetViewControl: false
            };
            map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
            createMarker();
            panorama = map.getStreetView();
            
            var direcInput = document.getElementById("director-input");

            var direction = new google.maps.places.Autocomplete(direcInput);

            direction.addListener('place_changed', function(){
                creator.onloadPage('show');
                if(creator.handleErrorSearch(direction)){
                    directionsDisplay.setMap(null);
                    creator.onloadPage('hide');
                    return;
                }

                directionsDisplay.setMap(map);
                var  idPanel = document.getElementById("direction-panel");
                directionsDisplay.setPanel(idPanel);
                var destina = new google.maps.LatLng(storeLocation);
                var travelM = $('.travel-list .travel-active').attr('travel-data');
                var systemUnit = google.maps.UnitSystem.METRIC;
                    if(config.distanceUnit == 'mile'){
                        systemUnit = google.maps.UnitSystem.IMPERIAL;
                    }
                var request = {
                    origin: direction.getPlace().geometry.location,
                    destination: destina,
                    travelMode: travelM,
                    unitSystem: systemUnit
                };

                directionsService.route(request, function(response, status) {
                    if (creator.handleErrDirecResult(status)) {
                        directionsDisplay.setMap(null);
                        resetMap();
                        $('#direction-panel').html('');
                        creator.onloadPage('hide');
                        return;
                    }
                    directionsDisplay.setDirections(response);
                    creator.onloadPage('hide');
                });
            });

            $('ul.vertical li.travel').click(function(){
                $(this).parent().children().removeClass('travel-active');
                $(this).addClass('travel-active');
                var str_point = $('.directions-box .originA').val();
                if(str_point!=''){
                    var num = $(this).parent().attr('number-box');
                    num = parseInt(num);
                    google.maps.event.trigger(direction, "place_changed"); 
                }
                
            });

            $('button.get-direction').click(function(){
                google.maps.event.trigger(direction, "place_changed");
            });

            $('button.street-view').click(function(){
                toggleStreetView(storeLocation);
            });

            var head = $('iframe.fb_iframe_widget_lift').contents().find("head");
           
            var css = '<style type="text/css">' + '._2pi8{display:none;}; ' + '</style>';
            $(head).append(css);
            
            function toggleStreetView(position) {
                sv.getPanorama({location: position, radius: 50}, processSVData);
            }

            function processSVData(data, status) {
              if (status === 'OK') {
                panorama.setPano(data.location.pano);
                panorama.setPov({
                  heading: 70,
                  pitch: 0
                });
                panorama.setVisible(true);
              } else {
                panorama.setVisible(false);
                alert('Street View data not found for this location');
                
              }
            }         

            function createMarker() {
                var point = new google.maps.LatLng(storeLocation);
                var storeInfo = getPositionData();
                storeInfo = JSON.parse(storeInfo);
                var marker = new google.maps.Marker({
                    position: point               
                });

                marker.setMap(map);

                var infoContent = creator.createPopupContent(storeInfo);

                google.maps.event.addListener(marker, 'click', function() {
                    map.setCenter(marker.getPosition());
                    map.setZoom(zoomLv);
                    infowindow.setContent(infoContent); 
                    infowindow.open(map,marker);
                    
                });
            }

            function getPositionData(){
                var info  = $('.option-direction').attr('position-data');
                return info;
            }

            function resetMap(){
                map.setCenter(storeLocation);
                map.setZoom(zoomLv);
            }



            
        });

    }

});