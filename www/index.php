<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "mainfile.php";
$UseAJAX = true;
require_once BASEDIR."scripts/header.php";

if (isset($_GET['logout']) && $_GET['logout'] == "done") {
echo "<div class='notegreen'>
<img src='http://img.weskate.ro/check.gif' alt='ok' align='left' />
<img src='http://img.weskate.ro/deconectare.png' alt='deconectare' align='right' />
Ai fost deconectat cu succes.</div>";
}

echo "<table cellpadding='0' cellspacing='4' width='100%'><tr valign='top'><td width='50%'>";

echo "<div class='myZoneDiv round' style='background-color:#e9e9f8;height:100%'>";
echo "<div style='padding-right:5px;display:block;' class='flright'>";
echo "<a href='javascript:void(0);' onclick='myZone(1);' id='myzone1'>Locuri de skate</a> | ";
echo "<a href='javascript:void(0);' onclick='myZone(2);' id='myzone2'>Stiri</a> | ";
echo "<a href='javascript:void(0);' onclick='myZone(3);' id='myzone3'>Skateri noi</a>";
echo "</div>";
echo "<div style='display:block;padding-left:20px;font-weight:bold;font-size:17px;' class='spacer'>&Icirc;n zona ta...</div>";
echo "<div style='padding-top:4px;display:block;' id='myzoneDiv'></div>";
echo "<script type='text/javascript'>
function myZone(zona) {
	ajaxpage('ajax_index.php?z='+zona,'myzoneDiv');

	for (i=1;i<=3;i=i+1) {
		if (i==zona) {
			document.getElementById('myzone'+i).style.fontWeight = 'bold';
			document.getElementById('myzone'+i).style.fontSize = '14px';
			document.getElementById('myzone'+i).style.color = '#000';
		} else {
			document.getElementById('myzone'+i).style.fontWeight = 'normal';
			document.getElementById('myzone'+i).style.fontSize = '12px';
			document.getElementById('myzone'+i).style.color = '#779';
		}
	}
}
myZone(1);\n
function newCity() {\n
	var x='';
	if (document.getElementById('saveIt') && document.getElementById('saveIt').checked) { x='&s=1'; }
	ajaxpage('ajax_index.php?z=1&l='+document.getElementById('new_city').value+x,'myzoneDiv');\n
}\n
function changeCity() {\n
	ajaxpage('ajax_index.php?z=1&l=0','myzoneDiv');\n
}\n
</script>";
echo "</div>";

echo "</td><td width='50%'>";

echo "<div class='myZoneDiv round' style='background-color:#e9e9f8;'><div style='min-height:264px;'>";
echo "<div style='display:block;padding-left:20px;font-weight:bold;font-size:17px;' class='spacer'>Bun venit, ".(iMEMBER ? $userdata['user_name'] : "vizitatorule!")."</div>";
if (iMEMBER) {

$ciorne = dbcount("(blog_id)",DB_BLOG,"blog_user='".$userdata['user_id']."' AND blog_draft='1'");
if ($ciorne) {
	$ciorne_txt = ($ciorne == 1 ? 'Ai <a href=\''.BASEDIR.'membri/blog.php\'>un articol &icirc;n blog</a> salvat ca ciorna.' : 'Ai <a href=\''.BASEDIR.'membri/blog.php\'>'.$ciorne.' articole &icirc;n blog</a> salvate ca ciorne.');
	echo "<div style='font-size:14px;padding:4px;display:block;font-weight:bold;'><img src='http://img.weskate.ro/ciorne.png' alt='ciorne' style='vertical-align:middle;border:0;margin-right:5px;' /> $ciorne_txt</div>";
}
$forumposts = dbcount("(post_id)",DB_POSTS,"post_author=".$userdata['user_id']);
if (number_format($forumposts) < 1) {
	echo "<div style='font-size:14px;padding:4px;display:block;font-weight:bold;'><img src='http://img.weskate.ro/exclamation.png' alt='atentie' style='vertical-align:middle;border:0;margin-right:5px;' />Nu ai nicio postare &icirc;n forum. <a href='".BASEDIR."forum/post.php?action=newthread&forum_id=9'>&Icirc;ncepe prin a te prezenta</a>.</div>";
}

$friendRequests = dbrows(dbquery("SELECT f.rel_id,u.user_profileurl,u.user_name FROM ".DB_PREFIX."friends f
		   LEFT JOIN ".DB_USERS." u ON f.friend_one=u.user_id
		   WHERE f.friend_two='".$userdata['user_id']."' AND f.rel_status='0'"));
if ($friendRequests) {
	$requests_txt = ($friendRequests == 1 ? 'Ai <a href=\'http://profil.weskate.ro/'.$userdata['user_profileurl'].'/prieteni\'>o cerere de prietenie</a>.' : 'Ai <a href=\'http://profil.weskate.ro/'.$userdata['user_profileurl'].'/prieteni\'>'.$friendRequests.' cereri de prietenie</a>.');
	echo "<div style='font-size:14px;padding:4px;display:block;font-weight:bold;'><img src='http://img.weskate.ro/friend_requests.png' alt='friend requests' style='vertical-align:middle;border:0;margin-right:5px;' /> $requests_txt</div>";

}

echo "<table cellpadding='3' cellspacing='2' border='0' width='100%' style='margin-top:5px;border-top:1px solid #d8d9e7;'><tr valign='top'>";
echo "<td width='50%' style='font-size:13px;font-weight:bold;'>";
echo "<a href='http://profil.weskate.ro/".$userdata['user_profileurl']."' style='display:block;padding:4px;' class='lightonhoverD acasa header-link-m smallround'>Profilul meu</a>";
echo "<a href='/membri/blog.php' style='display:block;padding:4px;' class='lightonhoverD blog header-link-m smallround'>Blogul meu</a></td><td>";
echo "<a href='/forum/discutii-urmarite' style='display:block;padding:4px;' class='lightonhoverD forum header-link-m smallround'>Discutii urmarite de mine</a>";
echo "<a href='/membri/my/' style='display:block;padding:4px;' class='lightonhoverD spoturi header-link-m smallround'><strong>My</strong> WeSkate</a>";
echo "</td></tr></table>";
} else { //continut daca nu e membru :
	echo "<div style='font-size:14px;padding:4px;display:block;margin-bottom:5px;border-bottom:1px solid #d8d9e7;'><p style='font-weight:bold;'><img src='http://img.weskate.ro/exclamation.png' alt='atentie' style='vertical-align:middle;border:0;margin-right:5px;' />Te rugam sa te conectezi.</p>Membri conectati isi pot completa si personaliza profilul, isi pot face prieteni noi, pot discuta pe forum, comenta si evalua poze si articole si multe altele.<br /><br />Esti nou? Nici o problema! Inregistrarea este usoara si rapida.</div>";
	echo "<div style='padding:5px;padding-left:30px;padding-right:30px;display:block;'>";
	echo "<div style='height:32px;padding-top:5px;background-image:url(http://img.weskate.ro/register32.png);background-repeat:no-repeat;background-position:center left;padding-left:35px;' class='flright'><a href='membri/inregistrare.php' style='font-weight:bold;font-size:16px;' class='side'>&Icirc;nregistrare</a></div>";
	echo "<div style='padding-top:5px;background-image:url(http://img.weskate.ro/login32.png);background-repeat:no-repeat;background-position:center left;padding-left:35px;height:32px;' class='flleft'><a href='membri/conectare.php' style='font-weight:bold;font-size:16px;' class='side'>Conectare</a></div>";
	echo "</div>";

}

echo "</div></div>";

echo "</td></tr></table>";

require_once PANELS."latest_things/latest_things.php";

//poze :
require_once PANELS."latest_images/latest_images.php";
//adsense:



openside("Publicitate");
?>

<script type="text/javascript"><!--
google_ad_client = "pub-2403880163104258";
/* weskate5_homepage_lat */
google_ad_slot = "5367746505";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>

<?php
closeside();

require_once BASEDIR."scripts/footer.php";
?>
