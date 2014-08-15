/*jshint devel:true */
/*global google */

(function($) {

    var CMBGmapsInit = function( fieldEl ) {

        var searchInput = $('.map-search', fieldEl ).get(0);
        var mapCanvas   = $('.map', fieldEl ).get(0);
        var latitude    = $('.latitude', fieldEl );
        var longitude   = $('.longitude', fieldEl );

        console.log( fieldEl );
        console.log( latitude );
        console.log( longitude );

        var mapOptions = {
            center:    new google.maps.LatLng( CMBGmaps.defaults.latitude, CMBGmaps.defaults.longitude ),
            zoom:      parseInt( CMBGmaps.defaults.zoom ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map( mapCanvas, mapOptions );

        // Marker
        var markerOptions = {
            map: map,
            draggable: true,
            title: CMBGmaps.strings.markerTitle
        };

        var marker = new google.maps.Marker( markerOptions );

        // Set stored Coordinates
        if ( latitude.val() && longitude.val() ) {
            latLng = new google.maps.LatLng( latitude.val(), longitude.val() );
            marker.setPosition(latLng);
            map.setCenter( latLng );
            map.setZoom(17);
        }

        google.maps.event.addListener(marker, 'drag', function() {
            latitude.val(marker.getPosition().lat());
            longitude.val(marker.getPosition().lng());
        });

        // Search
        var autocomplete = new google.maps.places.Autocomplete(searchInput);
        autocomplete.bindTo('bounds', map);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);

            latitude.val(place.geometry.location.lat());
            longitude.val(place.geometry.location.lng());
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