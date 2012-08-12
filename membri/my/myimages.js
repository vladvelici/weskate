var cPage=0;
var page_request=false;
var imgloaded = [1,2,3];

imgloaded[1] = false;
imgloaded[2] = false;
imgloaded[3] = false;

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

function changePage(page) {
	//pag1 album atasat | pag2 personal | pag3 search
	if (cPage!=page) {
		if (page >= 1 && page <= 3) {
			cPage=page;
			if (imgloaded[page] == false) {
				if (page==1 && document.getElementById('albm').value=='0') {
					document.getElementById('images_'+page).innerHTML = "<div style='text-align:center;font-weight:bold;font-size:16px;padding:7px;'>Nici un album atasat!<br /><span style='font-size:13px;'>Nu ai atasat nici un album la articol inca.</span>";
					document.getElementById('att').style.color='#900';
					document.getElementById('att').style.backgroundColor = '#fcc';
				} else {
					changePageContent(page,'jedit.php?p='+page+(page==1 ? '&id=' + document.getElementById('albm').value : ''));
				}
				imgloaded[page]=true;
			}
			for (i=1;i<=3;i=i+1) {
				document.getElementById('images_'+i).className= (i==page ? "vizibil" : "ascuns") ; //pag1 album relevant
				document.getElementById('images_B'+i).style.fontWeight = (i==page ? 'bold' : 'normal');
				document.getElementById('images_B'+i).style.color = (i==page ? '#000' : '#779');
			}
		}
		if (page!=1) {
			document.getElementById('att').style.color='#555';
			document.getElementById('att').style.backgroundColor = '#f7f7f7';
		} else if (document.getElementById('albm').value=='0') {
			document.getElementById('att').style.color='#900';
			document.getElementById('att').style.backgroundColor = '#fcc';
			
		}
	}
}

function changePageContent(page,url) {
	page_request.onreadystatechange=function(){
		if (page_request.readyState == 4) {
			if (page_request.status==200 || window.location.href.indexOf("http")==-1) {
				if (page==1 && (page_request.responseText == '001' || page_request.responseText == '002')) {
					if (page_request.responseText == '001') {
						document.getElementById('images_'+page).innerHTML = "<div style='text-align:center;font-weight:bold;font-size:16px;padding:7px;'>Albumul este gol! <br /><span style='font-size:12px;'>Nici o fotografie nu a fost gasita in albumul atasat.</span><br /> <br /><a href='javascript:void(0);' onclick='changePageContent("+page+",\""+url+"\")'>Verifica din nou</a></div>";

					} else {
						document.getElementById('images_'+page).innerHTML = "<div style='text-align:center;font-weight:bold;font-size:16px;padding:7px;'>Nici un album atasat!<br /><span style='font-size:13px;'>Nu ai atasat nici un album la articol inca.</span>";
						document.getElementById('att').style.color='#900';
						document.getElementById('att').style.backgroundColor = '#fcc';
					}
				} else {
					document.getElementById('images_'+page).innerHTML = page_request.responseText;
					document.getElementById('att').style.color='#555';
					document.getElementById('att').style.backgroundColor = '#f7f7f7';
				}
			} else {
				document.getElementById('images_'+page).innerHTML = "<div style='text-align:center;font-weight:bold;font-size:16px;padding:7px;'>Eroare! <br /><span style='font-weight:normal;font-size:10px;'>(HTTP "+page_request.status+")</span><br /> <br /><a href='javascript:void(0);' onclick='changePageContent("+page+",\""+url+"\")'>Incearca din nou</a></div>";
			}
		} else {
			document.getElementById('images_'+page).innerHTML = "<img align='center' src='http://img.weskate.ro/ajax_loading.gif' alt='Se incarca...' style='margin:10px;border:0pt none;vertical-align:middle;' />";
		}
	}
	getAjaxResult(url);
}
function refreshPageOne(v) {
	if (cPage==1) {
		changePageContent(1,'jedit.php?p=1&id=' + v);
		imgloaded[1]=true;
	} else {
		imgloaded[1]=false;
	}
}

function getAjaxResult(url){
	page_request.open('GET', url, true)
	page_request.send(null)
}

function openAlbum(id) {
	ajaxpage('jedit.php?p=2&id='+id,'images_2');
}

function addImage(img, alt) {
    tinyMCE.execInstanceCommand("myContent","mceInsertContent",false,"<img src='"+img+"' alt='"+alt+"' style='margin:3px;' align='left' />");
}



