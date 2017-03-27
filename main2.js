var markers = [],
  vehicles = [],
  order_colors = ['blue', 'orange', 'green', 'blue', 'orchid'],
  cur_color = 0;

function initMap() {
  var map = new AMap.Map('container', {
    center: [114.1727589, 22.310816],
    zoom: 11
  });
  map.plugin(["AMap.ToolBar"], function() {
    map.addControl(new AMap.ToolBar());
  });

  $('.create_orders').on('click', function() {
    markers.map(function(item) {
      item.setMap(null);
    });
    createOrder().done(function(res) {
      console.log(res);
      AMapUI.loadUI(['overlay/SimpleMarker'], function(SimpleMarker) {
        var start = new SimpleMarker({
          iconLabel: 'S',
          iconStyle: order_colors[cur_color % 5],
          map: map,
          position: res.pickup_lat_lng
        });
        var end = new SimpleMarker({
          iconLabel: 'E',
          iconStyle: order_colors[cur_color++ % 5],
          map: map,
          position: res.dropoff_lat_lng
        });
        markers.push(start, end);
      });
    });
  });

  $('.get_vehicles').on('click', function() {
    vehicles.map(function(item) {
      item.setMap(null);
    });
    createVehicles().done(function(vehicles) {
      console.log(vehicles);
      AMapUI.loadUI(['overlay/SimpleMarker'], function(SimpleMarker) {
        var start = new SimpleMarker({
          iconLabel: 'v1',
          iconStyle: 'lightgreen',
          map: map,
          position: vehicles[0]
        });
        var end = new SimpleMarker({
          iconLabel: 'v2',
          iconStyle: 'lightgreen',
          map: map,
          position: vehicles[1]
        });
        vehicles.push(start, end);
      });
    });
  });

  /**
   * create random order
   */
  function createOrder() {
    return $.ajax({
      url: 'http://carpool.lalamove.com/orders/create-random',
      dataType: 'jsonp',
      jsonp: 'jsonp',
    });
  }

  /**
   * create two random vehicle
   */
  function createVehicles() {
    return $.ajax({
      url: 'http://carpool.lalamove.com/vehicles/random',
      dataType: 'jsonp',
      jsonp: 'jsonp',
    });
  }
}
