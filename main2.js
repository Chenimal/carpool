var markers = [];

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
      var start = new AMap.Marker({
        icon: "http://webapi.amap.com/theme/v1.3/markers/n/mark_b.png",
        position: res.pickup_lat_lng
      });
      start.setMap(map);
      var end = new AMap.Marker({
        icon: "http://webapi.amap.com/theme/v1.3/markers/n/mark_b.png",
        position: res.dropoff_lat_lng
      });
      end.setMap(map);
      markers.push(start, end);
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
}
