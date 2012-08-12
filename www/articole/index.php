<?php
require_once "../mainfile.php";
$CuloarePagina = "galben";
require_once SCRIPTS."header.php";

set_title("Articole despre skateboarding");

if (isset($_GET['article_id']) && isnum($_GET['article_id'])) {
	$result = dbquery(
		"SELECT ta.*,tac.*, tu.user_name,tu.user_profileurl,tu.user_avatar,tu.user_yahoo,tu.user_email,pa.album_title,pa.album_thumb FROM ".DB_ARTICLES." ta
		INNER JOIN ".DB_ARTICLE_CATS." tac ON ta.article_cat=tac.article_cat_id
		LEFT JOIN ".DB_USERS." tu ON ta.article_name=tu.user_id
		LEFT JOIN ".DB_PHOTO_ALBUMS." pa ON ta.article_photoalbum=pa.album_id
		WHERE article_id='".$_GET['article_id']."' AND article_draft='0'");
	if (!dbrows($result)) { redirect("index.php?notfound"); }
	$data = dbarray($result);

	$URLcorect = "/articole/".urltext($data['article_subject']).".".$data['article_id'];
	if (PAGE_REQUEST != $URLcorect) redirect($URLcorect);

	$articol = stripslashes($data['article_article']);

	set_title(fixRoChars($data['article_subject'])." - articol pe WeSkate");
	if (!$data['article_keywords']) {
			$keywords = keywordize($data['article_subject']);
	} else {
		$keywords = $data['article_keywords'];
	}
	if (!$data['article_descriere']) {
		$descriere = trimlink(strip_tags(str_replace("\n", "", fixRoChars($data['article_snippet']))),150);
	} else {
		$descriere = $data['article_descriere'];
	}
	set_meta("keywords",$keywords);
	set_meta("description",$descriere);
	opentable("Articole");

	echo "<div style='border-top:1px dotted #999;border-bottom:1px dotted #999;padding:4px;' class='spacer'>";
	echo "<div><img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /><a href='".BASEDIR."articole'>Articole Home</a></div>";
	echo "<div style='padding-left:10px;'><img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /> Categorie : <a href='".BASEDIR."articole/categoria:".urltext($data['article_cat_name'])."'>".$data['article_cat_name']."</a></div>";
	echo "<div style='padding-left:20px;'><img src='http://img.weskate.ro/bullet_yellow.png' alt='bullet' border='0' align='left' /> <strong>".$data['article_subject']."</strong></div>";
	echo "</div>";

	if (strpos($articol,"--foto--") !== false) {
		if (stripos($articol,"<img") === false) {
			if ($data['article_thumb']) {
				$poza = "http://img.weskate.ro/articles/thumbs/".$data['article_thumb'];
			} else if ($data['article_photoalbum']) {
				$result = dbquery("SELECT photo_id,photo_thumb2,photo_filename FROM ".DB_PHOTOS." WHERE album_id='".$data['article_photoalbum']."' ORDER BY photo_datestamp DESC LIMIT 0,1");
				if (dbrows($result)) {
					$get_photo = dbarray($result);
					$poza = "http://img.weskate.ro/photoalbum/".($get_photo['photo_thumb2'] ? $get_photo['photo_thumb2'] : $get_photo['photo_filename']);
				}
			}
		} else {
			if ($data['article_thumb']) {
				$poza = "http://img.weskate.ro/articles/thumbs/s_".$data['article_thumb'];
			} else if ($data['article_photoalbum']) {
				$poza = "http://img.weskate.ro/photoalbum/".$data['album_thumb'];
			}
		}
		if (isset($poza)) {
			$poza = "<div class='flright smallround' style='margin:5px;border:1px solid #999;background-color:#eee;padding:4px;text-align:center;'><img src='$poza' alt='".($data['album_title'] ? $data['album_title'] : $data['article_subject'])."'/><br />".($data['article_photoalbum'] ? "<a href='/poze/".urltext($data['album_title']).".".$data['article_photoalbum']."' >Mai multe poze</a>" : "")."</div>";
			$articol = str_replace("--foto--",$poza,$articol);
		}		
	} else if (stripos($articol,"<img")!==false) {
		if ($data['article_photoalbum']) {
			if ($data['article_thumb']) {
				$poza = "http://img.weskate.ro/articles/thumbs/s_".$data['article_thumb'];
			} else {
				$poza = "http://img.weskate.ro/photoalbum/".$data['album_thumb'];
			}
			echo "<div class='flright smallround' style='margin:5px;border:1px solid #999;background-color:#eee;padding:4px;text-align:center;'><img src='$poza' alt='".($data['album_title'] ? $data['album_title'] : $data['article_subject'])."'/><br /><a href='/poze/".urltext($data['album_title']).".".$data['article_photoalbum']."' >Mai multe poze</a></div>";
		}
	} else {
		if ($data['article_thumb']) {
			$poza = "http://img.weskate.ro/articles/thumbs/".$data['article_thumb'];
		} else if ($data['article_photoalbum']) {
			$result = dbquery("SELECT photo_id,photo_thumb2,photo_filename FROM ".DB_PHOTOS." WHERE album_id='".$data['article_photoalbum']."' ORDER BY photo_datestamp DESC LIMIT 0,1");
			if (dbrows($result)) {
				$get_photo = dbarray($result);
				$poza = "http://img.weskate.ro/photoalbum/".($get_photo['photo_thumb2'] ? $get_photo['photo_thumb2'] : $get_photo['photo_filename']);
			}
		}
		if (isset($poza)) {
			echo "<div class='flright smallround' style='margin:5px;border:1px solid #999;background-color:#eee;padding:4px;text-align:center;'><img src='$poza' alt='".($data['album_title'] ? $data['album_title'] : $data['article_subject'])."'/><br />".($data['article_photoalbum'] ? "<a href='/poze/".urltext($data['album_title']).".".$data['article_photoalbum']."' >Mai multe poze</a>" : "")."</div>";
		}		
	}

	echo "<div class='capmain'>".$data['article_subject']."<div style='font-size:12px;color:#000;padding:2px;'>de <a href='http://profil.weskate.ro/".$data['user_profileurl']."'><strong>".$data['user_name']."</strong></a> ".showdate("datehover forumdate",$data['article_datestamp'])."</div></div>";

	echo ($data['article_breaks']=="y" ? nl2br($articol) : $articol);

	echo "<div style='clear:both;'></div>";

	echo "<div style='float:right;width:230px;'>";

	if ($data['article_allow_ratings']) {
		echo "<div style='font-size:16px;font-weight:bold;padding:5px;' class='capmain_color'>Evaluări</div>";
		require_once SCRIPTS."ratings.php";
		showRatings("A",$data['article_id'],"articole smallround header-link-m","200px");
	}

	$sources = $data['article_sources'];
	if ($sources) {
		echo "<div style='font-size:16px;font-weight:bold;padding:5px;' class='capmain_color'>Surse și link-uri utile</div><div style='padding:5px 10px 10px 10px;'>";
		if (strpos($sources,"\n") === false) {
			if (strpos($sources,"->") !== false) {
				list($txt,$url) = explode("->",$sources);
				$url = str_replace("&","&amp;",$url);
				echo "<a href='".(strpos($url,"http://") === 0 ? $url : "http://".$url)."' target='_blank'>".($txt ? $txt : $url)."</a>";
			} else {
				echo $sources;
			}
		} else {
			$sourceArray = explode("\n",$sources);
			foreach ($sourceArray as $nr => $sursa) {
				if (strpos($sursa,"->") !== false) {
					list($txt,$url) = explode("->",$sursa);
					$url = str_replace("&","&amp;",$url);
					echo "<a href='".(strpos($url,"http://") === 0 ? $url : "http://".$url)."' target='_blank'>".($txt ? $txt : $url)."</a>";
				} else {
					echo $sursa;
				}
				echo "<br />";
			}
		}
		echo "</div>";
	}
	$result = getRelatedList($data['article_subject'],5,"A",$data['article_id']);
	if ($result) {
		echo "<div style='font-size:16px;font-weight:bold;padding:5px;' class='capmain_color'>Poate te interesează și:</div><div style='padding:5px 10px 10px 10px;'>";
		while ($search=dbarray($result)) {
			echo "<a href='".$search['search_url']."' style='display:inline-block;padding:5px 0px 5px 20px;background:url(http://img.weskate.ro/".getImageByType($search['search_type']).") no-repeat center left;'>".$search['search_title']."</a><br />";
		}
		echo "</div>";
	}
	echo "</div>";
	if ($data['article_allow_comments']) {
		require_once SCRIPTS."comments.php";
		showcomments("A",$data['article_id']);
	} else {
		echo "<div style='font-weight:bold;padding:10px;'>Autorul articolului a dezactivat comentariile.</div>";
	}
	echo "<div style='clear:both;'></div>";

} elseif (isset($_GET['cat_id']) && isnum($_GET['cat_id'])) {
	$result = dbquery("SELECT article_cat_name FROM ".DB_ARTICLE_CATS." WHERE article_cat_id='".$_GET['cat_id']."'");
	if (!dbrows($result)) redirect(BASEDIR."articole/");
	$data = dbarray($result);

	$URLcorect = "/articole/categoria:".urltext($data['article_cat_name']);
	if (isset($_GET['page']) && isnum($_GET['page'])) {
		$URLcorect .= "-pag".$_GET['page'];
	} else {
		$_GET['page'] = 1;
	}

	if (PAGE_REQUEST != $URLcorect) redirect($URLcorect);
	add_to_head("<link rel='stylesheet' href='http://weskate.ro/articole/articole.css' type='text/css' media='Screen' />");
	opentable("Articole : ".$data['article_cat_name']);
	$categorie = $data['article_cat_name'];
	$items_per_page = 12;
	$rows = dbcount("(article_id)",DB_ARTICLES,"article_cat=".$_GET['cat_id']);
	echo "<a href='/articole/' class='header-link-m articole smallround".($rows > $items_per_page ? " flright" : "")."' style='padding:3px;display:inline-block;'>Înapoi la articole</a>";
	if ($rows > $items_per_page) {
		echo pagenav($_GET['pag'],$rows,$items_per_page,"/articole/categoria:".urltext($categorie)."-pag");
	}

	echo "<div style='clear:both;'></div>";

	set_title($data['article_cat_name']." - articole pe We Skate");
	$result = dbquery("SELECT a.article_subject, a.article_datestamp, a.article_id, a.article_snippet, a.article_thumb, a.article_photoalbum, f.album_thumb FROM ".DB_ARTICLES." a
			LEFT JOIN ".DB_PHOTO_ALBUMS." f ON f.album_id=a.article_photoalbum
			WHERE article_cat=".$_GET['cat_id']."
			ORDER BY article_datestamp DESC
			LIMIT ".firstitem($_GET['page'],$items_per_page).",".$items_per_page);
	if (!dbrows($result)) {
		echo "<div style='font-weight:bold;font-size:20px;text-align:center;padding:10px;'>Nu am găsit nici un articol în această categorie.</div>";
	} else {
		$i = 0;
		while ($data=dbarray($result)) {
			if ($i!=0 && $i%2==0) { echo "<div class='spacer' style='clear:both;'></div>"; }
			echo "<div class='flleft smallround' style='width:48%;margin:4px;background-color:#eee;border:1px solid #ccc;padding:4px;'>";
			$poza = false;
			if ($data['article_thumb']) {
				$poza = "http://img.weskate.ro/articles/thumbs/s_".$data['article_thumb'];
			} else if ($data['album_thumb']) {
				$poza = "http://img.weskate.ro/photoalbum/".$data['album_thumb'];
			}
			if ($poza && stripos($data['article_snippet'],"<img")===false) {
				echo "<img src='$poza' alt='".$data['article_subject']."' align='left' style='margin:5px;' />";
			}
			echo "<a href='/articole/".urltext($data['article_subject']).".".$data['article_id']."' class='article-title'>".$data['article_subject']."</a>";
			echo "<div class='small'>".showdate("ago",$data['article_datestamp'])."</div>";
			echo stripslashes($data['article_snippet']);
			echo "</div>";
			$i++;
		}
		echo "<div style='clear:both;'></div>";
		if ($rows > $items_per_page) {
			echo pagenav($_GET['pag'],$rows,$items_per_page,"/articole/categoria:".urltext($categorie)."-pag");
		}
	}
} else {
	add_to_head("<link rel='stylesheet' href='http://weskate.ro/articole/articole.css' type='text/css' media='Screen' />");
	set_meta("keywords","articole,skateboarding,interviuri,skateri,intretinere skate,review-uri");
	set_meta("description","Intretinere skate, review-uri, interviuri skateri, biografii si alte articole despre skateboarding.");
	opentable("Articole");
	//ultimele adaugate :
	echo "<div class='flright'>";
	$result = dbquery("SELECT article_id, article_subject FROM ".DB_ARTICLES." WHERE article_draft='0' ORDER BY article_datestamp DESC LIMIT 0,10");
	if (dbrows($result)) {
		openside("Ultimele articole adaugate","galben");
		while ($data = dbarray($result)) {	
			echo "<a href='/articole/".urltext($data['article_subject']).".".$data['article_id']."' title='".$data['article_subject']."' class='latestarticles lightonhoverF' style='display:block;padding:4px;'>&rsaquo; ".trimlink($data['article_subject'],36)."</a>\n";
		}
		closeside();
	}
	//CATEGORII :
	openside("Categorii","galben");
	$result = dbquery("SELECT * FROM ".DB_ARTICLE_CATS." ORDER BY article_cat_name");

	while ($data = dbarray($result)) {
		$num = dbcount("(article_cat)", DB_ARTICLES, "article_cat='".$data['article_cat_id']."' AND article_draft='0'");
		echo "<div class='catdiv lightonhoverF'>";
		echo "&rsaquo; <a href='".BASEDIR."articole/categoria:".strtolower(str_replace(' ','-',$data['article_cat_name']))."'>".$data['article_cat_name']."</a><span>($num articole)</span>";
		echo "</div>";
	}
	closeside();
	echo "</div>";

	$result = dbquery("SELECT tr.rating_item_id, avg(tr.rating_vote) medie, count(tr.rating_vote) nrvot, ta.article_subject, ta.article_thumb, ta.article_article, f.album_thumb FROM ".DB_RATINGS." tr
	INNER JOIN ".DB_ARTICLES." ta ON ta.article_id = tr.rating_item_id
	LEFT JOIN ".DB_PHOTO_ALBUMS." f ON f.album_id=ta.article_photoalbum
	WHERE rating_type = 'A' AND (ta.article_photoalbum!=0 OR ta.article_thumb!='')
	GROUP BY rating_item_id
	ORDER BY medie DESC, nrvot DESC
	LIMIT 0,4");
	add_to_head("<script type='text/javascript' src='http://weskate.ro/articole/articol.js'></script>");
	$articles = "";
	$set_var=true;
	echo "<div style='height:130px;'>";
	while ($data=dbarray($result)) {
		if ($data['article_thumb']) {
			$img = "http://img.weskate.ro/articles/thumbs/s_".$data['article_thumb'];
		} else {
			$img = "http://img.weskate.ro/photoalbum/".$data['album_thumb'];
		}
		echo "<div id='thumb_".$data['rating_item_id']."' class='ar_thumb".($set_var ? " selected" : "")."' style='background-image:url($img);'><a href='javascript:changeArticle(".$data['rating_item_id'].");'></a></div>";
		$txt = strip_tags(trimlink(stripslashes($data['article_article']),600),"<br><img>");
		$txt = str_replace("--foto--","",$txt);
		$articles .= "<div id='article_".$data['rating_item_id']."' class='".($set_var ? "vizibil" : "ascuns")."'><a href='/articole/".urltext($data['article_subject']).".".$data['rating_item_id']."' class='article-title'>".$data['article_subject']."</a><br />".$txt."</div>";
		if ($set_var) {
			add_to_head("<script type='text/javascript'>selectedArticle=".$data['rating_item_id'].";</script>");
			$set_var=false;
		}
	}
	echo "</div>".$articles;
	echo "<div style='clear:both;'></div>";
	
}

require_once SCRIPTS."footer.php";
?>
