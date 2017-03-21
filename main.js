var map,
  directions_service,
  directions_display,
  map_bounds = {
    'ne_lat': 22.50823614007428,
    'ne_lng': 114.45359691269528,
    'sw_lat': 22.11311632940318,
    'sw_lng': 113.89192088730465,
  },
  markers = [];

function initMap() {
  directions_service = new google.maps.DirectionsService;
  directions_display = new google.maps.DirectionsRenderer;
  map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 22.310816,
      lng: 114.1727589
    },
    zoom: 11
  });
  directions_display.setMap(map);

  $('.create_orders').on('click', function() {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }
    // number of orders is also random
    var num_orders = 1; //Math.ceil(Math.random() * 5);

    for (var i = 0; i < num_orders; i++) {
      var pickup_lat_lng = getRandomCoordinate(map_bounds),
        dropoff_lat_lng = getRandomCoordinate(map_bounds);

      $.ajax({
        url: 'http://carpool.lalamove.com/orders/create',
        dataType: 'jsonp',
        jsonp: 'jsonp',
        data: {
          pickup_lat_lng: [pickup_lat_lng.lat(), pickup_lat_lng.lng()],
          dropoff_lat_lng: [dropoff_lat_lng.lat(), dropoff_lat_lng.lng()]
        },
        success: function(res) {
          //console.log(res);
          var start = new google.maps.Marker({
            position: pickup_lat_lng,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
          });
          var end = new google.maps.Marker({
            position: dropoff_lat_lng,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
          });
          markers.push(start, end);
          calculateAndDisplayRoute(directions_service, directions_display, pickup_lat_lng, dropoff_lat_lng);
        },
        error: function(e) {
          console.log(e);
        }
      });
    }
  });
}

function calculateAndDisplayRoute(directions_service, directions_display, start, end) {
  directions_service.route({
    origin: start,
    destination: end,
    travelMode: google.maps.TravelMode.DRIVING
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      directions_display.setDirections(response);
    } else {
      console.log('Directions request failed due to ' + status);
    }
  });
}

/**
 * function to create random marker(inside bounds)
 */
function getRandomCoordinate(map_bounds) {
  //https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&key=YOUR_API_KEY
  var random_lat_lng = new google.maps.LatLng(map_bounds.sw_lat + (Math.random() * (map_bounds.ne_lat - map_bounds.sw_lat)),
    map_bounds.sw_lng + (Math.random() * (map_bounds.ne_lng - map_bounds.sw_lng)));
  return random_lat_lng;
}
