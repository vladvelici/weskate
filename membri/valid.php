<?php
require_once "../mainfile.php";

if (isset($_POST['user'])) {
	$user = htmlsafe($_POST['user']);
	if (strlen($user) < 3) {
		echo "<img src='http://img.weskate.ro/uncheck.gif' title='Nume de utilizator prea scurt' alt='Nume de utilizator prea scurt' />";
	} else {
		$unic = dbcount("(user_id)",DB_USERS,"user_name='$user'");
		if ($unic) { 
			echo "<img src='http://img.weskate.ro/uncheck.gif' title='Nume de utilizator deja folosit' alt='Nume de utilizator deja folosit' />";
		} else {
			echo "<img src='http://img.weskate.ro/check.gif' title='Nume de utilizator bun' alt='Nume de utilizator bun' />";
		}
	}
} else if (isset($_POST['pass'])) {
	$percent = passwordStrengh($_POST['pass']);
	if ($percent >= 75) {
		$color = "#0F0";
	} elseif ($percent >= 50) {
		$color = "#DDFF00";
	} elseif ($percent >= 25) {
		$color = "#FF9900";
	} else {
		$color = "#F00";
	}
	echo "<span><img src='http://img.weskate.ro/".($percent < 26 ? "un" : "")."check.gif' alt='".($percent>25 ? "acceptata" : "prea slaba")."'/></span><span class='smallround passStr'><span style='width:$percent%;background-color:$color;' class='smallround'></span></span><span style='font-size:10px;'>$percent%</span>";
} else if (isset($_POST['email'])) {
	require_once SCRIPTS."validate_email.php";
	if (validEmail($_POST['email'])) {
		$exista = dbcount("(user_id)",DB_USERS,"user_email='".sqlsafe($_POST['email'])."'".(iMEMBER ? " AND user_id!=".$userdata['user_id'] : ""));
		if (!$exista) {
			echo "<img src='http://img.weskate.ro/check.gif' title='E-mail acceptat' alt='E-mail acceptat' />";
		} else {
			echo "<img src='http://img.weskate.ro/uncheck.gif' title='E-mail folosit deja' alt='E-mail folosit deja' />";
		}
	} else {
		echo "<img src='http://img.weskate.ro/uncheck.gif' title='E-mail invalid' alt='E-mail invalid' />";
	}
} else if (isset($_POST['url'])) {
	$url = sqlsafe($_POST['url']);
	if (preg_match('/[^a-zA-Z0-9]/', $url)) {
		echo "<img src='http://img.weskate.ro/uncheck.gif' title='caractere invalide' alt='caractere invalide' /> Conține caractere invalide.";
		die();
	}
	if (dbcount("(user_id)",DB_USERS,"user_profileurl='$url'".(iMEMBER ? " AND user_id!=".$userdata['user_id'] : ""))) {
		echo "<img src='http://img.weskate.ro/uncheck.gif' title='folosit deja' alt='folosit deja' /> Este deja folosit.";
		die();
	}
	if (strlen($url) < 3) {
		echo "<img src='http://img.weskate.ro/uncheck.gif' title='prea scurt' alt='prea scurt' /> Minim 3 caractere.";
		die();
	}
	echo "<img src='http://img.weskate.ro/check.gif' title='ok' alt='ok' /> Disponibil.";
}

mysql_close();
?>
