<?php
require_once "../mainfile.php";

if (!iMEMBER) die ("Acces respins");
if (!isset($_POST['do'])) die("Acțiune nespecificată");
$do = $_POST['do'];

if ($do == "save") {
	if (!isset($_POST['key']) || $_POST['key'] != $_SESSION['user_key']) {
		die("Acces respins");
	} else if (!isset($_POST['panel_id']) || !isnum($_POST['panel_id'])) {
		die("ID panou invalid");
	} else {
		$result = dbquery("SELECT panel_user,panel_template,panel_title,panel_content FROM ".DB_USER_PANELS." WHERE panel_id=".$_POST['panel_id']);
		if ($data=dbarray($result)) {
			$data['panel_id']=$_POST['panel_id'];
			if ($data['panel_user'] != $userdata['user_id']) die("Acces respins");
			$step = "save";
			//getting the "encoded" content from the relevant template
			$valid_template=false;
			if (strpos($setari['profile_panels'],";")) {
				$panels = explode(";",$setari['profile_panels']);
				if (in_array($data['panel_template'],$panels) && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")) {
					$valid_template = true;
				}
			}else if ($data['panel_template'] == $setari['profile_panels'] && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")){
				$valid_template = true;
			}
			if ($valid_template) {
				require BASEDIR."panels/profile/".$data['panel_template'].".php";
				if (!isset($panel_content)) die("Conținut panou gol.");
				$title = (isset($_POST['paneltitle']) ? trim(htmlsafe(urldecode($_POST['paneltitle']))) : "fără titlu");
				$result = dbquery("UPDATE ".DB_USER_PANELS." SET panel_content='$panel_content', panel_title='$title' WHERE panel_id=".$_POST['panel_id']);
				if (!$result) die("<div class='notered'>Am întâmpinat o eroare la actualizarea panoului</div>");
				echo "<div class='notegreen' onclick='noPanelEdit(".$data['panel_id'].");'>Actualizat cu succes!</div>";
				echo "<a href='javascript:noPanelEdit(".$data['panel_id'].");'>Click pentru a vedea modificările</a>";
			} else {
				die("Template invalid sau inexistent.");
			}
		} else {
			die("Nu am găsit panoul!");
		}
	}
	
} else if ($do == "showedit" && isset($_POST['panel_id']) && isnum($_POST['panel_id'])) {

	$result = dbquery("SELECT panel_user,panel_template,panel_title,panel_content FROM ".DB_USER_PANELS." WHERE panel_id=".$_POST['panel_id']);
	if ($data = dbarray($result)) {
		if ($data['panel_user'] != $userdata['user_id']) die("Acces respins");
		$panel_id = $_POST['panel_id'];
		$panel_title = $data['panel_title'];
		$panel_content = $data['panel_content'];
	
		$step = "edit";
		$valid_template=false;
		if (strpos($setari['profile_panels'],";")) {
			$panels = explode(";",$setari['profile_panels']);
			if (in_array($data['panel_template'],$panels) && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")) {
				$valid_template = true;
			}
		}else if ($data['panel_template'] == $setari['profile_panels'] && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")){
			$valid_template = true;
		}
		if ($valid_template) {
			echo "<form method='post' action='".PAGE_SELF."' id='panel_form_".$_POST['panel_id']."' onsubmit='return submitEdit(".$_POST['panel_id'].")'>";
			echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
			echo "Titlu : <input type=text' name='paneltitle' value='".$data['panel_title']."'><br />";
			require BASEDIR."panels/profile/".$data['panel_template'].".php";
		} else {
			die("Template invalid sau inexistent.");
		}
		echo "<input type='submit' name='savepanel' value='Salvează' /> sau <a href='javascript:noPanelEdit(".$panel_id.");'>renunță</a>";
		echo "</form>";
	} else {
		echo "Nu am găsit panoul în baza de date.";
	}
} else if ($do=="render" && isset($_POST['panel_id']) && isnum($_POST['panel_id'])) {
	$step = "render";
	$result = dbquery("SELECT panel_user,panel_title,panel_id,panel_content,panel_template FROM ".DB_USER_PANELS." WHERE panel_id=".$_POST['panel_id']);
	if ($data=dbarray($result)) {
		if ($data['panel_user'] != $userdata['user_id']) {
			die("Acces respins");
		}
		if (strpos($setari['profile_panels'],";")) {
			$panels = explode(";",$setari['profile_panels']);
			if (in_array($data['panel_template'],$panels) && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")) {
				require BASEDIR."panels/profile/".$data['panel_template'].".php";
			}
		}else if ($data['panel_template'] == $setari['profile_panels'] && file_exists(BASEDIR."panels/profile/".$data['panel_template'].".php")){
			require BASEDIR."panels/profile/".$data['panel_template'].".php";
		}
	} else {
		echo "Nu am găsit panoul în baza de date.";
	}

} else if ($do=="templatelist") {
	$templates = $setari['profile_panels'];
	$order = (isset($_POST['order']) && isnum($_POST['order']) ? $_POST['order'] : false);
	if (!$order) die("Poziția panoului este incorectă");
	echo "<select name='template$order' id='template$order'>";
	if (strpos($templates,";")) {
		$templates = explode(";",$templates);
		foreach ($templates as $template) {
			echo "<option value='$template'>$template</option>";
		}
	} else {
		echo "<option value='$templates'>$templates</option>";
	}
	echo "</select> <input type='button' value='Crează' onclick='createNewPanel($order,\"".$_SESSION['user_key']."\")' /> sau <a href='javascript:cancelNewPanel($order);'>renunță</a>";
} else if ($do=="create") {
	if (!isset($_POST['key']) || $_POST['key'] != $_SESSION['user_key']) {
		die("Acces respins");
	} else if (!isset($_POST['order']) || !isnum($_POST['order'])) {
		die("Poziție invalidă sau inexistentă");
	} else {
		if (!isset($_POST['template'])) {
			 die ("Template invalid.");
		} else {
			$template = $_POST['template']; $valid_template = false;
			if (strpos($setari['profile_panels'],";")) {
				$panels = explode(";",$setari['profile_panels']);
				if (in_array($template,$panels) && file_exists(BASEDIR."panels/profile/".$template.".php")) {
					$valid_template = true;
				}
			} else if ($template == $setari['profile_panels'] && file_exists(BASEDIR."panels/profile/".$template.".php")) {
				$valid_template = true;
			}
			if (!$valid_template) die("Template invalid.");
			$order = (isset($_POST['order']) && isnum($_POST['order']) ? $_POST['order'] : false);
			if (!$order) die("Poziția panoului este incorectă");

			$result = dbquery("UPDATE ".DB_USER_PANELS." SET panel_order=panel_order+1 WHERE panel_order>=$order AND panel_user=".$userdata['user_id']);

			$result = dbquery("INSERT INTO ".DB_USER_PANELS." (panel_template,panel_title,panel_user,panel_content,panel_order) VALUES 
									('$template','panou nou',".$userdata['user_id'].",'',$order)");

			$result = dbquery("SELECT panel_id FROM ".DB_USER_PANELS." WHERE panel_user=".$userdata['user_id']." AND panel_order=$order");
			$data = dbarray($result);
			$panel_content = "";
			$panel_id = $data['panel_id'];
			$panel_title = "panou nou";
			require_once BASEDIR."look/profile_look.php";
			echo "<div id='panelbig$panel_id'>";
			openProfilePanel("<a href='javascript:editpanel(".$data['panel_id'].");' class='flright'><img src='http://img.weskate.ro/edit_mare.png' alt='edit' style='border:0px;'/></a><a class='flright' href='javascript:deletepanel(".$data['panel_id'].",\"".$_SESSION['user_key']."\");'><img src='http://img.weskate.ro/del_mare.png' alt='edit' style='border:0px;'/></a><div id='paneltitle$panel_id'>".$panel_title."</div>");
			echo "<div id='panel_c".$data['panel_id']."'>";

			echo "<form method='post' action='".PAGE_SELF."' id='panel_form_".$panel_id."' onsubmit='return submitEdit(".$panel_id.")'>";
			echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
			echo "Titlu : <input type=text' name='paneltitle' value='".$panel_title."'><br />";
			$step="edit";
			require BASEDIR."panels/profile/".$template.".php";
			echo "<input type='submit' name='savepanel' value='Salvează' /> sau <a href='javascript:noPanelEdit(".$panel_id.");'>renunță</a>";
			echo "</form>";

			echo "</div>";
			closeProfilePanel();
			echo "</div>";
		}
	}
} else if ($do=="delete") {
	if (isset($_POST['key']) && $_POST['key'] == $_SESSION['user_key'] && isset($_POST['panel_id']) && isnum($_POST['panel_id'])) {
		if (dbcount("(panel_id)",DB_USER_PANELS,"panel_id=".$_POST['panel_id']." AND panel_user=".$userdata['user_id'])) {
			$result=dbquery("DELETE FROM ".DB_USER_PANELS." WHERE panel_id=".$_POST['panel_id']);
			echo "<div class='notegreen' onclick='this.className=\"ascuns\"'>Panou șters cu succes.</div>";
		} else {
			die("Acces respins");
		}
	} else {
		die("Acces respins");
	}
}

mysql_close();
?>
