<?php
require_once "../../mainfile.php";
$CuloarePagina = "oranj";
require_once SCRIPTS."header.php";

set_title("Știri skateboarding pe categorii - we Skate");
add_to_head("<link rel='stylesheet' href='http://weskate.ro/stiri/stiri.css' type='text/css' media='screen' />");

echo "<div class='newsmenu flright'>";
echo "<a href='/stiri/' style='background-image:url(http://img.weskate.ro/listview.png);background-position:center left;background-repeat:no-repeat;padding-left:18px;'>Listă știri</a>
<span style='background-image:url(http://img.weskate.ro/categoryview.png);background-position:center left;background-repeat:no-repeat;padding-left:18px;'>Categorii de știri</span></div>";

opentable("Știri skateboarding");


if (isset($_GET['cat_id']) && isnum($_GET['cat_id'])) {
	
	$result = dbquery("SELECT news_cat_name FROM ".DB_NEWS_CATS." WHERE news_cat_id='".$_GET['cat_id']."'");
	if (dbrows($result)) {
		$data = dbarray($result);

		$URLcorect = "/stiri/categorii/".urltext($data['news_cat_name']);
		if (PAGE_REQUEST != $URLcorect) { redirect("http://www.weskate.ro".$URLcorect); }

		set_meta("keywords",keywordize($data['news_cat_name']));
		set_meta("description","Noutati ".strtolower(killRoChars($data['news_cat_name'])));
		set_title($data['news_cat_name']." - noutăți skateboarding pe WeSkate");

		echo "<a href='/stiri/categorii/' style='display:inline-block;padding:4px;' class='header-link-m stiri smallround'>&lsaquo; Înapoi la toate categoriile</a>";
		echo "<div style='clear:both;'></div>";
		echo "<div style='padding:7px;background-color:#eee;border:2px solid #999;' class='spacer round'>";

		$rows = dbcount("(news_id)", DB_NEWS, "news_cat='".$_GET['cat_id']."' AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'");

		echo "<div class='flright'><strong>$rows</strong> știr".($rows == 1 ? "e" : "i")." în acestă categorie</div>";
		echo "<div class='capmain'>".$data['news_cat_name']."</div>";

		if ($rows) {
			$result2 = dbquery("SELECT news_subject,news_id FROM ".DB_NEWS." WHERE news_cat='".$_GET['cat_id']."' AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0' ORDER BY news_datestamp DESC");
			while ($data2 = dbarray($result2)) {
				echo "&middot; <a href='/stiri/".urltext($data2['news_subject']).".".$data2['news_id']."'>".$data2['news_subject']."</a> <br />\n";
			}
		} else {
			echo "<div style='font-size:16px;text-align:center;padding:10px;'>Nu am găsit nici o știe în această categorie.</div>";
		}
		echo "</div>";
	} else {
		redirect("/stiri/categorii/index.php?err=1");
	}
} else {
	if (isset($_GET['err']) && $_GET['err']==1) {
		echo "<div class='noteyellow'>Categoria pe care ai vrut să o deschizi nu (mai) există.</div>";
	}
	echo "<div style='clear:both;'></div>";
	$result = dbquery("SELECT news_cat_id,news_cat_name FROM ".DB_NEWS_CATS);
	while ($data=dbarray($result)) {
		echo "<div style='padding:7px;background-color:#eee;border:2px solid #999;' class='spacer round'>";

		$rows = dbcount("(news_id)", DB_NEWS, "news_cat='".$data['news_cat_id']."' AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'");

		echo "<div class='flright'><strong>$rows</strong> știr".($rows == 1 ? "e" : "i")." în acestă categorie</div>";
		echo "<div class='capmain'>".$data['news_cat_name']."</div>";

		if ($rows) {
			$result2 = dbquery("SELECT news_subject,news_id FROM ".DB_NEWS." WHERE news_cat='".$data['news_cat_id']."' AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0' ORDER BY news_datestamp DESC LIMIT 0,5");
			while ($data2 = dbarray($result2)) {
				echo "&middot; <a href='/stiri/".urltext($data2['news_subject']).".".$data2['news_id']."'>".$data2['news_subject']."</a> <br />\n";
			}
			echo "<div style='padding:4px;text-align:right;'><a href='".urltext($data['news_cat_name'])."' style='display:inline-block;padding:4px;' class='header-link-m stiri smallround'>Toate știrile &rsaquo;</a></div>";
		} else {
			echo "<div style='font-size:16px;text-align:center;padding:10px;'>Nu am găsit nici o știe în această categorie.</div>";
		}
		echo "</div>";
	}
}

require_once SCRIPTS."footer.php";
?>
