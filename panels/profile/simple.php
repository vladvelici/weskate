<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }

if ($step == "render") { //can use $data['panel_VAR'] VAR = [id,content,title,user,template]
	echo nl2br($data['panel_content']);
} else if ($step == "edit") { //can use just $panel_id,$panel_content and the logged in user's settings
	echo "<textarea id='panel_content_edit_$panel_id' rows='8' cols='50' style='width:100%;' name='panel_content_edit_$panel_id'>$panel_content</textarea>";
} else if ($step == "save") { //calculates $panel_content; use $data[]; like at render
	if (isset($_POST['panel_content_edit_'.$data['panel_id']])) {
		$_POST['panel_content_edit'.$data['panel_id']]=trim(htmlsafe(urldecode($_POST['panel_content_edit_'.$data['panel_id']])));
		$panel_content = $_POST['panel_content_edit'.$data['panel_id']];
	}
}

?>
