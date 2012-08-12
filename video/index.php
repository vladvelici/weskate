<?php
require_once "../mainfile.php";
$CuloarePagina = "rosu";
require_once BASEDIR."scripts/header.php";

if (iMEMBER) {
	echo "<a href='/membri/my/video.php?key=".$_SESSION['user_key']."' style='font-size:13px;padding:5px 5px 5px 19px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3% 50%;font-weight:bold;' class='video header-link-m flright smallround'>Adaugă un video</a>";
}

require_once "functions.php";

add_to_head("<link rel='stylesheet' href='http://weskate.ro/video/video.css' type='text/css' media='screen' />");

if (isset($_GET['video_id']) && isnum($_GET['video_id']) && dbcount("(video_id)",DB_VIDEOS,"video_id='".$_GET['video_id']."'")) {

	$result = dbquery("SELECT v.*,u.user_name,u.user_profileurl,c.video_cat_name FROM ".DB_VIDEOS." v
			LEFT JOIN ".DB_VIDEO_CATS." c ON v.video_cat=c.video_cat_id
			LEFT JOIN ".DB_USERS." u ON v.video_owner=u.user_id
			WHERE video_id='".$_GET['video_id']."'");

	$data = dbarray($result);	

	$URLcorect = "/video/".urltext($data['video_title']).".".$data['video_id'];
	if (PAGE_REQUEST != $URLcorect) { redirect($URLcorect); }

	opentable("<a href='/video/' title='WeSkate Video'>Video</a> : <a href='/video/cat/".urltext($data['video_cat_name']).".".$data['video_cat']."'>".$data['video_cat_name']."</a>");

	set_title(fixRoChars($data['video_title'])." - WeSkate Video");
	set_meta("keywords",$data['video_meta_keywords']);
	set_meta("description",$data['video_meta_description']);

	$result = dbquery("UPDATE ".DB_VIDEOS." SET video_views=video_views+1 WHERE video_id='".$data['video_id']."'");

	echo "<div style='padding:3px 3px 3px 20px;clear:both;border-width:1px 0px 1px 0px;border-style:dotted;border-color:#999;' class='spacer'>";
	echo "<div style='float:right;width:250px;margin-right:20px;white-space:nowrap;'>";
	echo "<span style='font-size:16px;font-weight:bold;'>".$data['video_views']." vizualizari</span><br />";
	echo "Postat de <a href='http://profil.weskate.ro/".$data['user_profileurl']."' style='font-size:14px;font-weight:bold;'>".$data['user_name']."</a> <strong>".showdate("ago",$data['video_datestamp'])."</strong>";
	echo "</div>";
	echo "<span class='capmain_color' style='font-size:30px;font-weight:bold;'>".$data['video_title']."</span>";
	echo "</div>";

	echo "<div class='flright' style='width:250px;font-size:14px;margin-right:20px;'>";
	echo "<strong>Descriere</strong><div style='font-size:14px;padding:4px;'>".$data['video_meta_description']."</div>";
	echo "<strong>Cuvinte cheie</strong><div style='font-size:14px;padding:4px;'>".$data['video_meta_keywords']."</div>";


	$getRelated = dbquery("SELECT v.video_id,v.video_title,v.video_thumb,v.video_views,u.user_name,u.user_profileurl FROM ".DB_VIDEOS." v
				LEFT JOIN ".DB_USERS." u ON v.video_owner=u.user_id
				WHERE video_cat='".$data['video_cat']."' AND video_id!='".$data['video_id']."'
				ORDER BY RAND()
				LIMIT 0,5");

	echo "<strong>Video relevante</strong><div style='padding:4px 0px 4px 0px;'>";
	while ($related = dbarray($getRelated)) {
		echo "<div class='lightonhoverF spacer' style='clear:both;font-size:10px;min-height:50px;'>";
		echo "<a href='".urltext($related['video_title']).".".$related['video_id']."'><img src='".$related['video_thumb']."' style='border:0pt none;width:80px;height:50px;margin-right:3px;' align='left' alt='".$related['video_title']."' /></a>";
		echo "<a href='".urltext($related['video_title']).".".$related['video_id']."' style='font-weight:bold;'>".trimlink($related['video_title'],30)."</a><br />";
		$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_item_id='".$related['video_id']."' AND comment_type='V'");
		echo $related['video_views']." vizualizari | $comments comentari".($comments==1 ? "u" : "i")."<br />";
		echo "Postat de <a href='http://profil.weskate.ro/".$related['user_profileurl']."'>".$related['user_name']."</a>";
		echo "</div>";
	}
	echo "</div>";
	echo "<strong>Publicitate</strong><div style='padding:4px 0px 4px 0px;'>";
	echo '<script type="text/javascript"><!--
google_ad_client = "pub-2403880163104258";
/* 250x250, video */
google_ad_slot = "7090211372";
google_ad_width = 250;
google_ad_height = 250;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';
	echo "</div>"; //publicitate div
	echo "</div>"; //main right options div

	echo "<div style='margin-left:20px;width:640px;'>";
	echo showVideo($data['video_embed'],1,640,360);

	$id = $_GET['video_id']; $type = "V"; $divid="favorite".$id.$type;
	echo "<div id='$divid' style='white-space:nowrap;float:right;width:32px;height:32px;vertical-align:middle;text-align:center;margin:5px;'>";

	if (iMEMBER) {
		require_once SCRIPTS."sistem_favorite/functii.php";

		if (LaFavorite($data['video_id'],"V")) {
			echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=4','".$divid."');\" title=\"Sterge de la favorite\"><img src=\"http://img.weskate.ro/fav_del_mare.png\" alt=\"Sterge de la favorite\" style=\"border: 0pt none ; vertical-align: middle;\" /></a>";
		} else {
			echo "<a href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=4','".$divid."');\" title=\"Adauga la favorite\"><img src=\"http://img.weskate.ro/fav_add_mare.png\"  style=\"border: 0pt none;vertical-align: middle;\"  alt=\"Adauga la favorite\" /></a>";
		}
	} else {
		echo "<a href=\"/membri/conectare.php?redirto=".$URLcorect."\" title=\"Conecteaza-te pentru a adauga la favorite\" onmouseover='showmsg();' onmouseout='hidemsg();'><img src=\"http://img.weskate.ro/fav_add_mare.png\"  style=\"border: 0pt none;vertical-align: middle;\"  alt=\"Conecteaza-te pentru a adauga la favorite\" /></a>";
		echo "<script type='text/javascript'>
			function showmsg() { document.getElementById('msgtoshow').style.display='block'; }
			function hidemsg() { document.getElementById('msgtoshow').style.display='none'; }
		</script>";
	}
	echo "</div>";

	if (!iMEMBER) {
		echo "<div style='white-space:nowrap;display:none;padding:4px;float:right;' id='msgtoshow'>Conecteaza-te inainte de a adauga la favorite!</div>";
	}

	echo "<div style='padding:3px;'><div style='width:1%;white-space:nowrap;'>";
	if ($data['video_allow_ratings']) {
		require_once SCRIPTS."ratings.php";
		showRatings("V",$data['video_id'],"video smallround header-link-m",$width="175px");
	} else {
		echo "Evaluarile sunt dezactivate<br /> de catre utilizator.";
	}
	echo "</div></div>";

	echo "<div style='padding:5px 0px 5px 0px;'>";
	if ($data['video_allow_comments']) {
		require_once SCRIPTS."comments.php";
		showcomments("V", $_GET['video_id']);
	} else {
		echo "Comentariile sunt dezactivate de catre utilizator.";
	}
	echo "</div>";
	echo "</div>";
	echo "<div style='clear:both;'></div>";


} else {
	add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/overlay.js'></script>");
	opentable("<a href='/video/' title='WeSkate Video'>Video</a>");


	set_title("WeSkate Video");
	set_meta("keywords","skate,video,romania,weskate");
	set_meta("description","Video-uri cu cei mai buni skateri romani si straini.");
	$result = dbquery("SELECT r.rating_item_id, avg(r.rating_vote) medie, count(r.rating_vote) nrvot,
				v.video_title, v.video_embed, v.video_id, v.video_views, v.video_thumb, v.video_datestamp,
				u.user_name, u.user_profileurl
			FROM ".DB_RATINGS." r
			INNER JOIN ".DB_VIDEOS." v ON v.video_id = r.rating_item_id
			LEFT JOIN ".DB_USERS." u ON u.user_id = v.video_owner
			WHERE rating_type = 'V'
			GROUP BY rating_item_id
			ORDER BY medie DESC, nrvot DESC, video_views DESC
			LIMIT 0,4");
	if (!dbrows($result)) {
		$result = dbquery("SELECT v.video_title, v.video_embed, v.video_thumb, v.video_id, v.video_views, v.video_datestamp,
				u.user_name, u.user_profileurl FROM ".DB_VIDEOS." v
				LEFT JOIN ".DB_USERS." u ON u.user_id = v.video_owner
				ORDER BY video_views DESC, video_datestamp DESC
				LIMIT 0,4");
	}
	//featured video
	$i = 0; $div = false;
	while ($data=dbarray($result)) {
		if (!$i) {
			echo "<div style='width:400px;height:auto;background-color:#BD5F5F;position:relative;float:right;padding:5px 5px 16px 5px;overflow:hidden;'>";
			echo "<div class='header-umbra' style='position:absolute;bottom:0px;left:0px;z-index:1;width:100%;background-color:#F3F3F3;'></div>";
			echo showVideo($data['video_embed'],0,400,300);
			echo "<a href='".urltext($data['video_title']).".".$data['video_id']."' style='font-size:17px;color:#fff;padding:3px;' class='vizibil'>".$data['video_title']."</a>";
			$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_type='V' AND comment_item_id='".$data['video_id']."'");
			echo "<div style='color:#fff;'><strong>".$data['video_views']."</strong> vizualizari | <strong>$comments</strong> comentarii | <a href='http://profil.weskate.ro/".$data['user_profileurl']."' style='color:#ccc;font-weight:bold;'>".$data['user_name']."</a> | ".showdate("ago",$data['video_datestamp'])."</div>";
			echo "</div>";
			$i=1;
		} else {
			echo "<div style='padding:4px;height:90px;overflow:hidden;' class='lightonhoverF spacer'>";
			echo "<a href='".urltext($data['video_title']).".".$data['video_id']."' class='side'><img src='".$data['video_thumb']."' align='left' style='margin-right:5px;border:0pt none;' alt='".$data['video_title']."' /></a>";
			echo "<a href='".urltext($data['video_title']).".".$data['video_id']."' style='font-size:17px;' class='redlink'>".$data['video_title']."</a><br />";
			echo "<strong>".$data['video_views']."</strong> vizualizari | <strong>$comments</strong> comentarii <br />";
			echo "adaugat de <a href='http://profil.weskate.ro/".$data['user_profileurl']."' style='font-weight:bold;'>".$data['user_name']."</a> | ".showdate("ago",$data['video_datestamp']);
			echo "</div>";
		}
	}
	echo "<div style='clear:both;'></div>";
	opentable("Categorii video");
	$spaces=0; $i=0;
	function getCats($searchIt) {
		global $spaces,$i;
		$resultC = dbquery("SELECT video_cat_name,video_cat_id,video_cat_desc FROM ".DB_VIDEO_CATS." WHERE video_cat_sub='$searchIt'");
		while($dataC=dbarray($resultC)) {
			if ($spaces) {
				echo "<a style='padding:3px 3px 3px ".(3+($spaces*5))."px;' href='cat/".urltext($dataC['video_cat_name']).".".$dataC['video_cat_id']."' class='vizibil video header-link-m'>-&rsaquo; ".$dataC['video_cat_name']."</a>";
				$subcats = (bool) dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_sub='".$dataC['video_cat_id']."'");
			} else {
				$subcats = (bool) dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_sub='".$dataC['video_cat_id']."'");
				if ($i%3==0 ) { echo "<div style='clear:both;'></div>"; }
				echo "<div style='padding:5px;width:30%;float:left;margin:5px;'>";
				if ($subcats) {
					echo "<a href='javascript:void(0);' onclick='return overlay(this,\"subcat$i\",\"bottomright\");' class='flright' style='font-size:8px;'>subcategorii</a>";
				}
				echo "<a href='cat/".urltext($dataC['video_cat_name']).".".$dataC['video_cat_id']."' style='font-weight:bold;font-size:18px;'>".$dataC['video_cat_name']."</a><br />";
				echo $dataC['video_cat_desc'];
				echo "</div>";
				$i++;
			}
			if ($subcats) {
				$spaces++;
				if ($spaces==1) { echo "<div id='subcat".($i-1)."' style='position:absolute;background-color:#fff;border:2px solid #999;width:150px;' class='ascuns'>"; }
				getCats($dataC['video_cat_id']);
				if ($spaces==1) { echo "</div>"; }
				$spaces--;
			}
		}
	}
	getCats(0);
	echo "<div style='clear:both;'></div>";

}



require_once BASEDIR."scripts/footer.php";
?>
