<?php
require_once "../mainfile.php";
$CuloarePagina="roz";
require_once SCRIPTS."header.php";
if (!iMEMBER || !isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) redirect("index.php");
$key="?key=".$_SESSION['user_key'];
opentable("Trick editor");

if (isset($_POST['trick_save'])) {
	if (isset($_GET['edit']) && isnum($_GET['edit']) && dbcount("(trick_id)",DB_TRICKS,"trick_id=".$_GET['edit'])) {
		$edit = $_GET['edit'];
	} else { $edit=false; }
	$name = (isset($_POST['trick_name']) ? htmlsafe(trim($_POST['trick_name'])) : false);
	$url = urltext($name);
	if (dbcount("(trick_id)",DB_TRICKS,"(trick_name='$name' OR trick_url='$url')".($edit ? " AND trick_id!=$edit" : ""))) {
		$name=false;
	}
	$sinonime = (isset($_POST['trick_sinonime']) ? htmlsafe(trim($_POST['trick_sinonime'])) : false);
	$howto = (isset($_POST['trick_howto']) ? sqlsafe(trim($_POST['trick_howto'])) : false);
	$fbug = (isset($_POST['trick_fbug']) ? htmlsafe(trim($_POST['trick_fbug'])) : false);
	$dependente = "";
	for ($i=1;$i<=4;$i++) {
		if (isset($_POST["dependenta$i"]) && isnum($_POST["dependenta$i"]) && dbcount("(trick_id)",DB_TRICKS,"trick_id=".$_POST["dependenta$i"])) {
			if ($dependente) { $dependente.="."; }
			$dependente .= $_POST["dependenta$i"];
		}
	}
	if ($name && $howto) {
		if ($edit) {
			$result = dbquery("UPDATE ".DB_TRICKS." SET trick_name='$name', trick_sinonim='$sinonime', trick_howto='$howto', trick_fbug='$fbug',trick_requires='$dependente',trick_url='$url' WHERE trick_id=$edit");
			echo "<div class='notegreen'>Trick actualizat cu succes</div>";
		} else {
			$result = dbquery("INSERT INTO ".DB_TRICKS." (trick_name,trick_sinonim,trick_howto,trick_fbug,trick_requires,trick_url) VALUES ('$name', '$sinonime', '$howto', '$fbug', '$dependente', '$url')");
			$result = dbquery("SELECT trick_id FROM ".DB_TRICKS." WHERE trick_url='$url'");
			echo "<div class='notegreen'>Trick adăugat cu succes</div>";
		}
	} else {
		echo "<div class='notered'>Tutorialul sau numele trick-ului sunt prea scurte.</div>";
	}
}

add_to_head("<script language='javascript' type='text/javascript' src='/scripts/js/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript'>
	tinyMCE.init({
		mode:'specific_textareas',
		editor_selector:'tinymce',
		theme:'advanced',
		width:'100%',
		height:'500',
		language:'ro',
		entities:'60,lt,62,gt',
		document_base_url:'".$setari['siteurl']."',
		relative_urls:'false',
		convert_newlines_to_brs: false,
		forced_root_block: false,
		force_br_newlines: true,
		force_p_newlines: false,
		plugins:'advlink,insertdatetime,searchreplace,contextmenu,fullscreen',
		theme_advanced_buttons1 : 'bold, italic, underline, strikethrough,|,sub,sup,|, justifyleft, justifycenter, justifyright, justifyfull,|, forecolor, backcolor, formatselect, fontselect, fontsizeselect',
		theme_advanced_buttons2 : 'undo, redo,|, insertdate, inserttime,|, bullist, numlist,|, outdent, indent, blockquote,|, search, replace,|,link, unlink, anchor, image, cleanup ,|, hr,|,removeformat,visualaid,|,charmap,fullscreen',
		theme_advanced_buttons3 : '',
		theme_advanced_toolbar_align:'left',
		theme_advanced_toolbar_location:'top',
		theme_advanced_statusbar_location : 'bottom',
		plugin_insertdate_dateFormat:'%d-%m-%Y',
		plugin_insertdate_timeFormat:'%H:%M:%S',
		invalid_elements:'script,object,applet,iframe',
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		apply_source_formatting : true,
		convert_urls : false,
		onchange_callback : 'savedCheck',
		extended_valid_elements:'a[name|href|target|title],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]'
	});
</script>");
if (isset($_GET['edit']) && isnum($_GET['edit']) && dbcount("(trick_id)",DB_TRICKS,"trick_id=".$_GET['edit'])) {
	$edit = $_GET['edit'];
	$result = dbquery("SELECT * FROM ".DB_TRICKS." WHERE trick_id=$edit");
	$data = dbarray($result);
	$trick_howto=$data['trick_howto'];
	$trick_name=$data['trick_name'];
	$trick_sinonime=$data['trick_sinonim'];
	$trick_fbug = $data['trick_fbug'];
	for ($i=1;$i<=4;$i++){
		$dependente[$i]=0;
	}
	if (strpos($data['trick_requires'],".")) {
		$list = explode(".",$data['trick_requires']);
		$i =1;
		foreach ($list as $d) {
			$dependente[$i]=$d;
			$i++;
		}
	} elseif (isnum($data['trick_requires'])) {
		$dependente[1] = $data['trick_requires'];
	}
} else {
	$edit = 0;
	$trick_howto = isset($_POST['trick_howto']) ? $_POST['trick_howto'] : "";
	$trick_name = isset($_POST['trick_name']) ? $_POST['trick_name'] : (isset($_GET['name']) ? $_GET['name'] : "");
	$trick_sinonime = isset($_POST['trick_sinonime']) ? $_POST['trick_sinonime'] : "";
	for ($i=1;$i<=4;$i++) {
		if (isset($_POST["dependenta$i"]) && isnum($_POST["dependenta$i"])) {
			$dependente[$i] = $_POST["dependenta$i"];
		} else {
			$dependente[$i] = 0;
		}
	}
	$trick_fbug = isset($_POST['trick_fbug']) ? $_POST['trick_fbug'] : "";
}
for ($i=1;$i<=4;$i++) {
	$optiuni[$i]="<option value='0'>nici un trick</option>";
}
$result = dbquery("SELECT trick_id,trick_name FROM ".DB_TRICKS." ORDER BY trick_name ASC");
while ($get=dbarray($result)) {
	for ($i=1;$i<=4;$i++) {
		$optiuni[$i].="<option value='".$get['trick_id']."'".($dependente[$i] == $get['trick_id'] ? " selected='selected'" : "").">".$get['trick_name']."</option>";
	}
}

echo "<form name='savetrick' method='post' action='trick_editor.php$key".($edit ? "&amp;edit=$edit" : "")."'>";
echo "<table width='100%' cellpadding='4' cellspacing='0' style='background-color:#eee;border:1px solid #ccc;'><tr valign='top'><td>";
echo "Denumire trick:<br /><input type='text' name='trick_name' value='$trick_name' /><br />";
echo "Sinonime:<br /><input type='text' name='trick_sinonime' value='$trick_sinonime' /><br />";
echo "</td>";
echo "<td>Dependența 1:<br /><select name='dependenta1'>".$optiuni[1]."</select><br />";
echo "Dependența 2:<br /><select name='dependenta2'>".$optiuni[2]."</select></td>";
echo "<td>Dependența 3:<br /><select name='dependenta3'>".$optiuni[3]."</select><br />";
echo "Dependența 4:<br /><select name='dependenta4'>".$optiuni[4]."</select></td>";
echo "<td>Probleme frecvente:<br /><textarea name='trick_fbug' rows='3' cols='30'>$trick_fbug</textarea></td>";
echo "</tr>";
echo "</table>";
echo "<div style='text-align:right;background-color:#eee;border-left:1px solid #ccc;border-right:1px solid #ccc;padding:4px;'><input type='submit' value='Salvează' name='trick_save' /></div>";
echo "<textarea name='trick_howto' class='tinymce' rows='20' cols='60'>$trick_howto</textarea>";
echo "</form>";

require_once SCRIPTS."footer.php";
?>
