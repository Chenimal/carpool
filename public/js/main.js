var btns = $('.btn'),
  base_url = 'http://carpool.lalamove.com/',
  order_colors = ['blue', 'orange', 'green', 'red', 'orchid'],
  cur_color = 0,
  orders = {},
  vehicles = {},
  has_orders = false,
  has_vehicles = false,
  has_assigned = false,
  line_arr_a = [],
  line_arr_b = [],
  passed_polyline = {};

function initMap() {
  var map = new AMap.Map('container', {
    center: [114.127439, 22.3746645],
    zoom: 11
  });

  map.plugin(["AMap.ToolBar"], function() {
    map.addControl(new AMap.ToolBar());
  });

  // user interaction
  $('.create_orders').on('click', function() {
    btns.addClass('disabled');
    removeOrders();
    var num_orders = 1; //Math.ceil(Math.random() * 5);
    createOrders(num_orders).done(function() {
      has_orders = true;
      $('.create_orders, .get_vehicles').removeClass('disabled');
      if (has_orders && has_vehicles) {
        $('.assign_orders').removeClass('disabled');
      }
    })
  });
  $('.get_vehicles').on('click', function() {
    btns.addClass('disabled');
    removeVehicles();
    removePassedLine();
    getVehicles().done(function() {
      has_vehicles = true;
      $('.create_orders, .get_vehicles').removeClass('disabled');
      if (has_orders && has_vehicles) {
        $('.assign_orders').removeClass('disabled');
      }
    });
  });
  $('.assign_orders').on('click', function() {
    btns.addClass('disabled');
    assignOrders().done(function() {
      btns.removeClass('disabled');
      vehicles['a'].moveAlong(line_arr_a, 10000);
      vehicles['b'].moveAlong(line_arr_b, 10000);
    });
  });
  $('.start_over').on('click', function() {
    $('start_over, .assign_orders').addClass('disabled');
    removeOrders();
    removeVehicles();
    removePassedLine();
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
   * create multiple orders and return promise when all finished
   */
  function createOrders(num) {
    var promise = $.Deferred();
    var remain = num;
    for (var i = 0; i < num; i++) {
      createOrder().done(function() {
        remain--;
        if (remain <= 0) {
          promise.resolve();
        }
      });
    }
    return promise.promise();
  }

  /**
   * create two random vehicle
   */
  function getVehicles() {
    return $.ajax({
      url: base_url + 'vehicles/random',
      dataType: 'jsonp',
      jsonp: 'jsonp',
    }).done(function(data) {
      return AMapUI.loadUI(['overlay/SimpleMarker'], function(SimpleMarker) {
        Object.keys(data).map(function(k) {
          vehicles[k] = new SimpleMarker({
            iconLabel: 'V' + k,
            iconStyle: 'lightgreen',
            map: map,
            position: data[k]
          });
          // draw path
          passed_polyline[k] = new AMap.Polyline({
            map: map,
            // path: lineArr,
            strokeColor: "#F00",
            strokeOpacity: 0.6,
            strokeWeight: 3,
          });
          vehicles[k].on('moving', function(e) {
            passed_polyline[k].setPath(e.passedPath);
          });
        });
      });
    });
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
    return $.ajax({
      url: base_url + 'orders/assign',
      dataType: 'jsonp',
      jsonp: 'jsonp',
      data: data
    }).done(function(solution) {
      has_assigned = true;
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
      /*AMap.service('AMap.Driving', function() { //回调函数
        //实例化Driving
        if (line_arr_a.length > 1) {
          var driving_a = new AMap.Driving({
            map: map,
            city: '香港'
          });
          driving_a.search(line_arr_a[0], line_arr_a[line_arr_a.length - 1], {}, function(status, code) {
            console.log(status, code);
          });
        }
        if (line_arr_b.length > 1) {
          var driving_b = new AMap.Driving({
            map: map,
            city: '香港'
          });
          driving_b.search(line_arr_b[0], line_arr_b[line_arr_b.length - 1], {}, function(status, code) {
            console.log(status, code);
          });
        }
      });*/
    });
  }

  /**
   * remove orders from map
   */
  function removeOrders() {
    Object.keys(orders).map(function(k) {
      orders[k][0].setMap(null);
      orders[k][1].setMap(null);
    });
    has_orders = false;
    orders = {};
  }
  /**
   * remove vehicles from map
   */
  function removeVehicles() {
    Object.keys(vehicles).map(function(k) {
      vehicles[k].setMap(null);
    });
    has_vehicles = false;
    vehicles = {};
  }
  /**
   * remove passed line from map
   */
  function removePassedLine() {
    Object.keys(passed_polyline).map(function(k) {
      map.remove(passed_polyline[k]);
    });
    passed_polyline = {};
    has_assigned = false;
  }
}
