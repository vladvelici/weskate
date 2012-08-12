<?php
require_once "../mainfile.php";
if (iMEMBER) redirect("../index.php");
require_once SCRIPTS."header.php";

opentable("Activare cont");

if (isset($_POST['code']) && isset($_POST['url'])) {
	$error=false;
	$url = sqlsafe($_POST['url']);
	if (preg_match('/[^a-zA-Z0-9]/', $url) || dbcount("(user_id)",DB_USERS,"user_profileurl='$url'") || strlen($url) < 4) {
		echo "<div class='notered'>URL-ul este deja folosit, conține caractere invailde sau este prea scurt.</div>";
		$error=true;
	}
	$code = sqlsafe($_POST['code']);
	$result = dbquery("SELECT * FROM ".DB_NEW_USERS." WHERE user_code='$code' LIMIT 1");
	if (!dbrows($result)) {
		echo "<div class='notered'>Cod de activare greșit.</div>";		
		$error=true;
		$code = false;
	} else {
		if (!$error) {
			$data = dbarray($result);
			$result = dbquery("INSERT INTO ".DB_USERS."
					(user_name,user_email,user_joined,user_password,user_hide_email,user_profileurl) VALUES
					('".$data['user_name']."','".$data['user_email']."','".$data['user_datestamp']."','".$data['user_password']."','".$data['user_hide_email']."','$url')");
			$result = dbquery("DELETE FROM ".DB_NEW_USERS." WHERE user_code='$code'");
			redirect("conectare.php?activat");
		}
	}
}

if (!isset($code)) {
	if (isset($_GET['account'])) {
		$code = sqlsafe($_GET['account']);
	} else {
		$code = false;
	}
}
echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Pentru a termina înregistrarea, completează formularul următor:</strong><br /><br />";

echo "<form action='activate.php' method='post'>";
if ($code) {
	echo "<input type='hidden' value='".$code."' name='code' />";
	$result = dbquery("SELECT user_name FROM ".DB_NEW_USERS." WHERE user_code='$code' LIMIT 1");
	if (dbrows($result)) {
		$data=dbarray($result);
		$i=0;
		do {
			$url=urltext($data['user_name']).($i ? $i : "");
			$i++;
		} while(dbcount("(user_id)",DB_USERS,"user_profileurl='$url'"));
	} else {
		echo "<div class='notered'>Cod de validare greșit!</div>"; die();
	}
} else {
	echo "Cod de validare :<br />";
	echo "<input type='text' value='' name='code' /><br /><br />";
	$url = "";
}
add_to_head("<script type='text/javascript' src='http://weskate.ro/membri/register.js'></script>");
echo "URL profil<br />";
echo "<div class='small' style='text-indent:-125px;'>";
echo "http://profil.weskate.ro/<input type='text' value='".$url."' name='url' onkeyup='validateURL(this.value);' onfocus='validateURL(this.value);' />";
echo "</div>";
echo "<span id='urlstatus' style='font-size:12px;font-weight:bold;display:block;padding:3px;height:15px;'></span><span class='small'>Îl poți schimba mai târziu. Doar caracterele alfanumerice sunt permise.</span><br /><br />";
echo "<input type='submit' name='activate' value='Activează-mi contul' />";
echo "</form>";
echo "</div>";

require_once SCRIPTS."footer.php";
?>
