<?php
require_once "../mainfile.php";
$CuloarePagina="roz";
require_once SCRIPTS."header.php";

if (isset($_GET['trick'])) {
	$trick = urltext($_GET['trick']);
	$result = dbquery("SELECT * FROM ".DB_TRICKS." WHERE trick_url='$trick'");
	if (dbrows($result)) {
		$data = dbarray($result);
		opentable("Trick : ".$data['trick_name']);
		if (iMEMBER) {
			echo "<a style='display:inline-block;padding:3px;font-size:14px;font-weight:bold;' href='/trick/trick_editor.php?edit=".$data['trick_id']."&amp;key=".$_SESSION['user_key']."'>Îmbunătățește această pagină</a>";
		}
		echo "<div class='flright' style='width:200px;'>";
		openside("Probleme frecvente","roz");
		echo nl2br($data['trick_fbug']);
		closeside();
		if (intval($data['trick_requires'])) {
		openside("Dependențe","roz");
		}
		if (strpos($data['trick_requires'],".")) {
			$list = explode(".",$data['trick_requires']);
			foreach ($list as $d) {
				$dep_name=dbarray(dbquery("SELECT trick_url,trick_name FROM ".DB_TRICKS." WHERE trick_id=$d"));
				echo "<a href='/trick/".$dep_name['trick_url']."' class='header-link-m trickuri' style='padding:3px;'>".$dep_name['trick_name']."</a>";
			}
		} elseif (isnum($data['trick_requires'])) {
			$dep_name=dbarray(dbquery("SELECT trick_url,trick_name FROM ".DB_TRICKS." WHERE trick_id=".$data['trick_requires']));
			echo "<a href='/trick/".$dep_name['trick_url']."' class='header-link-m trickuri' style='padding:3px;'>".$dep_name['trick_name']."</a>";
		}
		if (intval($data['trick_requires'])) {
		closeside();
		}
		echo "</div>";
		echo "<div class='flleft' style='width:700px;'>";
		echo "<div class='capmain_color' style='padding:5px;font-size:16px;font-weight:bold;'>Învață ".$data['trick_name']."</div>";
		echo $data['trick_howto'];
		echo "</div>";
	} else {
		opentable("Trick : ".$_GET['trick']);
		echo "<div style='font-weight:bold;font-size:16px;padding:20px;'>Trick-ul nu există în baza de date.<br /><br />";
		if (iMEMBER) {
			echo "<a href='/trick/trick_editor.php?name=".$_GET['trick']."&amp;key=".$_SESSION['user_key']."'>Completează această pagină</a>";
		}
		echo "</div>";

	}
	echo "<div style='clear:both;'></div>";
} else {
	opentable("Trick-uri");
	$result = dbquery("SELECT trick_name,trick_url FROM ".DB_TRICKS);
	while ($data=dbarray($result)) {
		echo "<a href='/trick/".$data['trick_url']."' style='display:inline-block;margin:3px;font-weight:bold;padding:4px;'>".$data['trick_name']."</a><br />";
	}
	if (iMEMBER) {
	echo "<br /><br /><a href='/trick/trick_editor.php?key=".$_SESSION['user_key']."'>Adaugă un trick</a>";
	}
}

require_once SCRIPTS."footer.php";
?>
