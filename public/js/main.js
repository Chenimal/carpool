var base_url = 'http://carpool.lalamove.com/',
  order_colors = ['blue', 'orange', 'green', 'red', 'orchid'],
  cur_color = 0,
  orders = {},
  vehicles = {},
  line_arr_a = [],
  line_arr_b = [];

function initMap() {
  var map = new AMap.Map('container', {
    center: [114.1727589, 22.310816],
    zoom: 11
  });
  // draw path
  var passed_polyline_a = new AMap.Polyline({
    map: map,
    // path: lineArr,
    strokeColor: "#F00",
    // strokeOpacity: 1,     //线透明度
    strokeWeight: 3, //线宽
    // strokeStyle: "solid"  //线样式
  });
  var passed_polyline_b = new AMap.Polyline({
    map: map,
    // path: lineArr,
    strokeColor: "#00A",
    // strokeOpacity: 1,     //线透明度
    strokeWeight: 3, //线宽
    // strokeStyle: "solid"  //线样式
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
    var num_orders = 5; //Math.ceil(Math.random() * 5);
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
  $('.draw_paths').on('click', function() {
    vehicles['a'].moveAlong(line_arr_a, 10000);
    vehicles['b'].moveAlong(line_arr_b, 10000);
  });
  $('.start_over').on('click', function() {
    Object.keys(orders).map(function(k) {
      orders[k][0].setMap(null);
      orders[k][1].setMap(null);
    });
    orders = {};
    Object.keys(vehicles).map(function(k) {
      vehicles[k].setMap(null);
    });
    vehicles = {};
    map.remove(passed_polyline_a);
    map.remove(passed_polyline_b);
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
        vehicles['a'].on('moving', function(e) {
          passed_polyline_a.setPath(e.passedPath);
        })
        vehicles['b'].on('moving', function(e) {
          passed_polyline_b.setPath(e.passedPath);
        })
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
    }).done(function(solution) {
      console.log(solution);
      var criteria = 'duration';
      var sequence = solution[criteria]['sequence'];
      // vehicle_a
      line_arr_a = [];
      line_arr_a.push(vehicles['a'].getPosition());
      for (var i = 0; i < sequence[0].length; i++) {
        var index = sequence[0][i].split('_');
        line_arr_a.push(orders[index[0]][index[1] == 'start' ? 0 : 1].getPosition());
      }
      console.log(line_arr_a);
      // vehicle_b
      line_arr_b = [];
      line_arr_b.push(vehicles['b'].getPosition());
      for (var i = 0; i < sequence[1].length; i++) {
        var index = sequence[1][i].split('_');
        line_arr_b.push(orders[index[0]][index[1] == 'start' ? 0 : 1].getPosition());
      }
      console.log(line_arr_b);
    });
  }

}
