var state=false;
function toggleMyContainer() {
	if (state) {
		document.getElementById('myContainer').style.display='none';
		document.getElementById('myContainerB').style.borderWidth='0px 0px 2px 0px';
		document.getElementById('myContainerBa').innerHTML='&darr;';
		state=false;
	} else {
		document.getElementById('myContainer').style.display='block';
		document.getElementById('myContainerB').style.borderWidth='0px';
		document.getElementById('myContainerBa').innerHTML='&uarr;';
		state=true;
	}
}
