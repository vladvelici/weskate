<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }

function friends($id1,$id2,$status=2) {
	//Returneaza FALSE daca nu sunt prieteni si ID-ul relatiei (rel_id) daca sunt prieteni
	if ($id1==$id2) return false;
	$friend1 = min($id1,$id2);
	$friend2 = max($id1,$id2);

	if ($status!==false) { $status=" AND rel_status=$status"; } else { $status=""; }

	$result = dbquery("SELECT rel_id FROM ".DB_FRIENDS." WHERE friend_one='$friend1' AND friend_two='$friend2'".$status." LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		return $data['rel_id'];
	} else {
		return false;
	}
}

function CanRequestFriend($id1,$id2) {
	if (friends($id1,$id2,false)) {
		return false;
	} else {
		return true;
	}
}


function RequestFriend($user_id) {
	global $userdata;
	if (!CanRequestFriend($user_id,$userdata['user_id'])) { return false; }

	$friend_one = min($userdata['user_id'],$user_id);
	$friend_two = max($userdata['user_id'],$user_id);

	$status = ($friend_one==$userdata['user_id'] ? 0 : 1);

	$result = dbquery("INSERT INTO ".DB_FRIENDS." (friend_one,friend_two,rel_status) VALUES ($friend_one, $friend_two, $status)");
	if ($result) { return true; } else { return false; }
}

function AcceptFriend($friend) {
	global $userdata;
	$id1 = min($friend,$userdata['user_id']);
	$id2 = max($friend,$userdata['user_id']);
	$result = dbquery("UPDATE ".DB_FRIENDS." SET rel_status = '2' WHERE friend_one=$id1 AND friend_two=$id2");
	if ($result) { return true; } else { return false; }
		
}

function DenyFriend($friend) {
	global $userdata;
	$id1 = min($friend,$userdata['user_id']);
	$id2 = max($friend,$userdata['user_id']);
	$deny = ($id1==$userdata['user_id'] ? 3 : 4);
	$result = dbquery("UPDATE ".DB_FRIENDS." SET rel_status = '$deny' WHERE friend_one=$id1 AND friend_two=$id2");
	if ($result) { return true; } else { return false; }
}

function DeleteFriend($id1, $id2) {
	$friend1 = min($id1,$id2);
	$friend2 = max($id1,$id2);
	$result = dbquery("DELETE FROM ".DB_FRIENDS." WHERE friend_one=$friend1 AND friend_two=$friend2");
	if ($result) { 
		return true;
	} else {
		return false;
	}
}

function friendStatus($id1,$id2) {
	
	if ($id1==$id2) return false;
	$friend1 = min($id1,$id2);
	$friend2 = max($id1,$id2);

	$result = dbquery("SELECT rel_status FROM ".DB_FRIENDS." WHERE friend_one='$friend1' AND friend_two='$friend2' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		return $data['rel_status'];
	} else {
		return false;
	}

}






?>

