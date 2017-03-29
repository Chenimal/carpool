var base_url = 'http://carpool.lalamove.com/',
  order_colors = ['blue', 'orange', 'green', 'blue', 'orchid'],
  cur_color = 0,
  orders = {},
  vehicles = {};

function initMap() {
  var map = new AMap.Map('container', {
    center: [114.1727589, 22.310816],
    zoom: 11
  });
  map.plugin(["AMap.ToolBar"], function() {
    map.addControl(new AMap.ToolBar());
  });

  $('.create_orders').on('click', function() {
    Object.keys(orders).map(function(k) {
      orders[k][0].setMap(null);
      orders[k][1].setMap(null);
    });
    orders = {};
    var num_orders = 1; //Math.ceil(Math.random() * 5);
    for (var i = 0; i < num_orders; i++) {
      createOrder();
    }
  });
  $('.get_vehicles').on('click', function() {
    getVehicles();
  });
  $('.assign_orders').on('click', function() {
    assignOrders();
  });

  /**
   * create random order
   */
  function createOrder() {
    return $.ajax({
      url: base_url + 'orders/create-random',
      dataType: 'jsonp',
      jsonp: 'jsonp',
    }).done(function(res) {
      console.log(res);
      AMapUI.loadUI(['overlay/SimpleMarker'], function(SimpleMarker) {
        var start = new SimpleMarker({
          iconLabel: 'S',
          iconStyle: order_colors[cur_color % 5],
          map: map,
          position: res.pickup_lng_lat
        });
        var end = new SimpleMarker({
          iconLabel: 'E',
          iconStyle: order_colors[cur_color++ % 5],
          map: map,
          position: res.dropoff_lng_lat
        });
        orders[res.id] = [start, end];
      });
    });
  }

  /**
   * create two random vehicle
   */
  function getVehicles() {
    Object.keys(vehicles).map(function(k) {
      vehicles[k].setMap(null);
    });
    vehicles = {};
    return $.ajax({
      url: base_url + 'vehicles/random',
      dataType: 'jsonp',
      jsonp: 'jsonp',
    }).done(function(data) {
      console.log(data);
      AMapUI.loadUI(['overlay/SimpleMarker'], function(SimpleMarker) {
        vehicles['a'] = new SimpleMarker({
          iconLabel: 'v1',
          iconStyle: 'lightgreen',
          map: map,
          position: data['a']
        });
        vehicles['b'] = new SimpleMarker({
          iconLabel: 'v2',
          iconStyle: 'lightgreen',
          map: map,
          position: data['b']
        });
      });
    });;
  }

  /**
   * submit assign order request
   */
  function assignOrders() {
    var data = {
      order_ids: Object.keys(orders),
      vehicles: Object.keys(vehicles).map(function(k) {
        var position = vehicles[k].getPosition();
        return [position.lng, position.lat];
      })
    };
    $.ajax({
      url: base_url + 'orders/assign',
      dataType: 'jsonp',
      jsonp: 'jsonp',
      data: data
    }).done(function(a, b) {
      console.log(a, b);
    });
  }

}
