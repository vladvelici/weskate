var selField = "";
function validateEmail(mail) {
	ajaxpage('/membri/valid.php?email='+mail,'email_status',true);
}

function usernameCheck(user) {
	ajaxpage('/membri/valid.php?user='+user,'username_status',true);
}
function passCheck(pass) {
	ajaxpage('/membri/valid.php?pass='+pass,'password_status',true);
}
function pass2() {
	var pass1 = document.getElementById('password').value;
	var pas2 = document.getElementById('passwordCheck').value;
	if (pass1==pas2) {
		document.getElementById('passwordCheck_status').innerHTML = '<img src=\'http://img.weskate.ro/check.gif\' />';
	} else {
		document.getElementById('passwordCheck_status').innerHTML = '<img src=\'http://img.weskate.ro/uncheck.gif\' alt="parolele nu se potrivesc" title="parolele nu se potrivesc" />';
	}
}

function setclass(x) {
	refreshform();
	document.getElementById(x+'_div').className='reg_field_sel';
	document.getElementById(x).focus();
	selField = x;
}
function setfocus(x) {
	document.getElementById(x).focus();
}

function refreshform() {
	if (selField!="") {
		document.getElementById(selField+'_div').className='reg_field';
	}
}
function validateURL(url) {
	ajaxpage('/membri/valid.php?url='+url,'urlstatus',true);
}
