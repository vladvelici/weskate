<?php
require_once "../mainfile.php";
if (!iMEMBER) die("Doar pentru membri.");
if (isset($_GET['type']) && isset($_GET['id']) && isnum($_GET['id']) && isset($_GET['vote'])) {
	if ($_GET['key'] != $_SESSION['user_key']) {
		die("Acces respins.");
	}
	if ($_GET['vote'] == 1 || $_GET['vote'] == 0) {
		$vote = $_GET['vote'];
	} else {
		die("Vot invalid.");
	}
	$type = sqlsafe($_GET['type']);
	if (strlen($type) > 1) {
		die("Tip invalid.");
	}
	$id = $_GET['id'];
	if (dbcount("(rating_id)",DB_RATINGS,"rating_item_id=$id AND rating_type='$type' AND rating_user=".$userdata['user_id'])) {
		die("Ai votat deja!");
	}
	$result = dbquery("INSERT INTO ".DB_RATINGS." (rating_item_id,rating_type,rating_vote,rating_user) VALUES ($id,'$type',$vote,".$userdata['user_id'].")");
	//showing the new stats and the "thank you" message:
	require_once "ratings.php";
	echo "<div style='margin:1px;'>Mulțumim pentru părere!</div>";
	if ($stats = getRatingStats($type,$id)) {
		list($nota,$likes,$dislikes,$color) = $stats;
		echo "<div style='margin:1px;height:10px;border:1px solid #333;background-color:".($nota==0 && $dislikes>0 ? "#c00" : "#fff").";'><div style='width:$nota%;height:10px;background-color:$color;background-image:url(http://img.weskate.ro/line.png);background-repeat:repeat;'></div></div>";
		echo "<div style='font-size:9px;margin:1px;'>$nota% ($likes da/$dislikes nu)</div>";
	} else {
		echo "<div>Nici un vot.</div>";
	}
}

mysql_close();
?>
