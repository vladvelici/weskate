<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }


$result = dbquery("SELECT article_id id, article_subject subj, article_snippet txt, article_thumb thumb, 'a' type, article_datestamp stamp FROM ".DB_ARTICLES."
		WHERE article_draft='0' AND article_thumb!=''
		UNION
		SELECT news_id id,news_subject subj, news_news txt, news_thumb thumb, 'n' type, news_datestamp stamp FROM ".DB_NEWS."
		WHERE news_draft='0' AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_thumb!=''
		ORDER BY stamp DESC
		LIMIT 0,5");
if (!dbrows($result)) {
	if (iADMIN) echo "<div class='noteyellow'>Admin message: Nu avem continut pentru slide.</div>";
} else {
	echo "<div style='clear:both;'></div>";
	echo "<div style='margin:0px auto 0px auto;'>";
	echo "<div class='flleft'>";
	echo "<div id='LTslider' onmouseover='LTPtimePause();' onmouseout='LTPtimeSlide(5)'>\n";
	$menu = ""; $c=1;
	while ($data = dbarray($result)) {
		$data['txt'] = str_replace(array("<br />","<br>","<br/>"),"",$data['txt']);
		$data['txt'] = strip_tags($data['txt']);
		$data['txt'] = trimlink($data['txt'],339);
		if ($data['type'] == "n") {
			//add item to h menu:
			$menu .= "\n<a href='/stiri/".urltext($data['subj']).".".$data['id']."' onclick='return changeSlide($c);' class='LTslideLink stiri' title='".$data['subj']."' id='LTslideLink$c'".($c==1 ? " style='border-style:solid;border-width:2px 2px 2px 0px;border-color:#333;'" : "")."> $c <span>".trimlink($data['subj'],30)."</span></a><br />\n";
			//echo the items slideshow
			echo "\n<div class='".($c!=1 ? "ascuns" : "vizibil")." LTsliderdiv' id='LTslideItem$c'>\n";
			echo "<img src='http://img.weskate.ro/news/thumbs/".$data['thumb']."' alt='".$data['subj']."'/>";
			echo "\n<div class='LTslideTxt'><a href='/stiri/".urltext($data['subj']).".".$data['id']."' class='LTslideTitle-".$data['type']."' title='".$data['subj']."'>".trimlink($data['subj'],35)."</a><br />".$data['txt']." <a href='/stiri/".urltext($data['subj']).".".$data['id']."' class='LTslideTitle'>Citeste tot</a></div>\n";
			echo "</div>\n";
		} else if ($data['type'] == "a") {
			$menu .= "<a href='/articole/".urltext($data['subj']).".".$data['id']."' onclick='return changeSlide($c);' class='LTslideLink articole' title='".$data['subj']."' id='LTslideLink$c'".($c==1 ? " style='border-style:solid;border-width:2px 2px 2px 0px;border-color:#333;'" : "")."> $c <span>".trimlink($data['subj'],30)."</span></a><br />";
			//echo the items slideshow
			echo "<div class='".($c!=1 ? "ascuns" : "vizibil")." LTsliderdiv' id='LTslideItem$c'>";
			echo "<img src='http://img.weskate.ro/articles/thumbs/".$data['thumb']."' alt='".$data['subj']."'/>";
			echo "<div class='LTslideTxt'><a href='/articole/".urltext($data['subj']).".".$data['id']."' class='LTslideTitle-".$data['type']."' title='".$data['subj']."'>".trimlink($data['subj'],35)."</a><br />".$data['txt']." <a href='/articole/".urltext($data['subj']).".".$data['id']."' class='LTslideTitle'>Citeste tot</a></div>";
			echo "</div>";
		}
		$c++;
	}
	echo "<div class='LTslideMenu'>$menu</div></div>";
	echo "</div><div class='flleft'><script type=\"text/javascript\"><!--
google_ad_client = \"pub-2403880163104258\";
google_ad_slot = \"6650763357\";
google_ad_width = 336;
google_ad_height = 280;
//-->
</script>
<script type=\"text/javascript\"
src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";
	echo "</div></div><div style='clear:both;'></div>";
	add_to_head("<link rel='stylesheet' href='http://weskate.ro/panels/latest_things/ltp.css' type='text/css' media='screen' />");
	add_to_head("<script type='text/javascript' src='http://weskate.ro/panels/latest_things/ltp.js'></script>");
	echo "<script type='text/javascript'>LTPsetMax(".($c - 1).");LTPtimeSlide(5);</script>";

}


?>
