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
echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>
<td align='left'>
<span style='font-size:20px;text-transform:uppercase;'>WeSkate-ul lui</span><span class='namecolor' id='username_color'>".$user_data['user_name']."</span>
</td>
<td align='right' width='50%'><div class='round MeniuRotunjit'>
<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."' style='border-left:0px;'>Profil</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/blog'>Blog</a><span>My</span><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/favorite'>Favorite</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni'>Prieteni</a>";
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

$stiri = dbcount("(news_id)",DB_NEWS,"news_draft=0 AND news_name=".$user_data['user_id']);
$stiri_all = dbcount("(news_id)",DB_NEWS,"news_draft=0");

$articole = dbcount("(article_id)",DB_ARTICLES,"article_draft=0 AND article_name=".$user_data['user_id']);
$articole_all = dbcount("(article_id)",DB_ARTICLES,"article_draft=0");

$albume_foto = dbcount("(album_id)",DB_PHOTO_ALBUMS,"album_user=".$user_data['user_id']);
$albume_foto_all = dbcount("(album_id)",DB_PHOTO_ALBUMS);

$foto = dbcount("(photo_id)",DB_PHOTOS,"photo_user=".$user_data['user_id']);
$foto_all = dbcount("(photo_id)",DB_PHOTOS);

$spot = dbcount("(spot_id)",DB_SPOT_ALBUMS,"spot_user=".$user_data['user_id']);
$spot_all = dbcount("(spot_id)",DB_SPOT_ALBUMS);

$spot_photos = dbcount("(photo_id)",DB_SPOT_PHOTOS,"photo_user=".$user_data['user_id']);
$spot_photos_all = dbcount("(photo_id)",DB_SPOT_PHOTOS);

$posts = dbcount("(post_id)",DB_POSTS,"post_author=".$user_data['user_id']);
$posts_all = dbcount("(post_id)",DB_POSTS);

$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_name=".$user_data['user_id']);
$comments_all = dbcount("(comment_id)",DB_COMMENTS);

$all = $stiri_all + $articole_all + $albume_foto_all + $foto_all + $spot_all + $spot_photos_all + $posts_all + $comments_all;
$my = $stiri + $articole + $albume_foto + $foto + $spot + $spot_photos + $posts + $comments;
$percent = ($all ? round($my*100/$all,2) : 0);

echo "<table cellpadding='4' width='500' style='border:2px solid #555;margin-top:5px;' align='center' class='smallround'>";
echo "<tr class='tbl2'><td>Obiect</td><td>".$user_data['user_name']."</td><td>total</td><td>%</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Știri</td><td>$stiri</td><td>$stiri_all</td><td>".($stiri_all ? round((100*$stiri)/$stiri_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Articole</td><td>$articole</td><td>$articole_all</td><td>".($articole_all ? round((100*$articole)/$articole_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Albume foto</td><td>$albume_foto</td><td>$albume_foto_all</td><td>".($albume_foto_all ? round((100*$albume_foto)/$albume_foto_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Fotografii</td><td>$foto</td><td>$foto_all</td><td>".($foto_all ? round((100*$foto)/$foto_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Locuri de skate</td><td>$spot</td><td>$spot_all</td><td>".($spot_all ? round((100*$spot)/$spot_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Poze locuri de skate</td><td>$spot_photos</td><td>$spot_photos_all</td><td>".($spot_photos_all ? round((100*$spot_photos)/$spot_photos_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Postări în forum</td><td>$posts</td><td>$posts_all</td><td>".($posts_all ? round((100*$posts)/$posts_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Comentarii</td><td>$comments</td><td>$comments_all</td><td>".($comments_all ? round((100*$comments)/$comments_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl2'><td>Total</td><td>$my</td><td>$all</td><td>$percent</td></tr>";
echo "</table>";

$result = dbquery("SELECT v.validator_trust, v.validator_id, t.trick_name FROM ".DB_VALIDATOR." v
		LEFT JOIN ".DB_TRICKS." t ON t.trick_id=v.validator_trick
		WHERE validator_user=".$user_data['user_id']);

while ($data2= dbarray($result)) {
	echo "<div>".$data2['trick_name']."  -  ".($data2['validator_trust'] == 0 ? "in asteptare" : ($data2['validator_trust'] == 1 ? "valid" : "invalid"))."</div>";
}


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
