<?php
require_once "mainfile.php";

if (!isset($_GET['z']) || !isnum($_GET['z']) || $_GET['z'] < 1 || $_GET['z'] > 3) {
	echo "ID-ul zonei invalid. &Icirc;ncearc&#259; din nou.";
} else {
	$locatie = false;
	if (isset($_GET['l']) && isnum($_GET['l'])) {
		setcookie("weskate_location",$_GET['l']);
		$_COOKIE['weskate_location'] = $_GET['l'];
		if (iMEMBER && $_GET['l']!=0 && isset($_GET['s']) && $_GET['s']==true) {
			$result = dbquery("UPDATE ".DB_USERS." SET user_location=".$_GET['l']." WHERE user_id=".$userdata['user_id']);
		}
		$locatie=$_GET['l'];
	}

	$myCity = false;

	if ($locatie!==false) {
		$myCity = $locatie;
	} elseif (isset($_COOKIE['weskate_location']) && isnum($_COOKIE['weskate_location'])) {
		$myCity = $_COOKIE['weskate_location'];	
	} elseif (iMEMBER && $userdata['user_location'] != 0) {
		$myCity = $userdata['user_location'];
	}

	if ($myCity) {

	if ($_GET['z'] == 1) {

		$result = dbquery("SELECT sp.spot_thumb, sp.spot_title, sp.spot_id, gj.city_judet, jt.city_name AS judet, ju.city_name AS oras, ju.city_id AS oras_id FROM ".DB_SPOT_ALBUMS." sp
				   LEFT JOIN ".DB_CITIES." gj ON gj.city_id='$myCity'
				   LEFT JOIN ".DB_CITIES." ju ON ju.city_judet=gj.city_judet
				   LEFT JOIN ".DB_CITIES." jt ON jt.city_id=gj.city_judet
				   WHERE sp.spot_city=ju.city_id ORDER BY sp.spot_datestamp DESC LIMIT 0,3");

		if (dbrows($result)) {
			$showMSG = true;

			while ($data=dbarray($result)) {
				if ($showMSG) {
					echo "<div style='min-height:187px;'>";
					echo "<div style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;'><a href='javascript:changeCity();' class='flright'>schimbă locația</a>Locuri de skate din jude&#355;ul ".$data['judet'].":</div>";
					echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='spacer'><tr>";
					$newMSG = "Skateboarding-ul &icirc;n ";
					$oldCity = "";
					$cities = 0;
					$showMSG = false;
				}
				$cities++;
				if ($oldCity != $data['oras']) {
					$newMSG .= ($cities > 1 ? ", " : "")."<a href='".BASEDIR."prin-tara/".urltext($data['oras']).".".$data['oras_id']."'>".$data['oras']."</a>";
					$oldCity = $data['oras'];
				}
				$itemdescription=fixRoChars($data['spot_title']);
				echo "<td width='33%' align='center'>";
				echo "<a href='".BASEDIR."prin-tara/".urltext($data['oras']).".".$data['oras_id']."/".urltext($data['spot_title']).".".$data['spot_id']."' title='".$data['spot_title']."' class='header-link-m spoturi vizibil' style='padding-top:5px;padding-bottom:5px;'><strong>$itemdescription</strong><br />
<img src='http://img.weskate.ro/spoturi/".urltext($data['oras'])."/thumbs/".$data['spot_thumb']."' alt='".$data['spot_title']."' style='border:2px solid #eee;margin-top:2px;' /><br />&icirc;n ".$data['oras']."</a>\n";
				echo "</td>";
			}
			echo "</tr></table>";
			echo "</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>$newMSG.</div>";
		} else {
			echo "<div style='text-align:center;min-height:187px;'>";
			echo "<div style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;text-align:left;'><a href='javascript:changeCity();' class='flright'>schimbă locația</a>Locuri de skate din jude&#355;ul t&#259;u:</div>";
			echo "Nici un loc de skate &icirc;n jude&#355;ul t&#259;u.</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>&#350;ti un loc bun de skate? <a href='".BASEDIR."membri/my/spoturi.php'>Adaug&#259;-l acum!</a></div>";
		}
	} elseif ($_GET['z'] == 2) {

		$result = dbquery("SELECT nw.news_subject, nw.news_news, nw.news_id, gj.city_judet, ju.city_name AS oras, ju.city_id AS oras_id FROM ".DB_NEWS." nw
				   LEFT JOIN ".DB_CITIES." gj ON gj.city_id='$myCity'
				   LEFT JOIN ".DB_CITIES." ju ON ju.city_judet=gj.city_judet
				   LEFT JOIN ".DB_CITIES." jt ON jt.city_id=gj.city_judet
				   WHERE nw.news_city=ju.city_id ORDER BY nw.news_datestamp DESC LIMIT 0,2");
		$rows = dbrows($result);
		if ($rows) {
			echo "<div style='min-height:187px;'>";
			$newMSG = "";
			$oldCity = 0;
			while ($data=dbarray($result)) {
				if ($oldCity != $data['oras_id']) {
					$newMSG .= ($oldCity ? ", " : "")."<a href='".BASEDIR."stiri/orase/".urltext($data['oras']).".".$data['oras_id']."'>".$data['oras']."</a>";
					$oldCity = $data['oras_id'];
				}
				$continut = stripslashes($data['news_news']);

				$continut = str_replace(array("<br />","<br>","<br/>","\n"),"<br>",$continut);
				$continut = strip_tags($continut,"<br>");
				$limit = min(stripos($continut,"<br>",4),200);
				$continut = fixRoChars(trimlink($continut,$limit));

				echo "<div style='display:block;padding:4px;' class='spacer'><a href='".BASEDIR."stiri/".urltext($data['news_subject']).".".$data['news_id']."' style='color:#DD892F;font-weight:bold;font-size:15px;'>".fixRoChars($data['news_subject'])."</a><br />$continut <a href='".BASEDIR."stiri/".urltext($data['news_subject']).".".$data['news_id']."'>Cite&#351;te tot</a></div>";
			}
			echo "</tr></table>";
			echo "</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>Mai multe din ".$newMSG.".</div>";
		} else {
			echo "<div style='text-align:center;min-height:187px;'>";
			echo "<div style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;text-align:left;'><a href='javascript:changeCity();' class='flright'>schimbă locația</a>&#350;tiri din jude&#355;ul tau:</div>";
			echo "Nu am gasit nicio stire din jude&#355;ul tau.</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>Suntem &icirc;n urm&#259; cu &#350;tirile? <a href='".BASEDIR."membri/my/stiri.php'>Pune-ne la curent!</a></div>";
		}

	} elseif ($_GET['z'] == 3) {

		$result = dbquery("SELECT us.user_name, us.user_id, us.user_avatar, us.user_yahoo, us.user_email, us.user_profileurl, gj.city_judet, jt.city_name AS judet, ju.city_name AS oras, ju.city_id AS oras_id FROM ".DB_USERS." us
				   LEFT JOIN ".DB_CITIES." gj ON gj.city_id='$myCity'
				   LEFT JOIN ".DB_CITIES." ju ON ju.city_judet=gj.city_judet
				   LEFT JOIN ".DB_CITIES." jt ON jt.city_id=gj.city_judet
				   WHERE us.user_location=ju.city_id".(iMEMBER ? " AND us.user_id != '".$userdata['user_id']."'" : "")." AND us.user_skater='2' ORDER BY us.user_joined DESC LIMIT 0,3");

		if (dbrows($result)) {
			$showMSG = true;

			while ($data=dbarray($result)) {
				if ($showMSG) {
					echo "<div style='min-height:187px;'>";
					echo "<div style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;'><a href='javascript:changeCity();' class='flright'>schimbă locația</a>Skateri din jude&#355;ul ".$data['judet'].":</div>";
					echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='spacer'><tr valign='top'>";
					$newMSG = "Skateboarding-ul &icirc;n ";
					$oldCity = "";
					$cities = 0;
					$showMSG = false;
				}
				$cities++;
				if ($oldCity != $data['oras']) {
					$newMSG .= ($cities > 1 ? ", " : "")."<a href='".BASEDIR."prin-tara/".urltext($data['oras']).".".$data['oras_id']."'>".$data['oras']."</a>";
					$oldCity = $data['oras'];
				}
				echo "<td width='33%' align='center' class='lightonhoverF' style='padding-top:4px;padding-bottom:4px;'>";
				echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."' title='".$data['user_name']."' class='side' style='font-weight:bold;font-size:14px;'>".$data['user_name']."</a><br />";
				echo showAvatar($data['user_avatar'],$data['user_email'],$data['user_yahoo']);
				echo "<br />Ora&#351;: <a href='".BASEDIR."prin-tara/".urltext($data['oras']).".".$data['oras_id']."'>".$data['oras']."</a>\n";
				echo "</td>";
			}
			echo "</tr></table>";
			echo "</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>$newMSG.</div>";
		} else {
			echo "<div style='text-align:center;min-height:187px;'>";
			echo "<div style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;text-align:left;'><a href='javascript:changeCity();' class='flright'>schimbă locația</a>Skateri din jude&#355;ul t&#259;u:</div>";
			echo "E&#351;ti singurul skater înregistrat din acest judet!</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>Invit&#259;-&#355;i prietenii!</div>";
		}
	}
	} else {
			echo "<div style='text-align:center;min-height:187px;'>";
			echo "<p style='font-size:13px;padding:5px;border-top:1px dotted #999;border-bottom:1px dotted #999;text-align:left;'>Alege ora&#351;ul t&#259;u din list&#259;.</p>";
			echo "<strong>De unde e&#351;ti?</strong><br /><br />";
			$cities = dbquery("SELECT city_id,city_name FROM ".DB_CITIES." WHERE city_type=0 ORDER BY city_name");
			$options = "";
			if (dbrows($cities)) {
				while ($oras = dbarray($cities)) {
					$options .= "<option value='".$oras['city_id']."'>".$oras['city_name']."</option>\n";
				}
			}
			echo "<select id='new_city'>$options</select><br /><br />";
			if (iMEMBER) {
				echo "<label>
				<input type='checkbox' name='saveIt' id='saveIt' value='salveaza' />
				Setează ca orașul meu.
				</label>";
			}
			echo "<br/><br/><input type=\"button\" value=\"Alege ora&#351;ul\" onclick=\"newCity();\" />";

			echo "</div>";
			echo "<div style='text-align:right;display:block;background-image:url(http://img.weskate.ro/look/degradeu.png);background-repeat:repeat-x;padding:7px;font-size:13px;border-top:1px solid #d8d9e7;'>".(!iMEMBER ? "Alegerea ta va fi salvat&#259; doar temporar." : "Po&#355;i schimba ora&#351;ul t&#259;u din profil, dac&#259; dore&#351;ti.")."</div>";

	}
}

mysql_close();
?>
