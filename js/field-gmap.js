/*jshint devel:true */
/*global google */

(function($) {

	var CMBGmapsInit = function( fieldEl ) {

		var searchInput = $('.map-search', fieldEl ).get(0);
		var mapCanvas   = $('.map', fieldEl ).get(0);
		var latitude    = $('.latitude', fieldEl );
		var longitude   = $('.longitude', fieldEl );
		var elevation   = $('.elevation', fieldEl );
		var elevator    = new google.maps.ElevationService();

		var mapOptions = {
			center:    new google.maps.LatLng( CMBGmaps.defaults.latitude, CMBGmaps.defaults.longitude ),
			zoom:      parseInt( CMBGmaps.defaults.zoom ),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map      = new google.maps.Map( mapCanvas, mapOptions );

		// Marker
		var markerOptions = {
			map: map,
			draggable: true,
			title: CMBGmaps.strings.markerTitle
		};

		var marker = new google.maps.Marker( markerOptions );
		marker.setPosition( mapOptions.center );

		function setPosition( latLng, zoom ) {

			marker.setPosition( latLng );
			map.setCenter( latLng );

			if ( zoom ) {
				map.setZoom( zoom );
			}

			latitude.val( latLng.lat() );
			longitude.val( latLng.lng() );

			elevator.getElevationForLocations( { locations: [ marker.getPosition() ] }, function (results, status) {
				if (status == google.maps.ElevationStatus.OK && results[0] ) {
					elevation.val( results[0].elevation );
				}
			});

		}

		// Set stored Coordinates
		if ( latitude.val() && longitude.val() ) {
			latLng = new google.maps.LatLng( latitude.val(), longitude.val() );
			setPosition( latLng, 17 )
		}

		google.maps.event.addListener( marker, 'dragend', function() {
			setPosition( marker.getPosition() );
		});

		// Search
		var autocomplete = new google.maps.places.Autocomplete(searchInput);
		autocomplete.bindTo('bounds', map);

		google.maps.event.addListener(autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			}

			setPosition( place.geometry.location, 17 );

		});

		$(searchInput).keypress(function(e) {
			if (e.keyCode === 13) {
				e.preventDefault();
			}
		});

	}

	CMB.addCallbackForInit( function() {
		$('.CMB_Gmap_Field .field-item').each(function() {
			CMBGmapsInit( $(this) );
		});
	} );

	CMB.addCallbackForClonedField( ['CMB_Gmap_Field'], CMBGmapsInit );

}(jQuery));