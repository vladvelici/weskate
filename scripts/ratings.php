<?php
if (!defined("inWeSkateCheck")) die("Access respins");

function showRatings($type,$id,$class="lightonhoverF smallround header-link-m",$width="100%") {
	//text = true => da/nu; text = false => imi place/nu-mi place
	echo "<div style='width:$width;margin:1px auto 1px auto;' id='ratings-$type-$id'>";
	if (iMEMBER) {
	global $userdata;
	if (!dbcount("(rating_id)",DB_RATINGS,"rating_item_id=$id AND rating_type='$type' AND rating_user=".$userdata['user_id'])) {

	add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/rate.js'></script>");
	echo "<div style='margin:1px;'>";
	echo "<a href='javascript:rate(\"$type\",$id,1,\"".$_SESSION['user_key']."\");' class='$class' style='display:inline-block;padding:4px 4px 4px 18px;background-image:url(http://img.weskate.ro/check.gif);background-repeat:no-repeat;background-position:2px center;margin:2px;'>Îmi place</a>";
	echo "<a href='javascript:rate(\"$type\",$id,0,\"".$_SESSION['user_key']."\");' class='$class' style='display:inline-block;padding:4px 4px 4px 18px;background-image:url(http://img.weskate.ro/uncheck.gif);background-repeat:no-repeat;background-position:2px center;'>Nu-mi place</a>";
	echo "</div>";

	} else {
		echo "<div style='margin:1px;'>Ai votat deja. Mulțumim!</div>";
	}
	}
	if ($stats = getRatingStats($type,$id)) {
		list($nota,$likes,$dislikes,$color) = $stats;
		echo "<div style='margin:1px;height:10px;border:1px solid #333;background-color:".($nota==0 && $dislikes>0 ? "#c00" : "#fff").";'><div style='width:$nota%;height:10px;background-color:$color;background-image:url(http://img.weskate.ro/line.png);background-repeat:repeat;'></div></div>";
		echo "<div style='font-size:9px;margin:1px;'>$nota% ($likes da/$dislikes nu)</div>";
	} else {
		echo "<div>Nici un vot.</div>";
	}
	echo "</div>";

}
function getRatingStats($type,$id) {
	$result = dbquery("SELECT count(rating_id) AS voturi, avg(rating_vote) AS nota FROM ".DB_RATINGS." WHERE rating_item_id=$id AND rating_type='$type'");
	if (dbrows($result)) {
		$data = dbarray($result);
		$nota = round($data['nota'] * 100); //get percent
		if ($nota >= 75) {
			$color = "#0C0";
		} elseif ($nota >= 50) {
			$color = "#AACC00";
		} elseif ($nota >= 25) {
			$color = "#CC6600";
		} else {
			$color = "#C00";
		}
		$dislikes = dbcount("(rating_id)",DB_RATINGS,"rating_item_id=$id AND rating_type='$type' AND rating_vote=0");
		$likes = $data['voturi']-$dislikes;
		return array($nota,$likes,$dislikes,$color);
	} else {
		return false;
	}
}
?>
