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

function php_urlencode (str) {
str = escape(str);
return str.replace(/[*+\/@]|%20/g,
function (s) {
switch (s) {
case "*": s = "%2A"; break;
case "+": s = "%2B"; break;
case "/": s = "%2F"; break;
case "@": s = "%40"; break;
case "%20": s = "+"; break;
}
return s;
}
);
}

function savePhotoTitle(id,key) {
	var new_title = document.getElementById('c_title'+id).value;
	document.getElementById('photoTitle'+id).innerHTML = new_title;
//	togglePhotoTitle(id);
	ajaxpage('spoturi_ajax.php?new_title='+php_urlencode(new_title)+'&id='+id+'&key='+key,'changeNameS'+id);
}
function deletePhoto(id,code) {
	if (confirm("Esti sigur ca vrei sa stergi poza?")) {
		ajaxpage('spoturi_ajax.php?id='+id+'&delete=true&key='+code,'photo'+id);
	}
}

function togglePhotoTitle(id) {
	if (document.getElementById('changeName'+id).style.display=='none') {
		document.getElementById('changeName'+id).style.display='block';
		document.getElementById('changeNameS'+id).innerHTML = '';
	} else {
		document.getElementById('changeName'+id).style.display='none';
	}
}
