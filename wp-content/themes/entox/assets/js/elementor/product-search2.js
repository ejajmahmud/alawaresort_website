(function($){
	"use strict";
	

	$(window).on('elementor/frontend/init', function () {
        
		
		/* Product Slider */
		elementorFrontend.hooks.addAction('frontend/element_ready/entox_elementor_product_search2.default', function(){
			$(".ova-product-search2").each(function(){
				var that = $(this);

		        var input = document.getElementById('pac-input');
	            var autocomplete = '';
	            if( input ){
	               var autocomplete = new google.maps.places.Autocomplete(input);
	            }

	            if( autocomplete !== '' ){
	                autocomplete.addListener('place_changed', function() {

	                    var place = autocomplete.getPlace();
	                    if (!place.geometry) {
	                        return;
	                    }

	                    that.find("#map_name").val(place.name);
	                    that.find("#map_address").val(place.formatted_address);

	                    that.find('#map_lat').val(place.geometry.location.lat());
	                    that.find('#map_lng').val(place.geometry.location.lng());

	                    var point = {};
	                    point.lat = place.geometry.location.lat();
	                    point.lng = place.geometry.location.lng();
	                });
            	}

            	var locate_me = that.find('.locate_me');

            	if ( typeof google !== 'undefined' && that.length > 0 && navigator.geolocation && locate_me.length > 0 ) {
					navigator.geolocation.getCurrentPosition(showPosition);
				}

            	locate_me.on('click', function() {
            		if (navigator.geolocation) {
		               navigator.geolocation.getCurrentPosition(showPosition);
		            } else {
		               x.innerHTML = "Geolocation is not supported by this browser.";
		            }
            	});

            	function showPosition(position) {
		            var map_lat = position.coords.latitude;
		            var map_lng = position.coords.longitude;

		            that.find('[name="map_lat"]').attr('value', map_lat);
		            that.find('[name="map_lng"]').attr('value', map_lng);

		            var latlng = {
		               lat: parseFloat(map_lat),
		               lng: parseFloat(map_lng)
		            };
		            var geocoder = new google.maps.Geocoder;
		            geocoder.geocode({
		               'location': latlng
		            }, function( results, status ) {
		               if (status === 'OK') {
		                  if (results[0]) {
		                     	that.find('[name="map_address"]').val(results[0].formatted_address);
		                  } else {
		                     window.alert('No results found');
		                  }
		               } else {
		                  window.alert('Geocoder failed due to: ' + status);
		               }
		            });
		        }

		    });

		});
		/* end Product Slider */
   });

})(jQuery);
