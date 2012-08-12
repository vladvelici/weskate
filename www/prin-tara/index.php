<?php
require_once "../mainfile.php";
$CuloarePagina = "mov";
require_once SCRIPTS."header.php";

add_to_head("<script type='text/javascript' src='http://weskate.ro/scripts/js/mapper.js'></script>\n");
add_to_head("<link rel='stylesheet' href='http://weskate.ro/prin-tara/spot.css' type='text/css' media='Screen' />");
add_to_head("<script type='text/javascript' src='http://weskate.ro/prin-tara/printara.js'></script>");

set_title("Skateboarding-ul din Rom&acirc;nia");
set_meta("description","Locuri de skate, skateri, shopuri si tot ce tine de skateboarding-ul din tara, categorizat pe judete si orase");
set_meta("keywords","locuri,de,skate,spoturi,skateboarding,shops,skateri,in,romania");
opentable("Prin &#355;ar&#259;");

echo "<p style='padding-left:15px;font-size:15px;'>Alege jude&#355;ul &#351;i apoi ora&#351;ul pentru a vedea skate shop-urile, locurile de skate, skaterii &icirc;nregistra&#355;i &#351;i concursurile sau alte nou&#355;&#259;ti locale legate de skateboarding.</p>";



require_once "showmap.php";

require_once SCRIPTS."footer.php";
?>
