<?php
if (!defined("inWeSkateCheck")) { die("Acces respins."); }

function LaFavorite($id,$type,$user = false) {
	if (!$user) {
		if (iMEMBER) {
			global $userdata;
			$user = $userdata['user_id'];
		} else {
			return false;
		}
	}

	$result = dbquery("SELECT fav_id FROM ".DB_FAVORITE." WHERE fav_type='".$type."' AND item_id=".$id." AND fav_user=".$user." LIMIT 1");
	$rows = dbrows($result);

	if ($rows) {
		$data = dbarray($result);
		return $data['fav_id'];
	} else {
		return false;
	}

}

function AddToFav($id,$type) {
	if (!iMEMBER) return false;
	global $userdata;
	$user = $userdata['user_id'];

	if (!LaFavorite($id,$type,$user)) {
		$result = dbquery("INSERT INTO ".DB_FAVORITE." (fav_type,fav_user,item_id) VALUES ('$type','$user','$id')");
		return true;
	} else {
		return false;
	}
}

function DelFromFav($id,$type) {
	if (!iMEMBER) return false;
	global $userdata;
	$favid = LaFavorite($id,$type,$userdata['user_id']);
	if ($favid) {
		$result = dbquery("DELETE FROM ".DB_FAVORITE." WHERE fav_id='".$favid."'");
		return true;
	} else {
		return false;
	}
	
}
?>
