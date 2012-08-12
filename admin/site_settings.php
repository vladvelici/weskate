<?php
require_once "../mainfile.php";
require_once SCRIPTS."header.php";

if (!iSUPERADMIN || !isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) redirect(BASEDIR."index.php");

$key = "?key=".$_SESSION['user_key'];

if (isset($_POST['new_setting']) && isset($_POST['new_value'])) {
	$name = sqlsafe($_POST['new_setting']);
	$val = sqlsafe($_POST['new_value']);
	if (array_key_exists($name)) {
		redirect("site_settings.php$key&msg=adderr");
	} else {
		$result = dbquery("INSERT INTO ".DB_SETTINGS." (setting_name,setting_value) VALUES ('$name','$val')");
		if ($result) {
			redirect("site_settings.php$key&msg=addok");
		} else {
			redirect("site_settings.php$key&msg=adderr");
		}
	}
}


if (isset($_GET['delete'])) {
	$del = sqlsafe($_GET['delete']);
	$result = dbquery("DELETE FROM ".DB_SETTINGS." WHERE setting_name='$del'");
	if ($result) {
		redirect("site_settings.php$key&msg=delok");
	} else {
		redirect("site_settings.php$key&msg=delerr");
	}
}

opentable("Setări principale site");

echo "<div><a href='/admin/index.php?key=".$_SESSION['user_key']."'>Înapoi la index-ul panoului de administrare</a></div>";
echo "<div class='noteyellow'>Orice modificare poate afecta funcționarea corectă site-ului!</div>";


if (isset($_GET['msg'])) {
	if ($_GET['msg'] == "delok") {
		echo "<div class='notegreen'>Setarea a fost ștearsă cu succes.</div>";
	} else if ($_GET['msg'] == "delerr") {
		echo "<div class='notered'>Eroare la ștergerea setării! Mai încearcă...</div>";
	} else if ($_GET['msg'] == "updok") {
		echo "<div class='notegreen'>Valoarea setării actualizată cu succes!</div>";
	} else if ($_GET['msg'] == "upderr") {
		echo "<div class='notered'>Eroare la actualizarea setării! Mai încearcă...</div>";
	} else if ($_GET['msg'] == "addok") {
		echo "<div class='notegreen'>Setarea nouă adăugată cu succes!</div>";
	} else if ($_GET['msg'] == "adderr") {
		echo "<div class='notered'>Eroare la adăugarea setării! Mai încearcă...</div>";
	}
}


if (isset($_GET['edit'])) {
	$edit = sqlsafe($_GET['edit']);
	if (array_key_exists($edit,$setari)) {

		if (isset($_POST['newval'])) {
			$newval = sqlsafe($_POST['newval']);
			$result = dbquery("UPDATE ".DB_SETTINGS." SET setting_value='$newval' WHERE setting_name='$edit'");
			if ($result) {
				redirect("site_settings.php$key&msg=updok");
			} else {
				redirect("site_settings.php$key&msg=upderr");
			}
		}

		echo "<div style='margin:5px auto 5px auto;font-size:14px;width:200px;'>";
		echo "Scrie valoarea nouă pentru <strong>$edit</strong>:<br /><br />";
		echo "<form action='site_settings.php$key&amp;edit=".$edit."' method='post'><input type='text' name='newval' /><br />";
		echo "<input type='submit' value='Actualizează' />";
		echo "</form><br />";
		echo "valoarea veche:<hr /><span style='text-align:left;display:block;'>".$setari[$edit]."</span>";
		echo "</div>";
	} else {
		redirect("site_settings.php$key&msg=upderr");
	}
} else {




	echo "<table cellpadding='4' cellspacing='2' width='100%' class='smallround' style='border:1px solid #333;'>";
	echo "<tr><td></td><td style='font-weight:bold;font-size:14px;'>Setare</td><td style='font-weight:bold;font-size:14px;'>Valoare</td></tr>";
	$class = "tbl1";
	foreach ($setari as $setting => $value) {
		if ($class=="tbl1") { $class="tbl2"; } else {$class="tbl1";}
		echo "<tr class='$class lightonhoverF'><td>
		<a href='site_settings.php$key&amp;delete=$setting' class='video' style='padding:4px;' onclick='return confirm(\"Ești sigur că vrei să ștergi?\");'><img src='http://img.weskate.ro/circle_delete.png' style='border:0px;' /></a>
		<a href='site_settings.php$key&amp;edit=$setting' class='articole' style='padding:4px;'><img src='http://img.weskate.ro/edit.gif' style='border:0px;' /></a>
		</td>
		<td>$setting</td>
		<td>$value</td></tr>";
	}
	if ($class=="tbl1") { $class="tbl2"; } else {$class="tbl1";}

	echo "<tr class='$class lightonhoverF'><td>nouă:</td>
	<td colspan='2'><form name='newsetting' action='site_settings.php$key' method='post'>Nume: <input type='text' name='new_setting'/> Valoare: <input type='text' name='new_value' /> <input type='submit' value='ADAUGĂ' /></form></td></tr>";


	echo "</table>";
}
require_once SCRIPTS."footer.php";
?>
