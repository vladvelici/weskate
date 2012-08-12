<?php
require_once "../../mainfile.php";
$CuloarePagina = "rosu";
$UseAJAX=true;
require_once SCRIPTS."header.php";

if (iMEMBER) {
	echo "<a href='/membri/my/video.php?key=".$_SESSION['user_key']."' style='font-size:13px;padding:5px 5px 5px 19px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3% 50%;font-weight:bold;' class='video header-link-m flright smallround'>Adaugă un video</a>";
}

require_once "../functions.php";

add_to_head("<link rel='stylesheet' href='http://weskate.ro/video/video.css' type='text/css' media='screen' />");
$items_per_page = 15;

if (isset($_GET['cat_id']) && isnum($_GET['cat_id']) && dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_id='".$_GET['cat_id']."'")) {

	$result = dbquery("SELECT * FROM ".DB_VIDEO_CATS." WHERE video_cat_id='".$_GET['cat_id']."'");
	$data = dbarray($result);

	//check url
	$URLcorect = "/video/cat/".urltext($data['video_cat_name']).".".$data['video_cat_id'];
	if (isset($_GET['page']) && isnum($_GET['page']) && $_GET['page'] > 1) {
		$rowstart = ($page-1)*$items_per_page;
		$URLcorect .= "-pag".$page;
	} else {
		$rowstart = 0;
	}

	if (PAGE_REQUEST != $URLcorect) { redirect($URLcorect); }

	set_title(fixRoChars($data['video_cat_name'])." - WeSkate Video");
	set_meta("keywords",keywordize($data['video_cat_name'].",video"));
	set_meta("description",fixRoChars($data['video_cat_desc']));

	//get the tree
	$subcat = $data['video_cat_sub'];
	$tree="";
	while ($subcat) {
		$result2 = dbquery("SELECT video_cat_name,video_cat_sub FROM ".DB_VIDEO_CATS." WHERE video_cat_id='$subcat'");
		if (dbrows($result2)) {
			$data2 = dbarray($result2);
			$tree = "<a href='".urltext($data2['video_cat_name']).".$subcat'>".$data2['video_cat_name']."</a> -> ".$tree;
			$subcat = $data2['video_cat_sub'];
		} else {
			$subcat=0;
		}
	}
	echo "<a href='/video/cat/' class='capmain' style='font-size:30px;'>Categorii Video</a>";
	echo "<span style='font-size:16px;font-weight:bold;'> -> ".$tree."<span>".$data['video_cat_name']."</span></span>";

	$getSubcats = dbquery("SELECT video_cat_name,video_cat_id FROM ".DB_VIDEO_CATS." WHERE video_cat_sub='".$data['video_cat_id']."'");
	$subs = dbrows($getSubcats);
	if ($subs) {
		echo "<div style='padding:5px;font-size:16px;'><strong>Sucategorii : </strong>";
		$i = 1;
		while ($sub=dbarray($getSubcats)) {
			echo "<a href='".urltext($sub['video_cat_name']).".".$sub['video_cat_id']."'>".$sub['video_cat_name']."</a>".($i<$subs ? ", " : ".");
			$i++;
		}
		echo "</div>";

	}
	$videos = dbcount("(video_id)",DB_VIDEOS,"video_cat='".$data['video_cat_id']."'");
	if ($videos) {
		$result = dbquery("SELECT v.video_title,v.video_id,v.video_views,v.video_datestamp,v.video_thumb,u.user_name,u.user_profileurl FROM ".DB_VIDEOS." v
			LEFT JOIN ".DB_USERS." u ON v.video_owner=u.user_id
			WHERE video_cat='".$data['video_cat_id']."'
			ORDER BY video_datestamp DESC
			LIMIT $rowstart,$items_per_page");

		if ($videos > $items_per_page) {
			echo "<div style='text-align:center;clear:both;'>".makePageNav($_GET['page'],$items_per_page,$videos,3,"/video/cat/".urltext($data['video_cat_id']).".".$data['video_cat_id']."-pag",true)."</div>\n";
		}
		$i = 0;
		while ($vid=dbarray($result)) {
			if ($i%3==0) { echo "<div style='cleat:both;".($i==0 ? "height:10px;" : "")."'></div>"; } 
			echo "<div class='lightonhoverF flleft' style='width:32%;min-height:90px;padding:5px;margin:1px;overflow:hidden;'>";
			$href = "/video/".urltext($vid['video_title']).".".$vid['video_id'];
			echo "<a href='$href'><img src='".$vid['video_thumb']."' alt='".$vid['video_title']."' align='left' style='border:0pt none;margin-right:3px;'/></a>";
			echo "<a href='$href' style='font-size:14px;'>".$vid['video_title']."</a><br />";
			$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_type='V' AND comment_item_id='".$vid['video_id']."'");
			echo "<strong>$comments</strong> comentari".($comments!=1 ? "i" : "u")." | <strong>".$vid['video_views']."</strong> vizualiz".($vid['video_views']!=1 ? "ari" : "are")."<br />";
			echo "Adaugat de <a href='http://profil.weskate.ro/".$vid['user_profileurl']."'>".$vid['user_name']."</a><br /><strong>".showdate("%e %B %Y",$vid['video_datestamp'])."</strong>";
			echo "</div>";
			$i++;
		}

		if ($videos > $items_per_page) {
			echo "<div style='text-align:center;clear:both;'>".makePageNav($_GET['page'],$items_per_page,$videos,3,"/video/cat/".urltext($data['video_cat_id']).".".$data['video_cat_id']."-pag",true)."</div>\n";
		} else {
			echo "<div style='clear:both;'></div>";
		}
	} else {
		echo "<div style='padding:10px;font-size:19px;font-weight:bold;text-align:center;'>Nu a fost adaugat nici un video in aceasta categorie inca.</div>";
	}

} else {
	set_title("WeSkate Video");
	set_meta("keywords","categorii,video,skate,romania,weskate");
	set_meta("description","Categoriile de video de pe WeSkate.Ro:");
	$spaces=0; $i=0;

	opentable("Categorii Video");

	function getCats($searchIt) {
		global $spaces,$i;
		$resultC = dbquery("SELECT video_cat_name,video_cat_id,video_cat_desc FROM ".DB_VIDEO_CATS." WHERE video_cat_sub='$searchIt'");
		while($dataC=dbarray($resultC)) {
			if ($spaces) {
				echo "<a style='padding:3px 3px 3px ".(3+($spaces*5))."px;' href='".urltext($dataC['video_cat_name']).".".$dataC['video_cat_id']."' class='vizibil video header-link-m'>-&rsaquo; ".$dataC['video_cat_name']."</a>";
				$subcats = (bool) dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_sub='".$dataC['video_cat_id']."'");
			} else {
				$subcats = (bool) dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_sub='".$dataC['video_cat_id']."'");
				if ($i%3==0 ) { echo "<div style='clear:both;'></div>"; }
				echo "<div style='padding:5px;width:30%;float:left;margin:5px;'>";
				if ($subcats) {
					echo "<a href='javascript:void(0);' onclick='return overlay(this,\"subcat$i\",\"bottomleft\");' class='flright' style='font-size:8px;'>subcategorii</a>";
				}
				echo "<a href='".urltext($dataC['video_cat_name']).".".$dataC['video_cat_id']."' style='font-weight:bold;font-size:18px;'>".$dataC['video_cat_name']."</a><br />";
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
	echo "<div style='clear:both;'>";
	echo "<a href='/video/' style='clear:both;'>&lsaquo; &Icirc;napoi la pagina de start video</a>";
	echo "</div>";
	getCats(0);
	echo "<div style='clear:both;'>";
	echo "<a href='/video/' style='clear:both;'>&lsaquo; &Icirc;napoi la pagina de start video</a>";
	echo "</div>";
}

require_once SCRIPTS."footer.php";
?>
