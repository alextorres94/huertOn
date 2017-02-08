$(function() {
	initMap();
});

var map;

function initMap() {
  //Inicializamos el mapa y sus controles (es necesário tener un div de id="map") y lo centramos en Valencia por defecto
  //Pedimos la geolocalización al usuario, si la acepta centramos el mapa en su ubicación, sinó mostramos el error por consola
  map = L.map('map-show', {
  	zoomControl: false
  });

  L.control.zoom({
  	position: 'topright',
  	zoomInTitle: 'Acercar',
  	zoomOutTitle: 'Alejar'
  }).addTo(map);

  L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  setMap();

}

function setMap(){

	if($('#Geometry').val() != '') {
		retrieveGeometry($('#Geometry').val());
	}

}

function reverseGeocoding(marker) {
	$.getJSON('https://search.mapzen.com/v1/reverse?api_key=mapzen-9gW9cQ&point.lat='+marker.getLatLng().lat+'&point.lon='+marker.getLatLng().lng+'&size=1&layers=address&boundary.country=ES', function(data, status, xhr) {
		if(data.features[0] == undefined){
			marker.bindPopup('No se han encontrado resultados.').openPopup();
		}else{
			marker.bindPopup(data.features[0].properties.label).openPopup();
			if(data.features[0].properties.locality != null){
				var circle = L.circle([marker.getLatLng().lat, marker.getLatLng().lng], data.features[0].properties.distance*1000).addTo(map);
				$('#Town').attr('value', data.features[0].properties.locality).trigger('change');
				$('#Street').attr('value', data.features[0].properties.street).trigger('change');
				$('#Number').attr('value', data.features[0].properties.housenumber).trigger('change');
				$('#Zipcode').attr('value', data.features[0].properties.postalcode).trigger('change');
				console.log('Número '+data.features[0].properties.housenumber+', Calle '+data.features[0].properties.street+', Localidad '+data.features[0].properties.locality+', CP '+data.features[0].properties.postalcode+', Pais '+data.features[0].properties.country+', Distancia '+data.features[0].properties.distance*1000+' m, Exacto a '+data.features[0].properties.confidence);
			}else{
				var circle = L.circle([marker.getLatLng().lat, marker.getLatLng().lng], data.features[0].properties.distance*1000).addTo(map);
				$('#Town').attr('value', data.features[0].properties.localadmin).trigger('change');
				$('#Street').attr('value', data.features[0].properties.street).trigger('change');
				$('#Number').attr('value', data.features[0].properties.housenumber).trigger('change');
				$('#Zipcode').attr('value', data.features[0].properties.postalcode).trigger('change');
				console.log('Número '+data.features[0].properties.housenumber+', Calle '+data.features[0].properties.street+', Localidad '+data.features[0].properties.localadmin+', Pais '+data.features[0].properties.country+', Distancia '+data.features[0].properties.distance*1000+' m, Exacto a '+data.features[0].properties.confidence);
			}
		}
	});
}

function retrieveGeometry(geometry) {
    //Geometry será el string recuperado de la BBDD
    //Convertimos el string en un objeto JSON eliminando los errores y lo añadimos al mapa
    var geojsonFeature = JSON.parse(geometry.replace(/&quot;/g,'"'));

		console.log(geojsonFeature);

		//Si es un array es porque contiene un punto y un polígono
		if(Object.prototype.toString.call(geojsonFeature) === '[object Array]') {
    	var geojsonFeaturePoint = geojsonFeature[0];
			var geojsonFeaturePolygon = geojsonFeature[1];
			if(geojsonFeaturePoint.geometry.type == 'Point') {
				var marker = L.marker([geojsonFeaturePoint.geometry.coordinates[1], geojsonFeaturePoint.geometry.coordinates[0]], {draggable: false});
				prepareMarker(marker);
			}
			if(geojsonFeaturePolygon.geometry.type == 'Polygon') {
				var layer = L.geoJson(geojsonFeaturePolygon).addTo(map);
			}
		}else {
			if(geojsonFeature.geometry.type == 'Point') {
				var marker = L.marker([geojsonFeature.geometry.coordinates[1], geojsonFeature.geometry.coordinates[0]], {draggable: false});
				prepareMarker(marker);
			}
		}
}

function prepareMarker(marker) {

	marker.addTo(map);

	reverseGeocoding(marker);

	map.setView([marker.getLatLng().lat, marker.getLatLng().lng], 19);
}