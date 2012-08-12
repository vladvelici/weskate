<?php
require_once "../mainfile.php";


echo "<a href='javascript:showMap();' class='stiri biground header-link-m flright' style='padding:3px 5px 3px 5px;position:relative;z-index:7;'>alege alt județ</a>";
if (isset($_GET['jud']) && isnum($_GET['jud']) && dbcount("(city_id)",DB_CITIES,"city_id=".$_GET['jud']."")) {

	$result = dbquery("SELECT city_name FROM ".DB_CITIES." WHERE city_id = '".$_GET['jud']."'");
	$data = dbarray($result);

	echo "<strong>Județul <br /><span style='font-size:13px;'>".$data['city_name']."</span></strong>";
	echo "<div style='clear:both;' class='spacer'></div>";
	if ($_GET['jud'] == 137) {
		echo "<a href='/stiri/orase/bucuresti.154' class='stiri header-link-m' style='padding:4px;'>București</a>";
	}
	$result = dbquery("SELECT city_id,city_name FROM ".DB_CITIES." WHERE city_judet='".$_GET['jud']."' ORDER BY city_name");
	while ($data = dbarray($result)) {
		echo "<a href='/stiri/orase/".urltext($data['city_name']).".".$data['city_id']."' class='stiri header-link-m' style='padding:4px;'>".$data['city_name']."</a>";	
	}


} else {
echo "ID judet invalid.";
}

mysql_close();
?>
