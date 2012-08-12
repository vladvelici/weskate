<?php
if (!defined("inWeSkateCheck")) die("Acces respins");

function openProfilePanel($title,$padding=5) {
	echo "<table cellpadding='0' cellspacing='0' width='100%' class='profil-panel spacer'><tr>";
	echo "<td class='profil-paneltop'>";
	echo "<div class='profil-paneltop-color'>$title</div>";
	echo "</td></tr><tr>";
	echo "<td class='profil-panelbody' style='padding:".$padding."px;'>";
}

function closeProfilePanel() {
	echo "</td></tr></table>";
}

?>
