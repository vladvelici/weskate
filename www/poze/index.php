<?php
require_once "../mainfile.php";
$CuloarePagina = "albastru_inchis";
require_once SCRIPTS."header.php";
add_to_head("<link rel='stylesheet' href='http://weskate.ro/poze/poze.css' type='text/css' media='screen' />");

if (isset($_GET['err']) && $_GET['err']==1) {
	echo "<div class='notered'>ID Invalid : Ai incercat sa vezi o poza sau sa deschizi un album care a fost sters sau ai gresit ID-ul acestuia.</div>";
}

if (isset($_GET['photo_id']) && isnum($_GET['photo_id'])){
	$result = dbquery(
		"SELECT tp.*, ta.*, tu.user_name,user_profileurl, SUM(tr.rating_vote) AS sum_rating, COUNT(tr.rating_item_id) AS count_votes
		FROM ".DB_PHOTOS." tp
		LEFT JOIN ".DB_PHOTO_ALBUMS." ta USING (album_id)
		LEFT JOIN ".DB_USERS." tu ON tp.photo_user=tu.user_id
		LEFT JOIN ".DB_RATINGS." tr ON tr.rating_item_id = tp.photo_id AND tr.rating_type='P'
		WHERE photo_id='".$_GET['photo_id']."' GROUP BY tp.photo_id LIMIT 1"
	);
	if (!dbrows($result)) { redirect("/poze/err:id-invalid"); }
	$data = dbarray($result);

	$URLcorect = "/poze/".urltext($data['album_title']).".".$data['album_id']."/".($data['photo_title'] ? urltext($data['photo_title'])."_" : "")."poza".$_GET['photo_id'];
	if (PAGE_REQUEST != $URLcorect) {
		redirect($URLcorect);
	}

		$result=dbquery("UPDATE ".DB_PHOTOS." SET photo_views=(photo_views+1) WHERE photo_id='".$_GET['photo_id']."'");

		$pres = dbquery("SELECT photo_id,photo_title,photo_thumb1 FROM ".DB_PHOTOS." WHERE photo_datestamp>".($data['photo_datestamp'])." AND album_id=".$data['album_id']." ORDER BY photo_datestamp ASC LIMIT 1");
		$nres = dbquery("SELECT photo_id,photo_title,photo_thumb1 FROM ".DB_PHOTOS." WHERE photo_datestamp<".($data['photo_datestamp'])." AND album_id=".$data['album_id']." ORDER BY photo_datestamp DESC LIMIT 1");
		if (dbrows($pres)) $prev = dbarray($pres);
		if (dbrows($nres)) $next = dbarray($nres);

		opentable("Poze");
		
		echo "<div style='display:block;border-top:1px dotted #999;border-bottom:1px dotted #999;padding:4px;margin-bottom:10px;'>";
		echo "<div><img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /><a href='/poze/'>Poze</a></div>";
		echo "<div style='padding-left:10px;'><img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /><a href='/poze/".urltext($data['album_title']).".".$data['album_id']."'>".$data['album_title']."</a></div>";
		echo "<div style='padding-left:20px;'><img src='http://img.weskate.ro/bullet_purple.png' alt='bullet' border='0' align='left' />".($data['photo_title'] ? $data['photo_title'] : "Poza ".$data['photo_id']."")."</div>";
		echo "</div>";

		echo "<table cellpadding='0' cellspacing='0' width='100%'><tr valign='top'><td>";
		$photo_size = @getimagesize(BASEDIR."images/photoalbum/".$data['photo_filename']);
		if ($data['photo_description']) {
			$descriere = $data['photo_description'];
		} else {
			$descriere = $data['album_description'];
		}
		if ($data['photo_title']) {
			opentable($data['photo_title']);
			set_title(fixRoChars($data['photo_title']).", album: ".fixRoChars($data['album_title'])." - We Skate");
			set_meta("keywords",keywordize($data['photo_title'].",poza,poze,".$data['album_title']));
		} else {
			set_meta("keywords",keywordize("poze,".$data['album_title']));
			set_title(fixRoChars($data['album_title']).", poza ".$data['photo_id']." - weSkate");
		}
		set_meta("description",fixRoChars(trimlink(strip_tags(str_replace("\n", "", $descriere)),150)));

		echo "<div align='center' style='margin:5px;'>\n";
		echo "<a href=\"javascript:;\" onclick=\"window.open('http://weskate.ro/poze/showphoto.php?photo_id=".$data['photo_id']."','','scrollbars=yes,toolbar=no,status=no,resizable=yes,width=".($photo_size[0]+20).",height=".($photo_size[1]+20)."')\" class='photogallery_photo_link'><!--photogallery_photo_".$data['photo_id']."-->";
		echo "<img src='http://img.weskate.ro/photoalbum/".(isset($data['photo_thumb2']) && !empty($data['photo_thumb2']) ? $data['photo_thumb2'] : $data['photo_filename'])."' alt='".$data['photo_filename']."' title='Vezi poza mare' style='border:0px' /></a>\n";
		if ($data['photo_description']) {
			echo "<!--photogallery_photo_desc-->\n";
			echo nl2br($data['photo_description'])."\n";
		}
		echo "</div>\n";
		if ($data['photo_allow_comments']) {
			echo "<div style='margin:50px;'></div>";
			require_once SCRIPTS."comments.php";
			showcomments("P",$data['photo_id']);
		}
		echo "</td><td width='250'>";
		if ((isset($next['photo_id']) && isnum($next['photo_id'])) || (isset($prev['photo_id']) && isnum($prev['photo_id']))) {
			echo "<table cellpadding='0' cellspacing='4' width='90%' align='center'><tr>";
		}
		if (isset($prev['photo_id']) && isnum($prev['photo_id'])) {
			echo "<td style='text-align:left;white-space:nowrap;background-color:#eee;border:1px solid #999;padding:5px;' width='1%'><a href='/poze/".urltext($data['album_title']).".".$data['album_id']."/".($prev['photo_title'] ? urltext($prev['photo_title'])."." : "")."poza".$prev['photo_id']."' class='side'><img src='http://img.weskate.ro/photoalbum/".$prev['photo_thumb1']."' style='border:0pt none;' alt='".($prev['photo_title'] ? $prev['photo_title'] : $prev['photo_thumb1'])."' /><br />&lsaquo; Anterioara</a></td>";
		}

		if ((isset($next['photo_id']) && isnum($next['photo_id'])) || (isset($prev['photo_id']) && isnum($prev['photo_id']))) {
			echo "<td>&nbsp;</td>";
		}
		if (isset($next['photo_id']) && isnum($next['photo_id'])) {
			echo "<td style='text-align:right;white-space:nowrap;background-color:#eee;border:1px solid #999;padding:5px;' width='1%'><a href='/poze/".urltext($data['album_title']).".".$data['album_id']."/".($next['photo_title'] ? urltext($next['photo_title'])."." : "")."poza".$next['photo_id']."' class='side'><img src='http://img.weskate.ro/photoalbum/".$next['photo_thumb1']."' alt='".($next['photo_title'] ? $next['photo_title'] : $next['photo_thumb1'])."' style='border:0pt none;' /><br />Urm&#259;toarea &rsaquo;</a></td>";
		}
		if ((isset($next['photo_id']) && isnum($next['photo_id'])) || (isset($prev['photo_id']) && isnum($prev['photo_id']))) {
			echo "</tr></table>";
		}

		openside("Despre poză","albastru-inchis");
		echo "<div style='margin-bottom:3px;'><strong>Adăugată la: </strong>".showdate("shortdate", $data['photo_datestamp'])."</div>\n";
		echo "<div style='margin-bottom:3px;'><strong>Proprietar: </strong><a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a></div>\n";
		echo "<div style='margin-bottom:3px;'><strong>Rezoluție:</strong>$photo_size[0] x $photo_size[1]</div>\n";
		echo "<div style='margin-bottom:3px;'><strong>Spațiu pe disc:</strong>".parsebytesize(filesize(BASEDIR."images/photoalbum/".$data['photo_filename']))."</div>\n";
		$photo_comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='P' AND comment_item_id='".$data['photo_id']."'");
		echo "<div style='margin-bottom:3px;'><strong>".($photo_comments == 1 ? "un comentariu" : $photo_comments." comentarii")."</strong></div>\n";
		echo "<div style='margin-bottom:3px;'><strong>".$data['photo_views']." vizualiz".($data['photo_views']==1 ? "are" : "ări")."</strong></div>\n\n";
		echo "<hr />";
		echo "<a href='/poze/".urltext($data['album_title']).".".$data['album_id']."' style='background-repeat:no-repeat;background-image:url(http://img.weskate.ro/photoalbum.png);background-position:center left;padding:4px;padding-left:20px;display:block;' class='side lightonhoverF'>&Icirc;ntoarce-te la album</a>";
		//favorite :
		if (iMEMBER) {
			require_once SCRIPTS."sistem_favorite/functii.php";
			$id = $_GET['photo_id']; $type = "P"; $divid="favorite".$id.$type;
			echo "<div id='$divid' style='white-space:nowrap;display:block;'>";
			if (LaFavorite($id,$type)) {
				echo "<a title=\"&#350;terge de la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('/scripts/sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_remove.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">&#350;terge de la favorite</a>";
			} else {
				echo "<a title=\"Adaug&#259; la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('/scripts/sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_add.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class='side lightonhoverF'>Adaug&#259; la favorite</a>";
			}
			echo "</div>";
		}
		closeside();
		if ($data['photo_allow_ratings']) {
			openside("Evaluări","albastru-inchis");
			require_once SCRIPTS."ratings.php";
			showratings("P",$data['photo_id'],"poze smallround header-link-m");
			closeside();
		}

		echo "</td></tr></table>";

} elseif (isset($_GET['album_id']) && isnum($_GET['album_id'])) {
	$result = dbquery("SELECT * FROM ".DB_PHOTO_ALBUMS." WHERE album_id=".$_GET['album_id']."");
	if (!dbrows($result)) {
		redirect("http://weskate.ro/poze/err:id-invalid");
	} else {
		$data = dbarray($result);
		//check url
		$URLcorect = "/poze/".urltext($data['album_title']).".".$data['album_id'];
		if (isset($_GET['page']) && isnum($_GET['page'])) {
			$URLcorect .= "-pag".$_GET['page'];
		} else {
			$_GET['page'] = 1;
		}
		if ($URLcorect != PAGE_REQUEST) redirect("http://www.weskate.ro".$URLcorect);

		opentable("Poze");
		
		echo "<div style='display:block;border-top:1px dotted #999;border-bottom:1px dotted #999;padding:4px;margin-bottom:10px;'>";
		echo "<div><img src='http://img.weskate.ro/bullet_black.png' alt='bullet' style='border:0px none;' align='left' /><a href='".BASEDIR."poze/'>Poze</a></div>";
		echo "<div style='padding-left:10px;'><img src='http://img.weskate.ro/bullet_purple.png' alt='bullet' align='left' style='border:0px none;' />".$data['album_title']."</div>";
		echo "</div>";

		opentable($data['album_title']);
		echo "\n<table cellpadding='0' cellspacing='0' width='100%'><tr valign='top'><td width='200'>\n";

		$rows = dbcount("(photo_id)", DB_PHOTOS, "album_id='".$_GET['album_id']."'");
		set_title("Poze ".fixRoChars($data['album_title'])." pe weSkate");
		set_meta("keywords",keywordize("poze,".$data['album_title']));
		$descriere = str_replace("\n", " ", $data['album_description']);
		set_meta("description",fixRoChars($descriere));
		openside("Informații album","albastru-inchis");
		if ($data['album_thumb'] && file_exists(BASEDIR."images/photoalbum/".$data['album_thumb'])){
			echo "<div style='display:block;text-align:center;'><img src='http://img.weskate.ro/photoalbum/".$data['album_thumb']."' alt='".$data['album_thumb']."' style='margin:5px;'/></div>";
		}
			
		echo nl2br($data['album_description']);
		echo "<br /><br />Fotografii : ".$rows."<br />";
		echo "Creat la : ".showdate("shortdate", $data['album_datestamp']);
		closeside();
		echo "</td><td>";
		$items_per_page=15;
		if ($rows) {
			$result = dbquery(
				"SELECT tp.*, tu.user_id,user_name,user_profileurl, SUM(tr.rating_vote) AS sum_rating, COUNT(tr.rating_item_id) AS count_votes
					FROM ".DB_PHOTOS." tp
					LEFT JOIN ".DB_USERS." tu ON tp.photo_user=tu.user_id
					LEFT JOIN ".DB_RATINGS." tr ON tr.rating_item_id = tp.photo_id AND tr.rating_type='P'
					WHERE album_id='".$_GET['album_id']."' GROUP BY photo_id ORDER BY photo_datestamp DESC LIMIT ".firstitem($_GET['page'],$items_per_page).",".$items_per_page
				);

			$counter = 0;
			while ($data2 = dbarray($result)) {
				if ($counter != 0 && $counter % 5 == 0) { echo "<div style='clear:both;'></div>\n"; }
				echo "<div class='pozatd smallround flleft' style='text-align:center;width:120px;'>\n";
				if ($data2['photo_title']) {
					echo "<a href='http://www.weskate.ro/poze/".urltext($data['album_title']).".".$data['album_id']."/".urltext($data2['photo_title'])."_poza".$data2['photo_id']."'><strong>".$data2['photo_title']."</strong></a><br /><br />";
				}
				echo "\n<a href='http://www.weskate.ro/poze/".urltext($data['album_title']).".".$data['album_id']."/".($data2['photo_title'] ? urltext($data2['photo_title'])."_" : "")."poza".$data2['photo_id']."'>";
				if ($data2['photo_thumb1'] && file_exists(BASEDIR."images/photoalbum/".$data2['photo_thumb1'])){
					echo "<img src='http://img.weskate.ro/photoalbum/".$data2['photo_thumb1']."' alt='".$data2['photo_thumb1']."' title='deschide poza' style='border:0px' />";
				} else {
					echo "fără pictogramă";
				}
				echo "</a><br /><br />\n<span class='small'><!--photogallery_album_photo_info-->\n";
				echo "Adaugat de <a href='http://profil.weskate.ro/".$data2['user_profileurl']."'>".$data2['user_name']."</a><br />".showdate("ago", $data2['photo_datestamp'])."<br />\n";
				$photo_comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='P' AND comment_item_id='".$data2['photo_id']."'");
				echo ($photo_comments == 1 ? "un comentariu" : "$photo_comments comentarii")."<br />\n";
				echo $data2['photo_views']." vizualizări</span><br />\n";
				echo "</div>\n";
				$counter++;
			}

		} else {
			echo "<div align='center' style='padding-top:30px;font-weight:bold;font-size:15px;'>Nu a fost adaugat&#259; nicio poz&#259; &icirc;n acest album &icirc;nc&#259;.";
			if (iMEMBER) {
				echo "<br />Fi primul care <a href='/membri/my/poza.php?album=".$_GET['album_id']."'>adaug&#259; o poz&#259;</a> aici!";
			}
			echo "</div>";
		}
		if ($rows > $items_per_page) {
			echo "<div style='margin:5px;clear:both;text-align:center;'>\n".
			pagenav($_GET['page'], $rows, $items_per_page, "/poze/".urltext($data['album_title']).".".$_GET['album_id']."-pag").
			"\n</div>\n";
		}
		echo "</td></tr></table>";
	}
} else {
	opentable("Albume foto");

	set_title("Poze pe WeSkate");
	set_meta("keywords","poze,fotografii,imagini,albume foto,foto");
	set_meta("description","Albume foto cu si despre skateboarding");

	$rows = dbcount("(album_id)", DB_PHOTO_ALBUMS);
	if (!isset($_GET['page']) || !isnum($_GET['page'])) { $_GET['page'] = 1; }
	$items_per_page = 12;
	if ($rows) {
		$result = dbquery("SELECT * FROM ".DB_PHOTO_ALBUMS." ORDER BY album_datestamp DESC LIMIT ".firstitem($_GET['page'],$items_per_page).",".$items_per_page);
		$counter = 0;
		while ($data = dbarray($result)) {
			if ($counter != 0 && $counter % 3 == 0) { echo "<div style='clear:both;'></div>\n"; }
			echo "<div class='pozatd smallround' style='width:31%;'>\n";
			if ($data['album_thumb']){
				echo "<a href='/poze/".urltext($data['album_title']).".".$data['album_id']."'><img src='http://img.weskate.ro/photoalbum/".$data['album_thumb']."' alt='Pictograma ".$data['album_title']."' title='Deschide albumul' style='border:0px;margin:5px 10px 5px 5px;' align='left'/></a>";
			}
			echo "<a href='/poze/".urltext($data['album_title']).".".$data['album_id']."' style='font-size:15px;font-weight:bold;'>".$data['album_title']."</a><div class='small' style='padding:5px;'>".dbcount("(photo_id)",DB_PHOTOS,"album_id=".$data['album_id'])." fotografii</div>\n";
			echo "\n<span>\n";
			echo $data['album_description'];
			echo "</span></div>\n";
			$counter++;
		}
		echo "<div style='clear:both;'></div>";
		if ($rows > $items_per_page) {
			 echo "<div align='center'>\n".pagenav($_GET['page'], $items_per_page, $rows,"/poze/pag")."\n</div>\n";
		}
	} else {
		echo "<div style='text-align:center;font-size:20px;font-weight:bold;padding:10px;'>Nu am găsit nici un album foto.</div>\n";
	}
}

require_once SCRIPTS."footer.php";
?>
