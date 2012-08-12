<?php
require_once "../mainfile.php";
$items_per_page=$setari['threads_per_page'];
if (!isset($_GET['page']) || !isnum($_GET['page']) || !isset($_GET['forum']) || !isnum($_GET['forum'])
	|| !dbcount("(forum_id)",DB_FORUMS,"forum_id=".$_GET['forum'])
	|| dbcount("(thread_id)",DB_THREADS,"forum_id=".$_GET['forum']) < (firstitem($_GET['page'],$items_per_page)+1))
{
	die("Numărul paginii este prea mare sau ID-ul forumului este invalid.");
}

$forum_id = $_GET['forum'];
$page = $_GET['page'];
$getForum = dbarray(dbquery("SELECT forum_name FROM ".DB_FORUMS." WHERE forum_id=".$_GET['forum']));
$forum_name = $getForum['forum_name'];
require_once "forum_include.php";

mysql_close();
?>
