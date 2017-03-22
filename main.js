var map,
  directions_service,
  directions_display,
  map_bounds = {
    'ne_lat': 22.50823614007428,
    'ne_lng': 114.45359691269528,
    'sw_lat': 22.11311632940318,
    'sw_lng': 113.89192088730465,
  },
  map_center = {
    lat: 22.310816,
    lng: 114.1727589
  },
  order_colors = ['blue', 'pink', 'green', 'orange', 'purple'],
  cur_color = 0,
  map_center_lat_lng,
  markers = [];

function initMap() {
  directions_service = new google.maps.DirectionsService;
  directions_display = new google.maps.DirectionsRenderer;
  map = new google.maps.Map(document.getElementById('map'), {
    center: map_center,
    zoom: 11
  });
  map_center_lat_lng = new google.maps.LatLng(map_center.lat, map_center.lng);
  directions_display.setMap(map);

  $('.create_orders').on('click', function() {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(null);
    }
    // number of orders is also random
    var num_orders = Math.ceil(Math.random() * 5);

    for (var i = 0; i < num_orders; i++) {
      createOrder();
    }
  });
}

/**
 * create random order at client side
 */
function createOrder() {
  $.when(getRandomAccessibleCoordinate($.Deferred()), getRandomAccessibleCoordinate($.Deferred()))
    .then(function(pickup_lat_lng, dropoff_lat_lng) {
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
            icon: 'http://maps.google.com/mapfiles/ms/micons/' + order_colors[cur_color % 5] + '.png'
          });
          var end = new google.maps.Marker({
            position: dropoff_lat_lng,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/micons/' + order_colors[(cur_color++) % 5] + '-dot.png'
          });
          markers.push(start, end);
          /*calculateRoute(pickup_lat_lng, dropoff_lat_lng).done(function(response) {
            directions_display.setDirections(response);
          });*/
        },
        error: function(e) {
          console.log(e);
        }
      });
    });
}

/**
 * calculate route & also check if the place is accessible
 */
function calculateRoute(start, end) {
  var promise = $.Deferred();
  directions_service.route({
    origin: start,
    destination: end,
    travelMode: google.maps.TravelMode.DRIVING
  }, function(response, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      promise.resolve(response, status);
    } else {
      promise.reject(status);
    }
  });
  return promise;
}

/**
 * function to create random accessible marker(inside bounds)
 */
function getRandomAccessibleCoordinate(promise) {
  // random place
  var random_lat_lng = new google.maps.LatLng(map_bounds.sw_lat + (Math.random() * (map_bounds.ne_lat - map_bounds.sw_lat)),
    map_bounds.sw_lng + (Math.random() * (map_bounds.ne_lng - map_bounds.sw_lng)));

  calculateRoute(map_center_lat_lng, random_lat_lng).done(function(response, status) {
    promise.resolve(random_lat_lng);
    // if it is not accessible, keep randomnize
  }).fail(function(status) {
    console.log('not accessible');
    getRandomAccessibleCoordinate(promise);
  });
  return promise.promise();
}
