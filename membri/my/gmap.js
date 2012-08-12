var marker;
var map;
var txtCoords;
var txtZoomLvl;

function drawTheMap() {
	txtCoords = document.getElementById('coords');
	txtZoomLvl = document.getElementById('zoomlvl');
	var RoCenter = new google.maps.LatLng(45.780689,24.977572);
	if (txtCoords.value != '' && txtCoords.value != 0 && txtCoords.value != '0') {
		var x = txtCoords.value.split(",");
		var startPos = new google.maps.LatLng(parseFloat(x[0]),parseFloat(x[1]));
		var startZoom = parseInt(txtZoomLvl.value);
	} else {
		var startPos = RoCenter;
		var startZoom = 7;
	}

	var myOptions = {
		zoom: startZoom,
		center: startPos,
		mapTypeId: google.maps.MapTypeId.HYBRID
	}
	map = new google.maps.Map(document.getElementById("locationMap"), myOptions);
	marker = new google.maps.Marker({
		position: startPos, 
		map: map, 
		title:"Pozitioneaza locul de skate",
		draggable:true
	});

	var roSV = new google.maps.LatLng(43.585112,20.217059);
	var roNE = new google.maps.LatLng(48.221695,29.654315);
	var roLimit = new google.maps.LatLngBounds(roSV,roNE);

	google.maps.event.addListener(marker,'dragend',function() {
		if (roLimit.contains(marker.getPosition())) {
			txtCoords.value = marker.getPosition().toUrlValue();
			txtZoomLvl.value = map.getZoom();
		} else {
			txtCoords.value = "0";
		}
	});
	google.maps.event.addListener(map,'zoom_changed',function() {
		txtZoomLvl.value = map.getZoom();
	});
	google.maps.event.addListener(map,'rightclick',function(event) {
		var newpos = event.latLng;
		if (roLimit.contains(newpos)) {
			txtCoords.value = newpos.toUrlValue();
			marker.setPosition(newpos);
		} else {
			txtCoords.value = "0";
		}
	});

}
window.onload = function() { 
drawTheMap();
};

function toggleMapHelp(container) {
	if (container.style.display=="none") {
		container.style.display="inline-block";
	} else {
		container.style.display="none";
	}
}

function toggleCM() {
	var darklayer = document.getElementById('darklayer');
	var button = document.getElementById('darklayer_b');
	if (darklayer.style.display=="none") {
		darklayer.style.display="block";
		button.innerHTML = "Aleg locatia pe harta";
		txtZoomLvl.value = 0;
		txtCoords.value = 0;
		map.setOptions({scrollwheel:false});
	} else {
		darklayer.style.display="none";
		button.innerHTML = "Nu aleg locatia pe harta";
		txtZoomLvl.value = map.getZoom();
		txtCoords.value = marker.getPosition().toUrlValue();
		map.setOptions({scrollwheel:true});
	}
}
