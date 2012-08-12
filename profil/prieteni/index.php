<?php
require_once "../../mainfile.php";
$redir_subdomain = 1;
require_once SCRIPTS."sistem_prieteni/functii.php";
$userurl = htmlsafe(trim($_GET['user']));
$result = dbquery("SELECT user_visibility,user_status,user_id,user_profileurl,user_name,user_culoarepagina FROM ".DB_USERS." WHERE user_profileurl='".$userurl."' LIMIT 1");
if (dbrows($result)) { $user_data = dbarray($result); } else { redirect("http://www.weskate.ro/index.php?err=Friends_InvalidID"); }
$user_id = $user_data['user_id'];
$VizProfil = $user_id;
$CuloarePagina = $user_data['user_culoarepagina'];
require_once SCRIPTS."header.php";

add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/stilprofil.php?user_id=".$user_data['user_id']."' type='text/css' media='screen' />\n");
add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/prieteni.js'></script>");
echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>
<td align='left'>
<span style='font-size:20px;text-transform:uppercase;'>Prietenii lui</span><span class='namecolor' id='username_color'>".$user_data['user_name']."</span>
</td>
<td align='right' width='50%'><div class='round MeniuRotunjit'>
<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."' style='border-left:0px;'>Profil</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/blog'>Blog</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/my'>My</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/favorite'>Favorite</a><span>Prieteni</span>";
echo "</div>
</td></tr></table>";

//user visibility check.
$ProfilVizibil = false;
		
if ($user_data['user_visibility'] == 1) { //daca-i public il vede oricine
	$ProfilVizibil = true; 
} else {
	if (iMEMBER) { //daca nu e public, minim trebuie sa fi conectat
		if ($user_data['user_id'] == $userdata['user_id']) { //daca e profilul lui il vede, normal.
			$ProfilVizibil = true;
		} elseif ($user_data['user_visibility'] == 2) { //daca e pentru membri il vede.
			$ProfilVizibil = true;
		} elseif ($user_data['user_visibility'] == 3 && friends($userdata['user_id'],$user_data['user_id'])) { 
			$ProfilVizibil = true; //daca e pt prieteni
		}
	}
}
if ($user_data['user_status'] != 0) $ProfilVizibil=false;
//end user visibility check.
if ($ProfilVizibil) {

	$result = dbquery("SELECT if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one) AS prieten,f.rel_id,
			u.user_name,u.user_profileurl,u.user_email,u.user_yahoo,u.user_avatar
			FROM ".DB_FRIENDS." f
			LEFT JOIN ".DB_USERS." u ON u.user_id=if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one)
			WHERE rel_status=2 AND (friend_one=".$user_data['user_id']." OR friend_two=".$user_data['user_id'].")");
	if (dbrows($result)) {
		echo "<div id='friends' style='margin:3px auto 3px auto; width:700px;float:left;'>";
		$i=0;
		while ($data=dbarray($result)) {
			if ($i!=0 && $i%5==0) echo "<div style='clear:both;'></div>";
			echo "<div id='friend".$data['prieten']."' style='width:100px;padding:5px;border:1px solid #555;margin:5px;text-align:center;' class='smallround lightonhoverF flleft'><a href='http://profil.weskate.ro/".$data['user_profileurl']."' style='font-weight:bold;'>".$data['user_name']."</a><br />".showAvatar($data['user_avatar'],$data['user_email'],$data['user_yahoo']).(iMEMBER && $userdata['user_id'] == $user_data['user_id'] ? "<br /><a href='javascript:deleteFriend(".$data['prieten'].",\"".$_SESSION['user_key']."\");'>șterge prieten</a>" : "")."</div>";
			$i++;
		}
		echo "</div>";
	} else {
		echo "<div style='float:left;font-size:17px;font-weight:bold;text-align:center;padding:20px;'>".$user_data['user_name']." nu are nici un prieten.</div>";
	}

	if (iMEMBER && $userdata['user_id'] == $user_data['user_id']) {
		echo "<div class='flright' style='width:200px;'>";
		//cereri de prietenie
		$result = dbquery("SELECT if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one) AS prieten,
				u.user_name,u.user_profileurl
				FROM ".DB_FRIENDS." f
				LEFT JOIN ".DB_USERS." u ON u.user_id=if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one)
				WHERE (rel_status=0 AND friend_two=".$userdata['user_id'].") OR (rel_status=1 AND friend_one=".$userdata['user_id'].")");
		if (dbrows($result)) {
			openside("Cereri",$userdata['user_culoarepagina']);
			while ($data=dbarray($result)) {
				echo "<div id='request".$data['prieten']."' style='padding:4px;' class='lightonhoverF'><a href='http://profil.weskate.ro/".$data['user_profileurl']."'><strong>".$data['user_name']."</strong></a><br /><a href='javascript:acceptFriend(".$data['prieten'].",\"".$_SESSION['user_key']."\");'>acceptă</a> - <a href='javascript:denyFriend(".$data['prieten'].",\"".$_SESSION['user_key']."\");'>refuză</a></div>";
			}
			closeside();
		}
		//astepti raspuns
		$result = dbquery("SELECT if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one) AS prieten,
				u.user_name,u.user_profileurl
				FROM ".DB_FRIENDS." f
				LEFT JOIN ".DB_USERS." u ON u.user_id=if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one)
				WHERE (rel_status=0 AND friend_one=".$userdata['user_id'].") OR (rel_status=1 AND friend_two=".$userdata['user_id'].")");
		if (dbrows($result)) {
			openside("Aștepți răspuns de la:",$userdata['user_culoarepagina']);
			while ($data=dbarray($result)) {
				echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."' class='lightonhoverF vizibil' style='padding:3px;'><strong>".$data['user_name']."</strong></a>";
			}
			closeside();
		}
		//prieteni refuzati
		$result = dbquery("SELECT if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one) AS prieten,
				u.user_name,u.user_profileurl
				FROM ".DB_FRIENDS." f
				LEFT JOIN ".DB_USERS." u ON u.user_id=if(f.friend_one=".$user_data['user_id'].",f.friend_two,f.friend_one)
				WHERE (rel_status=3 AND friend_one=".$userdata['user_id'].") OR (rel_status=4 AND friend_two=".$userdata['user_id'].")");
		if (dbrows($result)) {
			openside("Prietenii refuzate",$userdata['user_culoarepagina']);
			while ($data=dbarray($result)) {
				echo "<div style='padding:4px;' class='lightonhoverF' id='friend".$data['prieten']."'><a href='http://profil.weskate.ro/".$data['user_profileurl']."'><strong>".$data['user_name']."</strong></a><br /><a href='javascript:deleteFriend(".$data['prieten'].",\"".$_SESSION['user_key']."\");'>anulează refuzul</a></div>";
			}
			closeside();
		}
		echo "</div>";
	}
	echo "<div style='clear:both;'></div>";

} else {
	echo "<div style='width:360px;margin:3px auto 3px auto;'>";
	if ($user_data['user_visibility'] == 2) {
		echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." nu este vizibil pentru vizitatori.</span>";
		echo "<p>Acest profil este vizibil doar utilizatorilor inregistrati. Daca ai cont, conecteaza-te si vei putea vedea profilul imediat, daca nu, inregistrarea este usoara si rapida.</p>";
	} elseif ($user_data['user_visibility'] == 3) {
		echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." este vizibil doar pentru prieteni.</span>";
		echo "<p>Doar prietenii lui ".$user_data['user_name']." pot vedea acest profil. Daca doresti sa-l vezi, ca membru, poti trimite o cerere de prietenie.</p>";
	} elseif ($user_data['user_visibility'] == 4) {
		echo "<span style='font-weight:bold;'>Profilul lui ".$user_data['user_name']." este privat.</span>";
		echo "<p>Nimeni nu poate vedea acest profil in afara de proprietarul acestuia.</p>";
	}
	echo "</div>";
}

require_once SCRIPTS."footer.php";
?>
