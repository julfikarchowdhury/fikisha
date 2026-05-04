
"use strict";

var mapLat = mapLat;
var mapLong = mapLong;
function initMap() {
  
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            if(mapLat !='' && mapLong !='') {
                initAutocomplete();
                getLatLongPosition(mapLat, mapLong);
            }else {
                initAutocomplete();
                getLatLongPosition(position.coords.latitude, position.coords.longitude);
            }
        });
    } else {
        alert("Sorry, your browser does not support HTML5 geolocation.");
    }
}

function getLatLongPosition(latitude, longitude) {
    const myLatlng = {
        lat: parseFloat(latitude),
        lng: parseFloat(longitude)
    };
    const map = new google.maps.Map(document.getElementById("sgoogleMap"), {
        zoom: 15,
        center: myLatlng,
    });

    // Create the initial InfoWindow.
    let infoWindow = new google.maps.InfoWindow({
        content: "Click the map to get latitude & longitude!",
        position: myLatlng,
    });

    infoWindow.open(map);
    // Configure the click listener.
    var marker;
    let total = 0;
    map.addListener("click", (mapsMouseEvent) => {
        // Close the current InfoWindow.
        infoWindow.close();
        // Create a new InfoWindow.
        infoWindow = new google.maps.InfoWindow({
            position: mapsMouseEvent.latLng,
        });

        var latLng = mapsMouseEvent.latLng.toJSON();

        var latlng = new google.maps.LatLng(latLng.lat, latLng.lng);
        var geocoder = geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'latLng': latlng
        }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    $('currentLocation #autocomplete-input').html(results[1].formatted_address);

                }
            }
        });

        $('#lat').val(latLng.lat);
        $('#long').val(latLng.lng);

        if (marker)
            marker.setMap(null);
        marker = new google.maps.Marker({
            position: myLatlng,
            map,
            draggable: true,
            title: "Your current location.",
        });

        changeMarkerPosition(latLng, marker)

    });


    $('#lat').val(latitude);
    $('#long').val(longitude);
    marker = new google.maps.Marker({
        position: myLatlng,
        map,
        draggable: true,
        title: "Your current location.",
    });
}

function changeMarkerPosition(latLng, marker) {
    var latlng = new google.maps.LatLng(latLng.lat, latLng.lng);
    marker.setPosition(latlng);
}
 