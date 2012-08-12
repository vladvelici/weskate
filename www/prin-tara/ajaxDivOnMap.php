<?php
require_once "../mainfile.php";

if (isset($_GET['jud']) && isnum($_GET['jud'])) {

$result = dbquery("SELECT city_id,city_name FROM ".DB_CITIES."
	WHERE city_judet = '".$_GET['jud']."' 
	ORDER BY city_name");
$result2 = dbquery("SELECT city_name FROM ".DB_CITIES." WHERE city_id = '".$_GET['jud']."'");
$data2 = dbarray($result2);
echo "<div class='jud_title'><a href='javascript:closeJudet();' class='biground flright closelink'>X</a>".$data2['city_name']."</div>";
while ($data = dbarray($result)) {
	echo "<a href='/prin-tara/".urltext($data['city_name']).".".$data['city_id']."' class='vizibil spoturi header-link-m' style='padding:4px;'> <span>".$data['city_name']."</span></a>";
}


} else {
	echo "ID judet invalid.";
}
mysql_close();
?>
