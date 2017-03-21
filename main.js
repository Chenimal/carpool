var map;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 22.310816,
      lng: 114.1727589
    },
    zoom: 11
  });
  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    var ne = bounds.getNorthEast();
    var sw = bounds.getSouthWest();
    console.log(ne.lat(), ne.lng(), sw.lat(), sw.lng());
    //do whatever you want with those bounds
  });
}
