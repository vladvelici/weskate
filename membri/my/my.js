var cei="";
var newscontent="";
var articlecontent="";
var page_request=false;
//initialize ajax:
	if (window.XMLHttpRequest) { // if Mozilla, Safari etc
		page_request = new XMLHttpRequest();
	} else if (window.ActiveXObject) { // if IE
		try {
			page_request = new ActiveXObject("Msxml2.XMLHTTP")
		}
		catch (e){
			try{
				page_request = new ActiveXObject("Microsoft.XMLHTTP")
			}
		catch (e){}
		}
	}

function showedit(ce) {
	if (cei==ce) {
		if (document.getElementById('ciorne').style.display == 'block') {
			document.getElementById('ciorne').style.display = 'none';
			document.getElementById('ciorne-txt').style.visibility='hidden';
		} else {
			document.getElementById('ciorne').style.display='block';
			document.getElementById('ciorne-txt').style.visibility='visible';
		}
	} else {
		if (ce=="a") {
			cei=ce;
			document.getElementById('ciorne-txt').style.visibility='visible';
			document.getElementById('ciorne').style.display = 'block';
			if (articlecontent!="") {
				document.getElementById('ciorne').innerHTML = articlecontent;
			} else {
				page_request.onreadystatechange=function(){
					if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)) {
						articlecontent = page_request.responseText;
						document.getElementById('ciorne').innerHTML = articlecontent;
					} else {
						document.getElementById('ciorne').innerHTML = "<img src='http://img.weskate.ro/ajax_loading.gif' style='' alt='Se incarca...' style='border:0pt none;vertical-align:middle;'/>";
					}
				}
				getAjaxResult('ciorne.php?t=a');
			}
		} else if (ce=="n") {
			cei=ce;
			document.getElementById('ciorne-txt').style.visibility='visible';
			document.getElementById('ciorne').style.display = 'block';
			if (newscontent!="") {
				document.getElementById('ciorne').innerHTML = newscontent;
			} else {
				page_request.onreadystatechange=function(){
					if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)) {
						newscontent = page_request.responseText;
						document.getElementById('ciorne').innerHTML = newscontent;
					} else {
						document.getElementById('ciorne').innerHTML = "<img src='http://img.weskate.ro/ajax_loading.gif' style='' alt='Se incarca...' style='border:0pt none;vertical-align:middle;'/>";
					}
				}
				getAjaxResult('ciorne.php?t=n');
			}
		} else {
			cei="";
			document.getElementById('ciorne').style.display = 'none';
			document.getElementById('ciorne-txt').style.visibility='hidden';
		}
	}
}
function getAjaxResult(url){
	page_request.open('GET', url, true)
	page_request.send(null)
}
