function ajaxpage(url, div, post, append) {
	if (post === undefined) { post=false; }
	if (append === undefined) { append = false; }
	try {
		xmlhttp=new XMLHttpRequest();
	} catch(e) {
		try {
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			return false;
		}
	}
	if (post) {
		var s = url.indexOf("?");
		var link = url.slice(0,s);
		var params = url.slice(s+1);
	} else {
		var link = url;
	}

	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			if (append==2) {
				document.getElementById(div).innerHTML += xmlhttp.responseText;				
			} else if (append) {
				document.getElementById(div).innerHTML = xmlhttp.responseText + document.getElementById(div).innerHTML;
			} else {
				document.getElementById(div).innerHTML = xmlhttp.responseText;
			}
		}
	}

	xmlhttp.open((post ? "POST" : "GET"),link,true);

	if (post) {
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	}
	xmlhttp.send((post ? params : ""));
}
