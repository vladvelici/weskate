<?php
require_once "../mainfile.php";
$CuloarePagina = "mov";
require_once SCRIPTS."header.php";

if (isset($_GET['spot_id']) && isnum($_GET['spot_id']) && dbcount("(spot_id)",DB_SPOT_ALBUMS,"spot_id='".$_GET['spot_id']."'")) {
	$result = dbquery(
		"SELECT s.*,c.city_name,j.city_name AS jud_name,us.user_name,us.user_profileurl FROM ".DB_SPOT_ALBUMS." s
		LEFT JOIN ".DB_CITIES." c ON c.city_id=s.spot_city
		LEFT JOIN ".DB_CITIES." j ON c.city_judet=j.city_id
		LEFT JOIN ".DB_USERS." us ON us.user_id=s.spot_user
		WHERE spot_id='".$_GET['spot_id']."'");
	$data = dbarray($result);
	$URLcorect = "/prin-tara/".urltext($data['city_name']).".".$data['spot_city']."/".urltext($data['spot_title']).".".$data['spot_id'];
	if (PAGE_REQUEST != $URLcorect) { redirect($URLcorect);	}

	opentable("Prin Tara");

	$result=dbquery("UPDATE ".DB_SPOT_ALBUMS." SET spot_views=(spot_views+1) WHERE spot_id='".$_GET['spot_id']."'");

	set_title(fixRoChars($data['spot_title'])." - Loc de skate &icirc;n ".$data['city_name']." - we Skate");
	set_meta("description",killRoChars($data['spot_title']).", loc de skate in ".$data['city_name']);
	set_meta("keywords",keywordize($data['spot_title'].",".$data['city_name'].",locuri de skate"));

	echo "<table cellspacing='0' cellpadding='4' width='100%' style='border-top:1px dotted #999;border-bottom:1px dotted #999;'><tr>";
	echo "<td width='200' style='white-space:nowrap;font-size:14px;'>";
	echo "<img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /><a href='/prin-tara/'>Prin &#355;ar&#259;</a>";
	echo "<br />";
	if ($data['jud_name']) {
		echo "<img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' style='margin-left:10px;' /> Jude&#355; : ".$data['jud_name']."<br />";
	}
	echo "<img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' style='margin-left:20px;' />
	<a href='/prin-tara/".urltext($data['city_name']).".".$data['spot_city']."'>".$data['city_name']."</a>";
	echo "</td><td>";
	echo "<div  style='line-height:160%;'>loc de skate:<br/><span class='capmain_color' style='font-size:35px;font-weight:bold;'>".$data['spot_title']."</span></div><br />";
	echo "</td>";
	echo "<td width='1%' style='white-space:nowrap;font-size:14px;'>adăugat de <a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a><br />".showdate("ago",$data['spot_datestamp'])."</td>";
	echo "</tr></table>\n";

	echo "<div class='flright' style='width:250px'>";
	if ($data['spot_description']) {
		openside("Descriere","mov");
			echo nl2br($data['spot_description']);
		closeside();
	}

	$coordsArray = explode(",",$data['spot_coords']);
	if ($data['spot_adress'] || is_array($coordArray)) {
		openside("Locație, instrucțiuni","mov");
			echo ($data['spot_adress'] ? $data['spot_adress']."<hr />" : "");

			if (is_array($coordsArray)) {
				echo "<div class='ascuns'>";
				//google map mare
				echo "</div>";
				echo "<img src='http://maps.google.com/maps/api/staticmap?center=".$coordsArray[0].",".$coordsArray[1]."&amp;zoom=".$coordsArray[2]."&amp;size=231x200&amp;maptype=hybrid&amp;markers=color:red|".$coordsArray[0].",".$coordsArray[1]."&amp;sensor=false' alt='".$data['spot_title']." pe harta' />";
			}
		closeside();
	}
	openside("Popularitate","mov");
		$all = dbcount("(user_id)",DB_USERS,"user_status='0'");
		$spot = dbcount("(fav_id)",DB_FAVORITE,"item_id='".$_GET['spot_id']."' AND fav_type='S'");
		$percent = (float) (($spot*100) / $all);
		$percent = round($percent,2);
		if ($percent) {
			echo "<span style='font-size:15px;'><strong>$percent</strong>%</span> din utilizatorii înregistrați frecventează acest loc:</br >";
			$result2 = dbquery("SELECT us.user_name,us.user_profileurl FROM ".DB_FAVORITE." f
					LEFT JOIN ".DB_USERS." us ON f.fav_user=us.user_id
					WHERE fav_type='S' AND item_id='".$_GET['spot_id']."' ORDER BY user_points DESC");
			$comma = "";
			while ($data2=dbarray($result2)) {
				echo $comma."<a href='http://profil.weskate.ro/".$data2['user_profileurl']."'>".$data2['user_name']."</a>";
				if (!$comma) $comma=",";
			}
		} else {
			echo "<em>Nici un membru WeSkate nu frecventează acest loc.</em>";
		}
		if (iMEMBER) {
			echo "<hr />";
			require_once SCRIPTS."sistem_favorite/functii.php";
			$id = $_GET['spot_id'];	$type = "S"; $divid="favorite".$id.$type;
			echo "<div id='$divid' style='white-space:nowrap;display:block;'>";
			if (LaFavorite($id,$type)) {
				echo "<a title=\"Nu mai vin aici\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('/scripts/sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=9','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/circle_delete.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"header-link-m spoturi\">Nu mai frecventez acest loc</a>";
			} else {
				echo "<a title=\"Vin des aici\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('/scripts/sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=9','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/new.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class='header-link-m spoturi'>Frecventez acest loc</a>";
			}
			echo "</div>";
		}
	closeside();
	echo "<div style='padding:5px 6px 5px 6px;'>";
	require_once SCRIPTS."ratings.php";
	showratings("S", $_GET['spot_id'], "spoturi smallround header-link-m");
	echo "</div>";
	echo "</div>";


	$myPhotos = dbcount("(photo_id)",DB_SPOT_PHOTOS,"photo_spot='".$_GET['spot_id']."'");
	echo "<div style='margin-top:7px;width:700px;float:left;'>";
	if ($myPhotos) {

		$resultp = dbquery("SELECT photo_file,photo_title,photo_id FROM ".DB_SPOT_PHOTOS." WHERE photo_spot='".$_GET['spot_id']."' ORDER BY photo_datestamp DESC");
		echo "<div style='font-size:17px;display:block;font-weight:bold;padding:5px;' class='capmain_color'>Poze din acest loc</div>";
		$i=0;
		while ($datap = dbarray($resultp)) {
			$photo_id=$datap['photo_id'];
			if ($i%5==0 && $i!=0) { echo "<div style='clear:both;'></div>"; }

			if ($datap['photo_file'] && file_exists(IMAGES."spoturi/".urltext($data['city_name'])."/thumbs/".$datap['photo_file'])) {
				echo "<div style='text-align:center;width:130px;' class='flleft'>";
				$spot_size=@getimagesize(IMAGES."spoturi/".urltext($data['city_name'])."/".$datap['photo_file']);
				echo "<a href=\"javascript:;\" onclick=\"window.open('http://img.weskate.ro/spoturi/".urltext($data['city_name'])."/".$datap['photo_file']."','','scrollbars=yes,toolbar=no,status=no,resizable=yes,width=".($spot_size[0]+20).",height=".($spot_size[1]+20)."')\" class='spoturi round header-link-m' style='height:100%;padding:4px;'>";
				echo "<img src='http://img.weskate.ro/spoturi/".urltext($data['city_name'])."/thumbs/".$datap['photo_file']."' style='border:2px solid #ccc;' alt='".($datap['photo_title'] ? $datap['photo_title'] : $data['spot_title'])."' /><br />";
				echo ($datap['photo_title'] ? "<strong>".$datap['photo_title']."</strong>" : "");
				echo "</a>";
				echo "</div>";
				$i++;
			}
		}
		echo "<div class='spacer' style='clear:both;'></div>";
	} else {
		echo "<span style='font-weight:bold;font-size:16px;display:block;text-align:center;padding:20px;'>Nu a fost adaugata nici o poza din acest loc inca.</span>";
	}
	require_once SCRIPTS."comments.php";
	showcomments("S", $_GET['spot_id']);
	echo "</div>";
	echo "<div style='clear:both;'></div>";
 
} else {
	$lastSlash = strrpos(PAGE_REQUEST,"/");
	redirect(substr(PAGE_REQUEST,0,$lastSlash)."-nf");
}

require_once SCRIPTS."footer.php";
?>
