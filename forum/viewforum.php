<?php
require_once "../mainfile.php";
$CuloarePagina = "verde";
require_once SCRIPTS."header.php";

if (!isset($_GET['forum_id']) || !isnum($_GET['forum_id'])) { redirect("index.php?err=InvalidId"); }

$items_per_page = $setari['threads_per_page'];
$result = dbquery("SELECT forum_name,forum_cat,forum_description FROM ".DB_FORUMS." WHERE forum_id=".$_GET['forum_id']);
$forum = dbarray($result);
if ($forum['forum_cat'] == 0) { redirect("index.php"); }

if (iMEMBER && isset($_POST['subject']) && isset($_POST['message']) && isset($_POST['userkey']) && $_POST['userkey'] == $_SESSION['user_key']) {
	if (strlen($_POST['subject'])<3) {
		echo "<div class='notered'>Subiect prea scurt</div>";
	} elseif (strlen($_POST['message'])<5) {
		echo "<div class='notered'>Mesaj prea scurt</div>";
	} else {
		$message=htmlsafe($_POST['message']);
		$subject=htmlsafe($_POST['subject']);
		$time=time();
		dbquery("INSERT INTO ".DB_THREADS." (thread_subject,thread_author,forum_id) VALUES ('$subject', ".$userdata['user_id'].", ".$_GET['forum_id'].")");
		$thread = dbquery("SELECT thread_id FROM ".DB_THREADS." WHERE thread_author=".$userdata['user_id']." AND forum_id=".$_GET['forum_id']);
		$thread = dbarray($thread); $thread = $thread['thread_id'];
		dbquery("INSERT INTO ".DB_POSTS." (thread_id,post_message,post_author,post_datestamp) VALUES
						 ('$thread', '$message',".$userdata['user_id'].", $time)");
		redirect("/forum/".urltext($forum['forum_name']).".f".$_GET['forum_id']."/".urltext($subject).".d$thread");
	}
}


$URLcorect = "/forum/".urltext($forum['forum_name']).".f".$_GET['forum_id'];
if (isset($_GET['page']) && isnum($_GET['page'])) {
	$URLcorect .= "-pag".$_GET['page'];
} else {
	$_GET['page'] = 1;
}

if (PAGE_REQUEST != $URLcorect) redirect($URLcorect);

set_title($forum['forum_name']." - WeSkate Forum");
set_meta("keywrods",killRoChars(keywordize($forum['forum_name'])));
set_meta("description",killRoChars($forum['forum_description']));
add_to_head("<link rel='stylesheet' href='http://weskate.ro/forum/forum.css' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='http://weskate.ro/forum/forum.js'></script>");
echo "<span class='capmain'>WeSkate Forum</span>";

echo "<div style='border-top: 1px dotted #555;border-bottom:1px dotted #555;padding:10px 5px 10px 5px;'>";
echo "<div style='margin-left:10px;'><img src='http://img.weskate.ro/bullet_black.png' alt='green bullet' align='left' /><a href='/forum/'>Forum Home</a></div>";
echo "<div style='margin-left:20px;'><img src='http://img.weskate.ro/bullet_green.png' alt='green bullet' align='left' />".$forum['forum_name']."</div>";
echo "</div>";


echo "<div class='spacer'></div>";
if (iMEMBER)
echo "<div  class='".(isset($_POST['subject']) || isset($_POST['message']) ? "vizibil" : "ascuns")." smallround' style='border:1px solid #999;width:500px;padding:5px;margin-left:auto;margin-right:auto;background:#eee;' id='newthread'>
<form action='".PAGE_REQUEST."' method='post'>
<input type='hidden' name='userkey' value='".$_SESSION['user_key']."'/>
<strong>Subiect :</strong><br /><input type='text' name='subject' style='width:495px;' id='subject'".(isset($_POST['subject']) ? " value='".$_POST['subject']."'" : "")."/><br />
<strong>Mesaj :</strong>
<textarea rows='10' cols='50' style='width:495px;' name='message' id='message'>".(isset($_POST['message']) ? $_POST['message'] : "")."</textarea><br />
<input type='submit' value='Postează' /> sau <a href='javascript:cancelThread();'>renunță</a>
</form>
</div>";

echo "<div id='viewforum'>";
$page = $_GET['page'];
$forum_id = $_GET['forum_id'];
$forum_name = $forum['forum_name'];
require_once "forum_include.php";
echo "</div>";


require_once SCRIPTS."footer.php";
?>
