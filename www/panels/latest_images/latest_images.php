<?php
if (!defined("inWeSkateCheck")) { die("Acces respins."); }

openside("Poze","albastru-inchis");

$result = dbquery("SELECT ph.photo_thumb1,ph.photo_id,ph.album_id,ph.photo_title,al.album_title FROM ".DB_PHOTOS." ph
		   LEFT JOIN ".DB_PHOTO_ALBUMS." al ON ph.album_id=al.album_id
		   ORDER BY RAND() DESC LIMIT 0,4");
if (dbrows($result) != 0) {

echo "<table cellpadding='0' cellspacing='0' width='100%'><tr>";
	while($data = dbarray($result)) {
		$itemdescription = trimlink($data['photo_title'], 23);
		echo "<td width='25%' align='center'><a href='/poze/".urltext($data['album_title']).".".$data['album_id']."/".($data['photo_title'] ? urltext($data['photo_title'])."_" : "")."poza".$data['photo_id']."' title='".$data['photo_title']."' class='side'>
<img src='http://img.weskate.ro/photoalbum/".$data['photo_thumb1']."' alt='".$data['photo_title']."' style='border:0px' /><br />$itemdescription</a></td>\n";
	}
echo "</tr></table>";
} else {
	echo "Nici o poza.";
}
closeside();

?>
