<?php
require_once "../mainfile.php";
echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='ro' lang='ro'>\n";
echo "<head>\n<title>".$setari['sitename']."</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n";
echo "<link rel='stylesheet' href='http://weskate.ro/look/style.css' type='text/css' media='screen' />\n";
echo "<link rel='shortcut icon' href='http://img.weskate.ro/favicon.ico' type='image/x-icon' />";
echo "</head><body>";



echo "<div style='margin:50px auto auto auto;width:500px;height:auto;padding:10px;vertical-align:middle;background-color:#f3f3f3;border:3px solid #ccc' class='biground'><div style='font-size:30px;font-weight:bold;' class='flright'>Lucrăm la site!</div><img src='http://img.weskate.ro/logo.png' alt='weskate logo' /><div style='clear:both;'></div><br /><br />".nl2br($setari['offline_message'])."<br /><br /> Mulțumim pentru înțelegere!</div>";

echo "</body></html>";
die();
?>
