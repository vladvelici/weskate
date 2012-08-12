<?php
require_once "../../mainfile.php";

if (iMEMBER && isset($_GET['id']) && isnum($_GET['id']) && checkMyAccess('L',$_GET['id']) && isset($_GET['key']) && $_GET['key']==$_SESSION['user_key']) {
	if (isset($_GET['new_title'])) {
		$newTitle = urldecode($_GET['new_title']);
		$newTitle = trim(htmlsafe($newTitle));
		$result = dbquery("UPDATE ".DB_SPOT_PHOTOS." SET photo_title='$newTitle' WHERE photo_id='".$_GET['id']."'");
		echo "<img src='http://img.weskate.ro/check.gif' alt='error' style='border:0pt none; vertical-align:middle;'/>";
	} else if (isset($_GET['delete'])) {
		$result = dbquery("SELECT p.photo_file,c.city_name,s.spot_thumb,s.spot_id FROM ".DB_SPOT_PHOTOS." p
				LEFT JOIN ".DB_SPOT_ALBUMS." s ON s.spot_id=p.photo_spot
				LEFT JOIN ".DB_CITIES." c ON s.spot_city=c.city_id
				WHERE photo_id=".$_GET['id']."");
		if ($result) {
			$data = dbarray($result);
			$notify = "";
			if ($data['spot_thumb'] == $data['photo_file']) {
				$getAnother = dbquery("SELECT photo_file FROM ".DB_SPOT_PHOTOS." WHERE photo_spot='".$data['spot_id']."' AND photo_id!='".$_GET['id']."' ORDER BY photo_datestamp DESC LIMIT 0,1");
				if (dbrows($getAnother)) {
					$getThumb = dbarray($getAnother);
					$newThumb = $getThumb['photo_file'];
				} else {
					$newThumb = "";
				}
				$result = dbquery("UPDATE ".DB_SPOT_ALBUMS." SET spot_thumb='$newThumb' WHERE spot_id='".$data['spot_id']."'");
				$notify = "";
			}
			$result = dbquery("DELETE FROM ".DB_SPOT_PHOTOS." WHERE photo_id='".$_GET['id']."'");
			$folder = IMAGES."spoturi/".urltext($data['city_name'])."/";
			@unlink($folder.$data['photo_file']);
			@unlink($folder."thumbs/".$data['photo_file']);
			echo "<img src='http://img.weskate.ro/check.gif' alt='error' style='border:0pt none; vertical-align:middle;' /><br /><strong>Poza a fost stearsa cu succes.</strong>";
		} else {
			echo "<img src='http://img.weskate.ro/uncheck.gif' alt='error' style='border:0pt none; vertical-align:middle;'/><br />Date de intrare invalide.";
		}

		
	} else {
		echo "<img src='http://img.weskate.ro/uncheck.gif' alt='error' style='border:0pt none; vertical-align:middle;'/><br />Date de intrare invalide";
	}
} else {
	echo "<img src='http://img.weskate.ro/uncheck.gif' alt='error' style='border:0pt none; vertical-align:middle;'/>";
}
mysql_close();
?>
