
function changeCity() {
	document.getElementById('change_local').className='vizibil';
	showMap();
}
function closeChangeCity() {
	document.getElementById('change_local').className='ascuns';
}
function judet(id) {
	ajaxpage('/stiri/ajaxDivOnMap.php?jud='+id,'citylist');
	showCityList();
}
function showMap() {
	document.getElementById('map').className='vizibil';
	document.getElementById('citylist').className='ascuns';
	document.getElementById('change_local').style.left='-250px';
	document.getElementById('change_local').style.width='431px';
}
function showCityList() {
	document.getElementById('map').className='ascuns';
	document.getElementById('citylist').className='vizibil';
	document.getElementById('change_local').style.left='-25px';
	document.getElementById('change_local').style.width='200px';
}
