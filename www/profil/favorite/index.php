<?php
require_once "../../mainfile.php";

if (!isset($_GET['user'])) { redirect("http://www.weskate.ro/index.php?err=InvalidName"); }
$_GET['user'] = htmlsafe(trim($_GET['user']));
$result = dbquery("SELECT user_id,user_profileurl,user_culoarepagina,user_name FROM ".DB_USERS." WHERE user_profileurl='".$_GET['user']."' LIMIT 1");
if (dbrows($result)) { $user_data = dbarray($result); } else { redirect("http://www.weskate.ro/index.php?err=NameNotFound&item=".$_GET['user']); }
$redir_subdomain = 1;
$VizProfil = $user_data['user_id'];
$CuloarePagina = $user_data['user_culoarepagina'];
require_once SCRIPTS."header.php";
add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/stilprofil.php?user_id=".$user_data['user_id']."' type='text/css' media='screen' />\n");
	echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>

	<td align='left'>
	<span style='font-size:20px;text-transform:uppercase;'>preferatele lui</span> <span class='namecolor'>".$user_data['user_name']."</span>
	</td>
	<td align='right' width='50%'><div class='MeniuRotunjit round'>";

	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."' style='border-left:0px;'>Profil</a>";
	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/blog'>Blog</a>";
	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/my'>My</a>";
	echo "<span>Favorite</span>";
	echo "<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni'>Prieteni</a>";

	echo "</div>
	</td>

	</tr></table>
	";

echo "<div style='font-size:16px;font-weight:bold;'>Pagină indisponibilă în această versiune.</div>";
require_once SCRIPTS."footer.php";
?>
