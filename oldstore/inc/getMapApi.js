function initMap(googleMap, searchInput, inputLatitude, inputLongitude, lat = 13.734210584614464, lng = 100.62714278697968, draggable = false) {

if(googleMap){

    
    // Default values if lat or lng are not provided
    lat = lat || 13.734210584614464;
    lng = lng || 100.62714278697968;

    var valZoom = 0;
    if(searchInput && inputLatitude && inputLongitude){
        valZoom = 8;
    }else{
        valZoom = 18;
    }

    var map = new google.maps.Map(document.getElementById(googleMap), {
        center: { lat: lat, lng: lng },
        zoom: valZoom,
        mapTypeId: 'roadmap'
    });

    var geocoder = new google.maps.Geocoder();
    var infoWindow = new google.maps.InfoWindow();

    // Create a marker if draggable is true
    var marker = null;
    if (draggable) {
        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: 'สถานที่ที่คุณเลือก',
            draggable: true // Allow the marker to be draggable
        });
    }

    // Call reverseGeocode with the initial latLng
    reverseGeocode(geocoder, { lat: lat, lng: lng }, inputLatitude, inputLongitude, searchInput);

    if(searchInput && inputLatitude && inputLongitude){
        setupAutocomplete(map, searchInput, inputLatitude, inputLongitude, marker);
        setupClickListener(map, geocoder, infoWindow, inputLatitude, inputLongitude, marker);
    }
    
    

    // Function to set up autocomplete
    function setupAutocomplete(map, searchInput, inputLatitude, inputLongitude, marker) {
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById(searchInput));
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            map.setCenter(place.geometry.location);
            map.setZoom(15);

            if (marker) {
                marker.setPosition(place.geometry.location);
            }

            var latElement = document.getElementById(inputLatitude);
            var lngElement = document.getElementById(inputLongitude);

            if (latElement) latElement.value = place.geometry.location.lat();
            if (lngElement) lngElement.value = place.geometry.location.lng();

        });
    }

    // Function to set up click listener for the map
    function setupClickListener(map, geocoder, infoWindow, inputLatitude, inputLongitude, marker) {
        google.maps.event.addListener(map, 'click', function (event) {
            var latLng = event.latLng;

            if (marker) {
                marker.setPosition(latLng);
            }

            updateCoordinates(latLng);
            reverseGeocode(geocoder, latLng, inputLatitude, inputLongitude, searchInput);
        });

        // If the marker is draggable
        if (draggable && marker) {
            marker.addListener('dragend', function (event) {
                var latLng = event.latLng;
                updateCoordinates(latLng);
            });
        }
    }

    // Function to update coordinates
    function updateCoordinates(latLng) {
        var latElement = document.getElementById(inputLatitude);
        var lngElement = document.getElementById(inputLongitude);

        if (latElement) latElement.value = latLng.lat();
        if (lngElement) lngElement.value = latLng.lng();

        reverseGeocode(geocoder, latLng, inputLatitude, inputLongitude, searchInput);
    }

    // Function to perform reverse geocoding
    function reverseGeocode(geocoder, latLng, inputLatitude, inputLongitude, searchInput) {
        geocoder.geocode({ 'location': latLng }, function (results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    // Geocoding completed
                    
                    var latElementGps = document.getElementById(inputLatitude).value;
                    var lngElementGps = document.getElementById(inputLongitude).value;

                    // var searchInputElement = document.getElementById(searchInput);
                    // if(searchInput == 'searchInput'){
                    //     searchInputElement.value = results[0].formatted_address;
                    // }else{
                    //     searchInputElement.value = '';
                    // }

                    var searchInputElement = document.getElementById(searchInput);
                    if(latElementGps && lngElementGps){

                        if(searchInputElement){
                            searchInputElement.value = results[0].formatted_address;
                        }
                    }
                    

                    // Optionally update the info window with address information
                    // infoWindow.setContent(results[0].formatted_address);
                    // infoWindow.open(map, marker);

                } else {
                    console.log('ไม่พบที่อยู่');
                }
            } else {
                console.log('การค้นหาที่อยู่ล้มเหลว: ' + status);
            }
        });
    }

    map.setOptions({
        zoomControl: true,
        mapTypeControl: false,
        streetViewControl: false
    });

}
}
