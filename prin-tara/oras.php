<?php
require_once "../mainfile.php";
$CuloarePagina = "mov";
$UseAJAX = true;
require_once SCRIPTS."header.php";

if (!isset($_GET['id']) || !isnum($_GET['id'])) {
	redirect("prin-tara/?invalid");
}


$result = dbquery("SELECT c.*, j.city_name AS city_judet_name FROM ".DB_CITIES." c
		LEFT JOIN ".DB_CITIES." j ON j.city_id = c.city_judet
		WHERE c.city_id='".$_GET['id']."'");
if (!dbrows($result)) {
	redirect(BASEDIR."prin-tara/?notfound");
}

$data = dbarray($result);

//verificare URL;
$URLcorect = "/prin-tara/".urltext($data['city_name']).".".$data['city_id'];
if (PAGE_REQUEST != $URLcorect) { redirect($URLcorect); }
//end verificare url;

add_to_title(" - Skateboarding-ul din ".$data['city_name']);
set_meta("keywords",strtolower($data['city_name']).",skateboarding,spoturi,locuri,de,skate,shop,skateri");
set_meta("description","Skateboarding-ul &icirc;n ".$data['city_name'].". Locuri de skate, magazine, evenimente si skateri");

opentable("Prin &#355;ar&#259;");
echo "<div style='display:block;border-top:1px dotted #999;border-bottom:1px dotted #999;padding:4px;'>";
echo "<img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' /><a href='/prin-tara/'>Prin &#355;ar&#259;</a>";
echo "<br />";
if ($data['city_judet'] != 0) { 
	echo "<img src='http://img.weskate.ro/bullet_black.png' alt='bullet' border='0' align='left' style='margin-left:10px;' /> Jude&#355; : ".$data['city_judet_name']."<br />";
}
echo "<img src='http://img.weskate.ro/bullet_purple.png' alt='bullet' border='0' align='left' style='margin-left:20px;' />".$data['city_name'];
echo "</div>";
echo "<table cellpadding='0' cellspacing='8' width='100%'><tr valign='top'>";
echo "<td>";
echo "<p style='display:block;font-size:17px;font-family:Impact, Charcoal, sans-serif;text-transform:uppercase;text-align:left;padding-left:20px;'>locuri de skate &Icirc;n <span style='font-size:25px;color:#949'>".$data['city_name']."</span></p>";

$rows = dbcount("(spot_id)", DB_SPOT_ALBUMS, "spot_city='".$_GET['id']."'");
if ($rows) {
	$result = dbquery("SELECT s.*,u.user_name,u.user_profileurl FROM ".DB_SPOT_ALBUMS." s
			LEFT JOIN ".DB_USERS." u ON s.spot_user=u.user_id
			WHERE spot_city='".$_GET['id']."'");

	$counter = 0;
	echo "<table cellpadding='0' cellspacing='1' align='center'>\n<tr>\n";
	$folder = "http://img.weskate.ro/spoturi/".urltext($data['city_name'])."/thumbs/";
	while ($datasp = dbarray($result)) {
		if ($counter != 0 && ($counter % 4 == 0)) { echo "</tr>\n<tr>\n"; }
		echo "<td align='center' valign='top' class='lightonhover' style='padding:4px;border:1px #999 dotted;' width='150'>\n";
		echo "<strong>".$datasp['spot_title']."</strong><br /><br />\n<a href='".PAGE_REQUEST."/".urltext($datasp['spot_title']).".".$datasp['spot_id']."'>";
		if ($datasp['spot_thumb']){
			echo "<img src='".$folder.$datasp['spot_thumb']."' alt='".$datasp['spot_title']."' title='Click pentru vizualizare' style='border:0px none;' />";
		} else {
			echo "fara pictograma";
		}
		echo "</a><br /><br />\n<span class='small'>\n";
		echo "Adaugat la ".showdate("shortdate", $datasp['spot_datestamp'])."<br />\n";
		echo "de catre "."<a href='http://profil.weskate.ro/".$datasp['user_profileurl']."'>".$datasp['user_name']."</a><br />\n";
		$spot_comments = dbcount("(comment_id)", DB_COMMENTS, "comment_type='S' AND comment_item_id='".$datasp['spot_id']."'");
		echo ($spot_comments == 1 ? "un comentariu" : $spot_comments." comentarii")."<br />\n";
		echo "Vizualizari : ".$datasp['spot_views']."</span>\n";
		echo "</td>\n";
		$counter++;
	}
	echo "</tr>\n</table>\n";
} else {
	echo "<p style='font-family:Impact, Charcoal, sans-serif;font-size:30px;padding:top:20px;display:block;text-align:center;'>Nici un loc de skate adaugat.".(iMEMBER ? "<br /><span style='font-size:16px;'><a href='../membri/my/spoturi.php?oras=".$_GET['id']."' style='color:#c9c;'>Fi primul care adauga locuri de skate in acest oras.</a></span>" : "")."</p>";
}

echo "</td><td width='250'>";
echo "<p style='display:block;font-size:17px;font-family:Impact, Charcoal, sans-serif;text-transform:uppercase;text-align:left;padding-left:20px;'>prin <span style='font-size:25px;color:#949'>".$data['city_name']."</span></p>";

if ($data['city_description']) {
	openside("Descriere ora&#351;","mov");
	echo nl2br($data['city_description']);
	closeside();
}

$newsr = dbquery(
	"SELECT news_id,news_subject FROM ".DB_NEWS."
	WHERE (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0' AND news_city=".$data['city_id']."
	ORDER BY news_datestamp DESC LIMIT 0,5");

if (dbrows($newsr) != 0) {
	openside("&#350;tiri","oranj");
	while ($newsdata = dbarray($newsr)) {
		$newsubj = trimlink($newsdata['news_subject'], 32);
		echo "<a href='/stiri/".urltext($newsdata['news_subject']).".".$newsdata['news_id']."' title='".$newsdata['news_subject']."' class='latestnews'>&rsaquo; ".$newsubj."</a>\n";
	}
	echo "<span style='display:block; text-align:right; border:0px; padding:4px; background:#eee;'><a href='/stiri/orase/".urltext($data['city_name']).".".$data['city_id']."' class='side'>Toate &#351;tirile din ".$data['city_name']." &rsaquo;&rsaquo;</a></span>";
	closeside();
}




if (iMEMBER) {
	openside("Optiuni","mov");
	echo "<a href='".BASEDIR."membri/my/stiri.php?oras=".$data['city_id']."' title='Scrie o &#351;tire din ".$data['city_name']."' style='display:block;background-image:url(http://img.weskate.ro/stiri.png);background-position:3px 50%; background-repeat:no-repeat;padding:4px;padding-left:20px;' class='header-link-m stiri'> Scrie o &#351;tire din ".$data['city_name']."</a>";
	echo "<a href='".BASEDIR."membri/my/spoturi.php?oras=".$data['city_id']."' title='Adauga un loc de skate in ".$data['city_name']."' style='display:block;background-image:url(http://img.weskate.ro/new.png);background-position:3px 50%; background-repeat:no-repeat;padding:4px;padding-left:20px;' class='header-link-m spoturi'> Adauga loc de skate in ".$data['city_name']."</a>";
	//favorite:

	require_once SCRIPTS."sistem_favorite/functii.php";
	$id = $_GET['id']; $type = "O"; $divid="favorite".$id.$type;
	echo "<div id='$divid' style='white-space:nowrap;display:block;'>";
	if (LaFavorite($id,$type)) {
		echo "<a title=\"Sterge de la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=rm&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_remove.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class=\"side lightonhoverF\">&#350;terge de la favorite</a>";
	} else {
		echo "<a title=\"Adaug&#259; la favorite\" href=\"javascript:void(0);\" onclick=\"javascript:ajaxpage('".SCRIPTS."sistem_favorite/ajaxfav.php?a=add&amp;id=".$id."&amp;t=".$type."&amp;out=3','".$divid."');\" style=\"background-repeat:no-repeat;background-image:url(http://img.weskate.ro/fav_add.png);background-position:center left;padding:4px;padding-left:20px;display:block;\" class='side lightonhoverF'>Adaug&#259; la favorite</a>";
	}
	echo "</div>";
	//end favorite
	closeside();
}

//skateri in oras
$result = dbquery("SELECT user_name, user_profileurl FROM ".DB_USERS." WHERE user_location='".$data['city_id']."' AND user_skater='2'");
if (dbrows($result)) {
	openside("Skateri in ".$data['city_name'],"albastru");
	$i = 0;
	while ($skater = dbarray($result)) {
		echo ($i ? ", " : "")."<a href='http://profil.weskate.ro/".$skater['user_profileurl']."'>".$skater['user_name']."</a>";
		if (!$i) { $i=1; }
	}
	closeside();
}	
//end.


//google ads
echo "<script type=\"text/javascript\"><!--
google_ad_client = \"pub-2403880163104258\";
google_ad_slot = \"2654206517\";
google_ad_width = 250;
google_ad_height = 250;
//-->
</script>
<script type=\"text/javascript\"
src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";
//end google ads

echo "</td></tr></table>";


require_once SCRIPTS."footer.php";
?>
