function judet(id) {
	ajaxpage('ajaxDivOnMap.php?jud='+id,'showCities');
	document.getElementById('map_overlay').className='vizibil';
}
function closeJudet() {
	document.getElementById('map_overlay').className='ascuns';
}
