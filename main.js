var map,
  map_bounds = {
    'ne_lat': 22.50823614007428,
    'ne_lng': 114.45359691269528,
    'sw_lat': 22.11311632940318,
    'sw_lng': 113.89192088730465,
  };

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 22.310816,
      lng: 114.1727589
    },
    zoom: 11
  });

  $('.create_orders').on('click', function() {
    // number of orders is also random
    var num_orders = Math.ceil(Math.random() * 5);

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
          console.log(res);
          new google.maps.Marker({
            position: pickup_lat_lng,
            map: map
          });
          new google.maps.Marker({
            position: dropoff_lat_lng,
            map: map
          });
        },
        error: function(e) {
          console.log('e');
        }
      });
    }
  });

  // function to create random marker(inside bounds)
  function getRandomCoordinate(map_bounds) {
    return new google.maps.LatLng(map_bounds.sw_lat + (Math.random() * (map_bounds.ne_lat - map_bounds.sw_lat)),
      map_bounds.sw_lng + (Math.random() * (map_bounds.ne_lng - map_bounds.sw_lng)));
  }
}
