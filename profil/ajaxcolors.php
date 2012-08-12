<?php
require_once "../mainfile.php";

if (iMEMBER) {
	if (isset($_GET['type']) && $_GET['type']=="p") { //culorile din pagina de start a profilului
		
		$culori = array();

		$culori['color_name'] = (isset($_GET['cn']) && (strlen($_GET['cn']) == 6 || strlen($_GET['cn'])==3) ? htmlsafe($_GET['cn']) : "000000");
		$culori['background_panel_title'] = (isset($_GET['bpt']) && (strlen($_GET['bpt']) == 6 || strlen($_GET['bpt'])==3) ? htmlsafe($_GET['bpt']) : "555555");
		$culori['color_panel_title'] = (isset($_GET['cpt']) && (strlen($_GET['cpt']) == 6 || strlen($_GET['cpt'])==3) ? htmlsafe($_GET['cpt']) : "FFFFFF");
		$culori['color_panel_border'] = (isset($_GET['cpb']) && (strlen($_GET['cpb']) == 6 || strlen($_GET['cpb'])==3) ? htmlsafe($_GET['cpb']) : "CCCCCC");
		$culori['background_panel_body'] = (isset($_GET['bpb']) && (strlen($_GET['bpb']) == 6 || strlen($_GET['bpb'])==3) ? htmlsafe($_GET['bpb']) : "F3F3F3");
		$culori['color_menu_link'] = (isset($_GET['cml']) && (strlen($_GET['cml']) == 6 || strlen($_GET['cml'])==3) ? htmlsafe($_GET['cml']) : "000033");
		$culori['width_panel_border'] = (isset($_GET['wpb']) && isnum($_GET['wpb']) && $_GET['wpb'] >= 1 && $_GET['wpb'] <=7 ? $_GET['wpb'] : "2");


		//verifcam diferentele de contrast [under construction]


		$user_colors = serialize($culori);
		
		$result = dbquery("UPDATE ".DB_USERS." SET user_colors='".$user_colors."' WHERE user_id='".$userdata['user_id']."'");
		if ($result) {
			echo "<div style='background-color:#B4FFA8;padding:5px;padding-left:25px;font-size:13px;display:block;background-repeat:no-repeat;background-image:url(http://img.weskate.ro/check.gif);background-position:5px 5px;'><div class='flright'><a href='javascript:void(0);' onclick='hideSaveTool();'>Ascunde aceast&#259; bar&#259;</a></div>Noile culori au fost salvate cu succes.</div>";
		} else {
			echo "<div style='background-color:#FFB0B0;padding:5px;padding-left:25px;font-size:13px;display:block;background-repeat:no-repeat;background-image:url(http://img.weskate.ro/uncheck.gif);background-position:5px 5px;'><div class='flright'><a href='javascript:void(0);' onclick='saveColors();'>&Icirc;ncearc&#259; din nou</a> -- <a href='javascript:void(0);' onclick='setDefaults();'>Anuleaz&#259; modific&#259;rile</a> -- <a href='#'>Ajutor</a></div>Eroare: Noile culori nu au putut fi salvate.</div>";
		}
	}
} else {
	echo "Eroare: Doar pentru membri. Te rog (re)conecteaza-te.";
}

mysql_close();
?>
