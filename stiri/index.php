<?php
require_once "../mainfile.php";
$CuloarePagina = "oranj";
$UseMAPPER=true;
require_once SCRIPTS."header.php";
add_to_head("<link rel='stylesheet' href='http://weskate.ro/stiri/stiri.css' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='http://weskate.ro/stiri/stiri.js'></script>");

$items_per_page = 11;

set_title("Știri skateboarding pe We Skate");

set_meta("keywords","skateboard,skateboarding,skate,noutati,stiri");
set_meta("description","Concursuri, deschideri de skate park-uri și alte evenimente și noutăți din lumea skateboarding-uluil românesc.");
echo "<div class='newsmenu flright'>";
if (isset($_GET['read']) && isnum($_GET['read'])) {
	echo "<a href='/stiri/' style='background-image:url(http://img.weskate.ro/listview.png);background-position:center left;background-repeat:no-repeat;padding-left:18px;'>Listă știri</a>";
} else {
	echo "<span style='background-image:url(http://img.weskate.ro/listview.png);background-position:center left;background-repeat:no-repeat;padding-left:18px;'>Listă știri</span>";
}
echo "<a href='/stiri/categorii' style='background-image:url(http://img.weskate.ro/categoryview.png);background-position:center left;background-repeat:no-repeat;padding-left:18px;'>Categorii de &#351;tiri</a>
</div>";
$city = false;
$cityName = false;
if (!isset($_GET['read']) || !isnum($_GET['read'])) {
echo "<div class='flright newslocale'>";
	echo "<div id='change_local' class='ascuns'>
	<a href='javascript:closeChangeCity();' class='stiri biground header-link-m flright' style='padding:3px 5px 3px 5px;position:relative;z-index:7;'>X</a>
	<div id='map' class='ascuns'>";
	require_once "showMap.php";
	echo "</div>
	<div id='citylist' class='ascuns'></div>
	</div>";
	echo "<div style='font-size:16px;font-weight:bold;padding-left:20px;background:url(http://img.weskate.ro/cityview.png) no-repeat center left;'>Știri locale</div>";
	if (isset($_GET['city']) && isnum($_GET['city'])) {
		$result = dbquery("SELECT city_name FROM ".DB_CITIES." WHERE city_id=".$_GET['city']."");
		if (dbrows($result)) {
			$data = dbarray($result);
			$city = $_GET['city'];
			$cityName = $data['city_name'];
			echo "<div style='font-weight:bold;font-size:14px;padding:7px;'>".$data['city_name']."</div>";
			echo "<a href='javascript:changeCity();' class='stiri smallround header-link-m' style='padding:3px;display:inline-block;'>Schimbă orașul</a>";
			echo "<a href='/stiri/' class='stiri smallround header-link-m' style='margin-left:3px;padding:3px;display:inline-block;'>Orice oraș</a>";
		} else {
			echo "<div style='font-weight:bold;font-size:12px;padding:7px 5px 7px 5px;'>se afișează toate știrile</div>";
			echo "<a href='javascript:changeCity();' class='stiri smallround header-link-m' style='padding:3px;display:inline-block;'>Alege un oraș</a>";
		}
	} else {
		echo "<div style='font-weight:bold;font-size:12px;padding:7px 5px 7px 5px;'>se afișează toate știrile</div>";
		echo "<a href='javascript:changeCity();' class='stiri smallround header-link-m' style='padding:3px;display:inline-block;'>Alege un oraș</a>";
	}

	echo "</div>";
}


if (isset($_GET['read']) && isnum($_GET['read'])) {
	opentable("Știri skateboarding");
	echo "<a href='/stiri/' style='display:inline-block;padding:4px;' class='header-link-m stiri smallround'>&lsaquo; Înapoi la lista de știri</a>";
	echo "<div style='clear:both;'></div>";
	if (!dbcount("(news_id)",DB_NEWS,"(news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0' AND news_id=".$_GET['read'])) redirect("/stiri/notfound");
	$result = dbquery(
		"SELECT tn.*, tc.news_cat_name,pa.album_title, tu.user_id, tu.user_name, tu.user_profileurl, sp.city_name AS news_city_name FROM ".DB_NEWS." tn
		LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
		LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
		LEFT JOIN ".DB_CITIES." sp ON tn.news_city=sp.city_id
		LEFT JOIN ".DB_PHOTO_ALBUMS." pa ON pa.album_id = tn.news_photoalbum
		WHERE (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0' AND news_id=".$_GET['read']."
		ORDER BY news_datestamp DESC LIMIT 1");
	$data = dbarray($result);

	$URLcorect = "/stiri/".urltext($data['news_subject']).".".$data['news_id'];
	if (PAGE_REQUEST != $URLcorect) redirect($URLcorect);

	echo "<a name='news_".$data['news_id']."' id='news_".$data['news_id']."'></a>";
	echo "<div style='padding:10px;background-color:#eee;border:2px solid #999;' class='spacer round'>\n";

	echo "<div class='flright'>";
	if ($data['news_photoalbum']) {
		echo "<a class='lightonhover side' title='Album foto : ".$data['album_title']."' style='background-position:center left;display:block;padding:3px;background-image:url(http://img.weskate.ro/photoalbum.png);background-repeat:no-repeat;padding-left:20px;' href='/poze/".urltext($data['album_title']).".".$data['news_photoalbum']."'>Album foto</a>";
	}
	if ($data['news_city']) {
		echo "<a class='lightonhover side' style='background-position:center left;display:block;padding:3px;background-image:url(http://img.weskate.ro/cityview.png);background-repeat:no-repeat;padding-left:20px;' href='/stiri/orase/".urltext($data['news_city_name']).".".$data['news_city']."'>".$data['news_city_name']."</a>";
	}
	echo "<a class='lightonhover side' style='display:block;padding:3px;background-image:url(http://img.weskate.ro/categoryview.png);background-repeat:no-repeat;padding-left:20px;background-position:center left;' href='/stiri/categorii/".urltext($data['news_cat_name'])."'>".$data['news_cat_name']."</a>";
	echo "</div>";

	echo "<span class='news-title'>".$data['news_subject']."</span><br />\n";
	echo "<div style='padding-left:15px;'> de <a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a> | ".showdate("datehover longdate",$data['news_datestamp'])."</div>";

	echo "<div style='clear:both;border-top:1px dotted #999;margin:7px 0px 7px 0px;'></div>";

	$news = stripslashes($data['news_extended'] ? $data['news_extended'] : $data['news_news']);
	if ($data['news_breaks'] == "y") { $news = nl2br($news); }


	if (stripos($news,"<img") === false && $data['news_thumb']) {
		echo "<img src='http://img.weskate.ro/news/thumbs/".$data['news_thumb']."' alt='".$data['news_subject']."' align='right' style='margin:5px;border:2px solid #ccc;'/>";
	}

	echo ($data['news_breaks'] == "y" ? nl2br($news) : $news);
	echo "<div style='clear:both;'></div>";
	echo "<div style='border-top:1px dotted #999;margin-top:7px;;padding:5px 7px 5px 26px;background:url(http://img.weskate.ro/tags.png) 7px 50% no-repeat;min-height:16px;'>";
	$keywords = $data['news_keywords'];
	if (!$keywords) $keywords = keywordize(killRoChars($data['news_subject']));
	set_meta("keywords",$keywords);
	set_title($data['news_subject']." - știri pe WeSkate");
	if (strpos($keywords,",")) {
		$tags = explode(",",$keywords);
		$comma = false;
		foreach ($tags as $tag) {
			echo ($comma ? ", " : "")."<a href='/cauta/index.php?q=".$tag."'>$tag</a>";
			if (!$comma) $comma=true;
		}
	} else {
		echo "<a href='/cauta/index.php?q=".$keywords."'>$keywords</a>";
	}

	if (!$data['news_descriere']) {
		$description = strip_tags($data['news_descriere'],"<br>");
		$cut = min(min(strpos($description,"\n"),strpos($description,"<br />")),255);
		$description = trimlink($description,$cut);
	} else {
		$description = $data['news_descriere'];
	}
	set_meta("description",$description);

	echo "</div>";
	echo "</div>\n";
	echo "<div class='flright' style='width:230px;'>";
	if ($data['news_allow_ratings']) {
		require_once SCRIPTS."ratings.php";
		echo "<div style='font-size:16px;font-weight:bold;padding:5px;' class='capmain_color'>Evaluări</div>";
		showRatings("N",$data['news_id'],"stiri smallround header-link-m","200px");
	}

	$sources = $data['news_sources'];
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

	if ($result = getRelatedList(killRoChars($data['news_subject']),5,"N",$data['news_id'])) {
		echo "<div style='font-size:16px;font-weight:bold;padding:5px;' class='capmain_color'>Poate te interesează și:</div><div style='padding:5px 10px 10px 10px;'>";
		while ($search=dbarray($result)) {
			echo "<a href='".$search['search_url']."' style='display:inline-block;padding:5px 0px 5px 20px;background:url(http://img.weskate.ro/".getImageByType($search['search_type']).") no-repeat center left;'>".$search['search_title']."</a><br />";
		}
		echo "</div>";
	}
	echo "</div>";
	//comments
	if ($data['news_allow_comments']) {
		require_once SCRIPTS."comments.php";
		showcomments("N",$data['news_id']);
	}
	//end comments
	echo "<div style='clear:both;'></div>";



} else {
	$rows = dbcount("(news_id)", DB_NEWS,"(news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'".($city ? " AND news_city=$city" : ""));

	if (isset($_GET['page']) && isnum($_GET['page'])) {
		$URLcorect = "/stiri/".($city ? "orase/".urltext($cityName).".".$city."-" : "")."pag".$_GET['page'];
	} else {
		$_GET['page'] = 1;
		$URLcorect = "/stiri/".($city ? "orase/".urltext($cityName).".".$city : "");
	}
	if (PAGE_REQUEST != $URLcorect) redirect($URLcorect);

	opentable("Știri skateboarding");
	if ($rows > $items_per_page) {
		echo pagenav($_GET['page'],$rows,$items_per_page,"/stiri/".($city ? urltext($cityName).".".$city."-" : "")."pag");
	}
	echo "<div style='clear:both;'></div>";



	if ($rows) {
		$result = dbquery(
			"SELECT tn.*, tc.news_cat_name,pa.album_title, tu.user_id, tu.user_name, tu.user_profileurl, sp.city_name AS news_city_name FROM ".DB_NEWS." tn
			LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
			LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
			LEFT JOIN ".DB_CITIES." sp ON tn.news_city=sp.city_id
			LEFT JOIN ".DB_PHOTO_ALBUMS." pa ON pa.album_id = tn.news_photoalbum
			WHERE (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'".($city ? " AND news_city=$city" : "")."
			ORDER BY news_datestamp DESC LIMIT ".firstitem($_GET['page'],$items_per_page).",$items_per_page"
		);
		
		while ($data = dbarray($result)) {
		echo "<a name='news_".$data['news_id']."' id='news_".$data['news_id']."'></a>";
		echo "<div style='padding:10px;background-color:#eee;border:2px solid #999;' class='spacer round'>\n";

		echo "<div class='flright'>";
		if ($data['news_photoalbum']) {
			echo "<a class='lightonhover side' title='Album foto : ".$data['album_title']."' style='background-position:center left;display:block;padding:3px;background-image:url(http://img.weskate.ro/photoalbum.png);background-repeat:no-repeat;padding-left:20px;' href='/poze/".urltext($data['album_title']).".".$data['news_photoalbum']."'>Album foto</a>";
		}
		if ($data['news_city']) {
			echo "<a class='lightonhover side' style='background-position:center left;display:block;padding:3px;background-image:url(http://img.weskate.ro/cityview.png);background-repeat:no-repeat;padding-left:20px;' href='/stiri/orase/".urltext($data['news_city_name']).".".$data['news_city']."'>".$data['news_city_name']."</a>";
		}
		echo "<a class='lightonhover side' style='display:block;padding:3px;background-image:url(http://img.weskate.ro/categoryview.png);background-repeat:no-repeat;padding-left:20px;background-position:center left;' href='/stiri/categorii/".urltext($data['news_cat_name'])."'>".$data['news_cat_name']."</a>";
		echo "</div>";

		echo "<a href='/stiri/".urltext($data['news_subject']).".".$data['news_id']."' class='news-title'>".$data['news_subject']."</a><br />\n";
		echo "<div style='padding-left:15px;'> de <a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a> | ".showdate("datehover longdate",$data['news_datestamp'])."</div>";

		echo "<div style='clear:both;border-top:1px dotted #999;margin:7px 0px 7px 0px;'></div>";

		$news = stripslashes($data['news_news'] ? $data['news_news'] : $data['news_extended']);

		if (stripos($news,"<img") === false && $data['news_thumb']) {
			echo "<img src='http://img.weskate.ro/news/thumbs/s_".$data['news_thumb']."' alt='".$data['news_subject']."' align='right' style='margin:5px;border:2px solid #ccc;'/>";
		}

		echo ($data['news_breaks'] == "y" ? nl2br($news) : $news);
		echo "<div style='clear:both;'></div>";
		echo "<div style='border-top:1px dotted #999;margin-top:7px;;padding:5px 7px 0px 7px;'>";
		$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_item_id=".$data['news_id']." AND comment_type='N'");
		echo "<a href='/stiri/".urltext($data['news_subject']).".".$data['news_id']."'>Citește tot</a> / ".$comments." comentari".($comments==1 ? "u" : "i");
		echo "</div>";
		echo "</div>\n";
		}
		if ($rows > $items_per_page) {
			echo pagenav($_GET['page'],$rows,$items_per_page,"/stiri/pag");
		}
	} else {
		if ($city) echo "<div style='font-size:20px;text-align:center;padding:20px;'>Nu am găsit nici o știre din orașul <strong>$cityName</strong>.</div>";
	}
}
require_once SCRIPTS."footer.php"; 
?>
