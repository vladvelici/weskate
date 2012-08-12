<?php
require_once "../mainfile.php";
$CuloarePagina = "verde";
require_once SCRIPTS."header.php";

if (!isset($_GET['thread_id']) || !isnum($_GET['thread_id'])) { redirect("index.php?err=InvalidId"); }

$result = dbquery("SELECT f.forum_name,t.thread_subject,t.forum_id FROM ".DB_THREADS." t
		LEFT JOIN ".DB_FORUMS." f ON f.forum_id=t.forum_id
		WHERE thread_id=".$_GET['thread_id']);
if (!dbrows($result)) { redirect("index.php?err=InvalidId"); }

$thread = dbarray($result);

$URLcorect = "/forum/".urltext($thread['forum_name']).".f".$thread['forum_id']."/".urltext($thread['thread_subject']).".d".$_GET['thread_id'];
if (isset($_GET['page']) && isnum($_GET['page'])) {
	$URLcorect .= "-pag".$_GET['page'];
} else {
	$_GET['page']=1;
	$update_views = dbquery("UPDATE ".DB_THREADS." SET thread_views=thread_views+1 WHERE thread_id=".$_GET['thread_id']);
}
if ($URLcorect != PAGE_REQUEST) { redirect($URLcorect); }

$posts = dbcount("(post_id)",DB_POSTS,"thread_id=".$_GET['thread_id']);

set_title($thread['thread_subject']." - ".$thread['forum_name']." - WeSkate Forum");
set_meta("keywords",keywordize($thread['thread_subject']));
add_to_head("<link rel='stylesheet' href='http://weskate.ro/forum/forum.css' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='http://weskate.ro/forum/forum.js'></script>");

echo "<span class='capmain'>WeSkate Forum</span>";

echo "<div style='border-top: 1px dotted #555;border-bottom:1px dotted #555;padding:10px 5px 10px 5px;'>";
echo "<div style='margin-left:10px;'><img src='http://img.weskate.ro/bullet_black.png' alt='green bullet' align='left' /><a href='/forum/'>Forum Home</a></div>";
echo "<div style='margin-left:20px;'><img src='http://img.weskate.ro/bullet_black.png' alt='black bullet' align='left' /><a href='/forum/".urltext($thread['forum_name']).".f".$thread['forum_id']."'>".$thread['forum_name']."</a></div>";
echo "<div style='margin-left:30px;'><img src='http://img.weskate.ro/bullet_green.png' alt='green bullet' align='left' />".$thread['thread_subject']."</div>";
echo "</div>";

echo "<div class='spacer'></div>";


echo "<div id='viewthread'>";
$page = $_GET['page'];
$thread_id=$_GET['thread_id'];
$thread_subject=$thread['thread_subject'];
require_once "thread_include.php";

echo "</div>";
echo "<div style='clear:both;'></div>";

if (iMEMBER) {
	echo "<div id='replydiv' class='ascuns smallround' style='border:1px solid #999;width:500px;padding:5px;margin:3px auto 3px auto;'>";
	echo "<form method='post' action='".PAGE_REQUEST."' onsubmit='return postreply(".$_GET['thread_id'].",\"".$_SESSION['user_key']."\");'>";
	echo "<input type='hidden' name='quote' value='0' id='quote' />";
	echo "<div id='quotediv' class='ascuns'>Răspunzi începând cu un citat al lui <span id='quotename'></span>. <a href='javascript:noquote();'>renunță la citat</a></div>";
	echo "<textarea id='replymessage' name='replymessage' rows='6' cols='70' style='width:495px;'></textarea><br />";
	echo "<input type='submit' value='Postează răspuns' /> sau <a href='javascript:noreply();'>renunță</a>";
	echo "</form>";
	echo "</div>";
}

require_once SCRIPTS."footer.php";
?>
