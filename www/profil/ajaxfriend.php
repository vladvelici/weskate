<?php
require_once "../mainfile.php";
require_once "../scripts/sistem_prieteni/functii.php";

if (iMEMBER && isset($_GET['key']) && $_GET['key'] == $_SESSION['user_key']) {
	if (isset($_GET['delete']) && isnum($_GET['delete']) &&	dbcount("(rel_id)",DB_FRIENDS,

"(rel_status=2 AND friend_one=".min($userdata['user_id'],$_GET['delete'])." AND friend_two=".max($userdata['user_id'],$_GET['delete']).") OR
 (rel_status=3 AND friend_one=".$userdata['user_id']." AND friend_two=".$_GET['delete'].")
 OR (rel_status=4 AND friend_one=".$_GET['delete']." AND friend_two=".$userdata['user_id'].")")) {

		$action = DeleteFriend($_GET['delete'],$userdata['user_id']);
		if ($action) {
			if (isset($_GET['profil'])) {
				echo "Prieten șters cu succes.";
			} else {
				echo "<a href=\"javascript:void(0);\" onclick=\"ajaxpage('/ajaxfriend.php?add=".$_GET['delete']."&amp;key=".$_SESSION['user_key']."','friends')\" title=\"Adaug&#259; &icirc;n lista de prieteni\" class=\"optnav\"><span style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend_add.png);padding-left:20px;\">Adaug&#259; la prieteni</span></a>";
			}
		} else {
			echo "Eroare.";
		}
	} else if (isset($_GET['add']) && isnum($_GET['add'])) {
		if (CanRequestFriend($_GET['add'],$userdata['user_id'])) {
			$action = RequestFriend($_GET['add']);
			if ($action) {
				echo "<div style='display:block;padding:4px;'><span style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/friend.png);padding-left:20px;'>A&#351;teptare r&#259;spuns...</span></div>";
			} else {
				echo "Eroare";
			}
		} else {
			echo "Eroare.";
		}
	} else if (isset($_GET['deny']) && isnum($_GET['deny'])) {
		if (dbcount("(rel_id)",DB_FRIENDS,"(rel_status=0 AND friend_two=".$userdata['user_id']." AND friend_one=".$_GET['deny'].") OR (rel_status=1 AND friend_one=".$userdata['user_id']." AND friend_two='".$_GET['deny']."')")) {
			DenyFriend($_GET['deny']);
			echo "prieten refuzat";
		}
	} else if (isset($_GET['accept']) && isnum($_GET['accept'])) {
		if (dbcount("(rel_id)",DB_FRIENDS,"(rel_status=0 AND friend_two=".$userdata['user_id']." AND friend_one=".$_GET['accept'].") OR (rel_status=1 AND friend_one=".$userdata['user_id']." AND friend_two='".$_GET['accept']."')")) {
			AcceptFriend($_GET['accept']);
			echo "prieten acceptat";
		}
	}
} else {
	echo "Doar pentru membri.";
}

mysql_close();
?>
