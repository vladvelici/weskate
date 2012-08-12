<?php
require_once "../mainfile.php";
$items_per_page=$setari['posts_per_page'];
if (!isset($_GET['page']) || !isnum($_GET['page']) || !isset($_GET['thread']) || !isnum($_GET['thread'])
	|| !dbcount("(thread_id)",DB_THREADS,"thread_id=".$_GET['thread'])
	|| dbcount("(post_id)",DB_POSTS,"thread_id=".$_GET['thread']) < (firstitem($_GET['page'],$items_per_page)+1))
{
	die("Numărul paginii este prea mare sau ID-ul discuției este invalid.");
}

$thread_id = $_GET['thread'];
$posts = dbcount("(post_id)",DB_POSTS,"thread_id=$thread_id");

$page = $_GET['page'];
$get_th = dbarray(dbquery("SELECT thread_subject FROM ".DB_THREADS." WHERE thread_id=$thread_id LIMIT 1"));
$thread_subject = $get_th['thread_subject'];

// /look/theme.php not included, but the next functions are required:
function openside($title, $culoare = "gri") {
	echo "<div style='margin:5px;display:block;'>\n";
	echo "<div class='flright' style='width:10px;height:25px;background-image:url(http://img.weskate.ro/look/panou-dreapta.png);'></div>
	<div class='flleft' style='width:10px;height:25px;background-image:url(http://img.weskate.ro/look/panou-stanga.png);'></div>";
	echo "<div class='scapmain-title-".$culoare."' style='display:block;padding:4px;font-size:12px;font-weight:bold;background-image:url(http://img.weskate.ro/look/panou-mid.png);'>";
	echo $title."</div>\n";
	echo "<div class='scapmain-".$culoare."' style='padding:4px;display:block;'>";
}
function closeside() {	echo "</div></div>\n";	}

require_once "thread_include.php";


mysql_close();
?>
