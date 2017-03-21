var map;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 22.2793278,
      lng: 114.1628131
    },
    zoom: 11
  });
  new google.maps.KmlLayer({
    map: map,
    url: "http://www.geocodezip.com/geoxml3_test/kml/hkisland.kml"
  });
  var bounds = new google.maps.LatLngBounds();
  console.log(bounds.getSouthWest().lat());
}
