<?php
if (!defined("inWeSkateCheck")) die("Acces respins");

$items_per_page=$setari['posts_per_page'];
/* VARIABILE NECESARE :
$posts
$page
$thread_id
$thread_subject
*/
if (!isset($posts) || !isset($page) || !isset($thread_id) || !isset($thread_subject)) {
	die("Necesități nesatisfăcute. Nu pot continua");
}
$result = dbquery("SELECT p.*,u.user_name,u.user_profileurl,u.user_sig,u.user_avatar,u.user_yahoo,u.user_email,u.user_points,
		e.user_name AS edit_name, e.user_profileurl AS edit_profileurl,
		q.user_name AS quote_name, qm.post_message AS quote_message
		FROM ".DB_POSTS." p
		LEFT JOIN ".DB_USERS." u ON p.post_author=u.user_id
		LEFT JOIN ".DB_USERS." e ON e.user_id=p.post_edituser
		LEFT JOIN ".DB_POSTS." qm ON qm.post_id=p.post_quote
		LEFT JOIN ".DB_USERS." q ON q.user_id=qm.post_author
		WHERE p.thread_id=".$thread_id."
		ORDER BY post_datestamp ASC
		LIMIT ".firstitem($page,$items_per_page).",$items_per_page");

if (iMEMBER) {
	echo "<div class='flright'>";
	echo "<a href='javascript:reply();' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/reply.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>răspunde</a>";
	if (iADMIN || dbcount("(thread_id)",DB_THREADS,"thread_author=".$userdata['user_id'])." AND thread_id=$thread_id") {
		$getforum = dbquery("SELECT forum_name,forum_id FROM ".DB_FORUMS." WHERE forum_cat!=0");
		$forums = "<option value='0'>Renunță / alege forum</option>";
		while ($forum = dbarray($getforum)) {
			$forums .= "<option value='".$forum['forum_id']."'>".$forum['forum_name']."</option>";
		}
		echo "<a href='javascript:deleteThread(".$thread_id.",\"".$_SESSION['user_key']."\");' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/circle_delete.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>șterge discuția</a>";
		echo "<div id='movethread1' style='display:inline-block;'><a href='javascript:moveThread(1);' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/edit.gif);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;' id='movethread1_a'>mută discuția</a><select id='movethread1_b' onchange='moveThreadTo(this.value,$thread_id,\"".$_SESSION['user_key']."\",1);' style='display:none;'>$forums</select></div>";
	}
	echo "</div>";
}
if ($posts > $items_per_page) {
	echo pagenav($page,$posts,$items_per_page,"javascript:forumpage(",2,",".$thread_id.");");}
echo "<div style='clear:both;' id='showposts'>";
$counter=firstitem($page,$items_per_page) + 1;
while ($data=dbarray($result)) {
	echo "<div id='post".$data['post_id']."'>";
	openside("#$counter : ".$thread_subject,"verde");
	//poster info...
	echo "<div class='flright smallround' style='margin:5px;border:1px solid #999;background-color:#eee;text-align:center;padding:4px;'>";
	echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."'><strong>".$data['user_name']."</strong></a><br />";
	echo showAvatar($data['user_avatar'],$data['user_email'],$data['user_yahoo']);
	echo "<br />";
	echo $data['user_points']." WSP<br />";
	echo "</div>";
	echo "<div style='font-size:13px;padding:5px;margin-bottom:5px;border-bottom:1px dotted #999;'>Postat ".showdate("forumdate datehover",$data['post_datestamp'])."</div>";
	if ($data['post_quote']) {
		echo "<div style='border-left:1px solid #555;margin:5px;padding:5px;background-color:#ddd;'><strong>".$data['quote_name']."</strong> a spus:<br /><em><span style='font-weight:bold;font-size:16px;'>„</span>".nl2br($data['quote_message'])."<span style='font-weight:bold;font-size:16px;'>”</span></em></div>";
	}
	if ((iMEMBER && $userdata['user_id'] == $data['post_author']) || iADMIN) {
		echo "<div id='post_msg_".$data['post_id']."'>";
	}
	echo parsebb($data['post_message']);
	if ((iMEMBER && $userdata['user_id'] == $data['post_author']) || iADMIN) {
		echo "</div><div id='post_msg_edit_".$data['post_id']."' class='ascuns'>";
		echo "<textarea style='width:700px;' rows='8' cols='50' id='post_newval_".$data['post_id']."'>".$data['post_message']."</textarea><br />";
		echo "<a href='javascript:saveEdit(".$data['post_id'].",\"".$_SESSION['user_key']."\");'>salvează</a> - <a href='javascript:cancelEdit(".$data['post_id'].");'>renunță</a>";
		echo "</div>";
	}

	if ($data['post_edittime'] && $data['post_edituser']) {
		echo "<div style='clear:both;border-top:1px dotted #999;margin-top:5px;padding:5px 5px 0px 5px;'>";
		echo "Ultima modificare de <a href='http://profil.weskate.ro/".$data['edit_profileurl']."'>".$data['edit_name']."</a> la ".showdate("forumdate agohover",$data['post_edittime']);
		echo "</div>";
	}

	echo "<div style='clear:both;border-top:1px dotted #999;margin-top:5px;'>";
	if (iMEMBER) {
		echo "<div class='flright'>";
		echo "<a href='javascript:reply(".$data['post_id'].",\"".str_replace("\"","",$data['user_name'])."\");' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/quote.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>răspunde cu citat</a>";
		if ($data['post_author'] == $userdata['user_id'] || iADMIN) {
			echo "<a href='javascript:editPost(".$data['post_id'].");' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/edit.gif);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>editează</a>";
			echo "<a href='javascript:deletePost(".$data['post_id'].",\"".$_SESSION['user_key']."\");' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/circle_delete.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>șterge</a>";
		}
		echo "</div>";
	}
	echo "<div style='padding:5px;width:70%;'>";
	echo ($data['user_sig'] ? parsebb($data['user_sig']) : "<em>".$data['user_name']."</em>")."</div></div><div style='clear:both;'></div>";
	closeside();
	echo "</div>";
	$counter++;
}
echo "</div>";
if (iMEMBER) {
	echo "<div class='flright'>";
	echo "<a href='javascript:reply();' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/reply.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>răspunde</a>";
	if (iADMIN) {
		echo "<a href='javascript:deleteThread(".$thread_id.",\"".$_SESSION['user_key']."\");' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/circle_delete.png);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;'>șterge discuția</a>";
		echo "<div id='movethread2' style='display:inline-block;'><a href='javascript:moveThread(2);' class='header-link-m forum smallround' style='background-image:url(http://img.weskate.ro/edit.gif);background-position:2px 50%;background-repeat:no-repeat;padding:3px 3px 3px 20px;margin:3px;display:inline-block;' id='movethread2_a'>mută discuția</a><select id='movethread2_b' onchange='moveThreadTo(this.value,$thread_id,\"".$_SESSION['user_key']."\",2);' style='display:none;'>$forums</select></div>";
	}
	echo "</div>";
}
if ($posts > $items_per_page) {
	echo pagenav($page,$posts,$items_per_page,"javascript:forumpage(",2,",".$thread_id.");");
}

?>
