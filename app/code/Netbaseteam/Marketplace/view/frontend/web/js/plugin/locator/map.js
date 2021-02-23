define([
    'jquery'
], function($){
    return function (config) {
        $(document).ready(function(){
            var map;
            var panorama;
            var infowindow = new google.maps.InfoWindow();
            var storeLocation  = {lat: parseFloat(config.latDefault),lng:parseFloat(config.lngDefault)};
            var zoomLv = parseInt(config.zoomLevelDefault);
            var geocoder = new google.maps.Geocoder();
            var markers = [];
            if($("input[name='shop_latitude']").val()&& $("input[name='shop_longitude']").val()) {
                storeLocation = {lat: parseFloat($("input[name='shop_latitude']").val()), lng:parseFloat($("input[name='shop_longitude']").val())};
            }
            if($("input[name='zoom_level']").val()) {
                zoomLv = parseInt($("input[name='zoom_level']").val());
            }
            var mapOptions = {
                zoom:zoomLv,
                center: storeLocation,
                streetViewControl: false
            };
            var applyBtn;
            var marker;
            map = new google.maps.Map(document.getElementById("map"), mapOptions);

            map.addListener('click', function(event){
                clearMarkers();
                var point = event.latLng;
                geocoder.geocode({
                    'latLng': point
                }, function (results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        var marker = new google.maps.Marker({
                            position: event.latLng,
                            map: map
                        });
                        markers.push(marker);



                        if (results[0]) {
                            var infoAddress = getAddressInfo(results[0],point);
                            var popupContent = createPopupInfo(infoAddress);
                            infowindow.setContent(popupContent);
                            infowindow.open(map,marker);
                            applyBtn = $('<button type="button" style="display: none" class="apply-to-form primary">Apply to Form</button>');
                            $(".button").append(applyBtn);

                            applyBtn.click(function(){
                                $("input[name='country_code']").val(infoAddress.country);
                                $("input[name='shop_location']").val(infoAddress.adr_format);
                                $("input[name='shop_latitude']").val(infoAddress.lat);
                                $("input[name='shop_longitude']").val(infoAddress.lng);
                                $("input[name='zoom_level']").val(map.getZoom());

                                window.scrollTo(0,0);

                            });

                        } else {
                            alert('No results found');
                        }
                    } else {

                    }
                });
            });
            var searchbox = document.getElementById("search-auto");
            var searchAuto = new google.maps.places.Autocomplete(searchbox);

            searchAuto.addListener('place_changed', function(){
                if(!handleErrorSearch(this)){

                    //map.setCenter(this.getPlace().geometry.location);
                    //console.log(this.getPlace().geometry.location.lat());
                    var myLatlng = new google.maps.LatLng(this.getPlace().geometry.location.lat(),this.getPlace().geometry.location.lng());
                    bounds  = new google.maps.LatLngBounds();

                    bounds.extend(this.getPlace().geometry.location);

                    map.setCenter(bounds.getCenter()); //or use custom center
                    map.fitBounds(bounds);

                    var zoom = '';
                    var type = this.getPlace().types;


                    var i;
                    for (i = 0; i < type.length; ++i) {
                        if(type[i] == 'street_address') {
                            zoom = 19;
                            break;
                        }
                        if(type[i] == 'route') {
                            zoom = 17;
                            break;
                        }
                        if(type[i] == 'intersection') {
                            zoom = 15;
                            break;
                        }
                        if(type[i] == 'political') {
                            zoom = 13;
                            break;
                        }
                        if(type[i] == 'locality') {
                            zoom = 11;
                            break;
                        }
                        if(type[i] == 'administrative_area_level_1') {
                            zoom = 11;
                            break;
                        }

                        if(type[i] == 'administrative_area_level_2') {
                            zoom = 15;
                            break;
                        }

                        if(type[i] == 'country') {
                            zoom = 4;
                            break;
                        }
                    }

                    map.setZoom(zoom);

                }

            });

            $('#search-auto').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            $('.search-box .btn-reset').click(function(){
                infowindow.close();
                resetMapAction();
                return false;
            });




            function getAddressInfo(results,point){
                var geoinfo = geoDetailInfo(results);
                var info = {
                    'adr_format':results.formatted_address,
                    'lat':point.lat(),
                    'lng':point.lng(),
                    'country':geoinfo.country,
                    'region':geoinfo.region,
                    'city':geoinfo.city

                };
                return info;
            }

            function geoDetailInfo(geoResult){
                var country = '';
                var region = '';
                var city = '';
                for (var i=0; i<geoResult.address_components.length; i++){
                    if (geoResult.address_components[i].types[0] == "locality") {

                        city = geoResult.address_components[i].long_name;
                    }
                    if (geoResult.address_components[i].types[0] == "administrative_area_level_1") {

                        region = geoResult.address_components[i].long_name;
                    }

                    if (geoResult.address_components[i].types[0] == "country") {
                        country = geoResult.address_components[i].short_name;

                    }
                }
                var info = {
                    country:country,
                    region:region,
                    city:city
                }

                return info;
            }


            function setMapOnAll(map) {
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(map);
                }
            }


            function clearMarkers() {
                setMapOnAll(null);
            }

            function createPopupInfo(info){
                var html = '<div class="map-infowindow">';
                html += '</div>';
                if(info.adr_format){
                    html += '<span class="formattedAddress">Address: '+info.adr_format+'</span><br>';
                }
                html += '<span class="latlng">{Latitude:'+info.lat+',Longitude:'+info.lng+'}</span><br>';

                html += '</div>';
                html += '<div class="button"></div>';

                return html;
            }

            function handleErrorSearch(direc){
                if (!direc.getPlace()||direc.getPlace() == null) {
                    alert("No details available for this location!");
                    return true;
                }
                if(direc.getPlace().geometry == null||!direc.getPlace().geometry.location){
                    alert("No details available for this location!");
                    return true;
                }
                return false;
            }

            function resetMapAction(){

                map.setCenter(storeLocation);
                map.setZoom(zoomLv);

            }

        });

    }

});