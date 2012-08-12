<?php
if (!defined("inWeSkateCheck")) { die("Acces respins."); }

if (!isset($CuloarePagina)) {
	$CuloarePagina = "albastru";
}

function render_page($continut_panou,$peste_panou="") {
	global $userdata, $CuloarePagina, $redir_subdomain;

include "ws6.html";

echo "<table cellpadding='0' cellspacing='0' width='100%'>\n
<tr class='bara'".(PAGE_SELF == "editare_profil.php" ? " id='bara_color'" : "")."><td width='100%'>

<table cellpadding='0' cellspacing='0' width='959' align='center'><tr>

  <td style='padding-top:15px;' width='479'><img src='http://img.weskate.ro/logo.png' alt='WeSkate Logo' /></td>
  <td class='header-linie' align='right' width='300' style='padding-left:5px;padding-right:5px;padding-top:15px;'>
  	<table cellpadding='0' cellspacing='0' border='0' width='100%'>
	<tr>
		<td width='50%' style='text-align:left;'>
		<a href='http://www.weskate.ro/' class='headerlink acasa'><span style='background-image:url(http://img.weskate.ro/acasa.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Acas&#259;</span></a>
		<a href='http://www.weskate.ro/video' class='headerlink video'><span style='background-image:url(http://img.weskate.ro/video.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Video</span></a>
		<a href='http://www.weskate.ro/poze' class='headerlink poze'><span style='background-image:url(http://img.weskate.ro/poze.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Poze</span></a>
		<a href='http://www.weskate.ro/prin-tara' class='headerlink spoturi'><span style='background-image:url(http://img.weskate.ro/prin-tara.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'>Prin &#355;ar&#259;</span></a></td>
		<td width='50%' style='text-align:left;'>
		<a href='http://www.weskate.ro/stiri' class='headerlink stiri'><span style='background-image:url(http://img.weskate.ro/stiri.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> &#350;tiri</span></a>
		<a href='http://www.weskate.ro/articole' class='headerlink articole'><span style='background-image:url(http://img.weskate.ro/articole.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Articole</span></a>
		<a href='http://www.weskate.ro/forum' class='headerlink forum'><span style='background-image:url(http://img.weskate.ro/forum.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Forum</span></a>
		<a href='http://www.weskate.ro/trick' class='headerlink trickuri'><span style='background-image:url(http://img.weskate.ro/spots.png);background-repeat:no-repeat;padding-left: 20px;background-position: center left;'> Trick-uri</span></a></td>
	</tr>
	</table>
  </td>
  <td width='180' class='header-linie' style='padding-top:15px;'>";

		if (!isset($_GET['redirto'])) {
			if (isset($redir_subdomain) && isnum($redir_subdomain)) {
				$redirto = "?redirto=subd&subd=$redir_subdomain&pr=".str_replace(array("%2F","%3F"),array("/","?"),urlencode(PAGE_REQUEST));
			} else {
				$redirto = "?redirto=".str_replace(array("%2F","%3F"),array("/","?"),urlencode(PAGE_REQUEST));
			}
		} else {
			$redirto = "?".$_GET['redirto'];
			if (isset($_GET['subd']) && isnum($_GET['subd'])) {
				$redirto .= "&subd=".$_GET['subd'];
			}
			if (isset($_GET['pr']) && $_GET['pr']) {
				$redirto .= "&pr=".$_GET['pr'];
			}
		}

	if (iMEMBER) { 
		echo "<div class='header-text-m' style='display:inline-block;text-align:right;float:right;'>";
		echo "<a href='http://weskate.ro/membri/account.php'>".$userdata['user_name']."</a>";
		echo "</div>";
		echo "<div style='display:inline-block;text-align:left;padding-left:5px;'>";
		echo "<a href='http://www.weskate.ro/membri/my' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'><strong>My</strong> WeSkate</a>";
		echo "<a href='http://profil.weskate.ro/".$userdata['user_profileurl']."' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>Profil</a>";
		echo "<a href='http://www.weskate.ro/membri/conectare.php$redirto&amp;logout=".$_SESSION['user_key']."' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>Deconectare</a>";
		if (iADMIN) {
			echo "<a href='http://www.weskate.ro/admin/index.php?key=".$_SESSION['user_key']."' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>Administrare</a>";
		}
		echo "</div>";
	} else {
		echo "<span style='font-weight:bold;text-align:right;display:block;' class='header-text-m'>Vizitator</span>";
		echo "<div style='display:block;text-align:left;padding-left:5px;'>";

		echo "<a href='http://www.weskate.ro/membri/conectare.php".$redirto."' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>Conectare</a>";
		echo "<a href='http://www.weskate.ro/membri/inregistrare.php' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>&Icirc;nregistrare</a>";
		echo "<a href='http://www.weskate.ro/membri/resetareparola.php' class='header-link-m lightonhoverD' style='padding:1px 2px 1px 2px;'>Resetare parol&#259;</a>";
		echo "</div>";
	}
echo"</td>
  </tr></table>
</td></tr>
  <tr><td height='16' class='header-umbra' width='100%' ".(PAGE_SELF == "editare_profil.php" ? " id='bara_img'" : "")."></td></tr>
</table>
";


if ($peste_panou) {
	echo "<div style='width:959px;margin:0px auto 5px auto;'>".$peste_panou."</div>";
}
if ($continut_panou) {
	if (logoutMSG) {
		$logout_msg = "<div class='notegreen' id='logout_msg'><a href='javascript:void(0);' onclick='this.parentNode.style.display=\"none\";' class='side'>[închide]</a>Ai fost deconectat cu success. Te mai așteptăm!</div>";
	} else { $logout_msg = ""; }
	echo "<div class='main-container'>".$logout_msg.$continut_panou."</div>";
}
	//footer
echo "<div style='width:959px;margin:10px auto 5px auto;'>
<div class='flright'><a href='http://ajutor.weskate.ro/' class='footerlink'>Ajutor</a> / <a href='http://ajutor.weskate.ro/termeni' class='footerlink'>Termeni și condiții</a> / <a href='http://ajutor.weskate.ro/faq/' class='footerlink'>FAQ</a> / <a href='http://ajutor.weskate.ro/contact' class='footerlink'>Contact</a> / <a href='javascript:reportPage();' class='footerlink'>Raportează pagina</a></div>
WeSkate v5.1 copyright &copy; 2010 by Velici Vlad.<br />Orice material aparține autorului acestuia.
</div>";
}


function opentable($title) {
	echo "<span class='capmain spacer' style='display:block;'>".$title."</span>";
}
function closetable() {} //in the case i forgot to erase it...

function openside($title, $culoare = "gri") {
	
	echo "<div style='margin:5px;display:block;'>\n";

	echo "<div class='flright' style='width:10px;height:25px;background-image:url(http://img.weskate.ro/look/panou-dreapta.png);'></div>
	<div class='flleft' style='width:10px;height:25px;background-image:url(http://img.weskate.ro/look/panou-stanga.png);'></div>";

	echo "<div class='scapmain-title-".$culoare."' style='display:block;padding:4px;font-size:12px;font-weight:bold;background-image:url(http://img.weskate.ro/look/panou-mid.png);'>
	";
	echo $title."</div>\n";
	echo "<div class='scapmain-".$culoare."' style='padding:4px;display:block;'>";

}

function closeside() {
	echo "</div></div>\n";
}

?>
