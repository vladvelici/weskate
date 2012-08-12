<?php
if (!defined("inWeSkateCheck")) { die("Acces respins"); }
require_once BASEDIR."scripts/handle_output.php";
require_once BASEDIR."look/theme.php";
if ($setari['maintenance'] == "1" && !iADMIN && PAGE_REQUEST != "/membri/conectare.php") {
	redirect($setari['siteurl']."sorry/intretinere.php");
}

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='ro' lang='ro'>\n";
echo "<head>\n<title>".$setari['sitename']."</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n";
echo "<meta name='description' content='".$setari['description']."' />\n";
echo "<meta name='keywords' content='".$setari['keywords']."' />\n";
echo "<link rel='stylesheet' href='http://weskate.ro/look/style.css' type='text/css' media='screen' />\n";
echo "<script type='text/javascript' src='http://weskate.ro/scripts/js/ajax.js'></script>\n";
echo '<link rel="stylesheet" href="http://weskate.ro/look/slide.css" type="text/css" media="screen" />';
echo '<script src="http://weskate.ro/scripts/js/jquery-1.3.2.min.js" type="text/javascript"></script>';
echo '<script src="http://weskate.ro/scripts/js/slide.js" type="text/javascript"></script>';

if (!isset($CuloarePagina)) {
	$CuloarePagina = "albastru";
}
echo "<link rel='stylesheet' href='http://weskate.ro/look/culori/".$CuloarePagina.".css' type='text/css' media='screen' />\n";
if (isset($UseMAPPER) && $UseMAPPER == true) {
	echo "<script type='text/javascript' src='http://weskate.ro/scripts/js/mapper.js'></script>\n";
}
if (file_exists(BASEDIR."images/favicon.ico")) { echo "<link rel='shortcut icon' href='http://img.weskate.ro/favicon.ico' type='image/x-icon' />\n"; }

if (isset($UseColorPicker) && $UseColorPicker == true) {
	echo "<script type='text/javascript' src='http://weskate.ro/includes/jscolor/jscolor.js'></script>";
}

echo "</head>\n<body>\n";
ob_start();
?>
