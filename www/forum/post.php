<?php
require_once "../mainfile.php";

if (!iMEMBER) die("Doar pentru membri. Te rog (re)conectează-te.");
if (!isset($_POST["key"]) || $_POST['key'] != $_SESSION['user_key']) die("Acces respins");
if (!isset($_POST['do'])) die("Nici o acțiune specificată.");

$do = $_POST['do'];

if ($do == "reply") {

	//anti flood
	$result = dbquery("SELECT post_datestamp FROM ".DB_POSTS." WHERE post_author=".$userdata['user_id']." ORDER BY post_datestamp DESC LIMIT 1");
	if (!dbrows($result)) {
		$flood = false;
	} else {
		$data = dbarray($result);
		$flood = ((time()-$setari['flood_time']) > $data['post_datestamp'] ? false : true);
	}
	if ($flood) die("Acces respins: Prea multe postări în prea puțin timp.");
	if (!isset($_POST['thread']) || !isnum($_POST['thread']) || !dbcount("(thread_id)",DB_THREADS,"thread_id=".$_POST['thread'])) {
		die("ID discuție invalid.");
	}
	if (!isset($_POST['text']) || strlen($_POST['text']) < 5) {
		die("Răspuns prea scurt sau inexistent!");
	}
	if (!isset($_POST['quote']) || !isnum($_POST['quote'])) {
		$quote = 0;
	} else {
		$quote = $_POST['quote'];
	}
	$time = time();
	$author = $userdata['user_id'];
	$text = trim(htmlsafe(urldecode($_POST['text'])));
	$thread_id = $_POST['thread'];
	$result = dbquery("INSERT INTO ".DB_POSTS." (thread_id,post_message,post_quote,post_datestamp,post_author) VALUES ($thread_id,'$text',$quote,$time,$author)");
	$posts = dbcount("(post_id)",DB_POSTS,"thread_id=$thread_id");
	$update_thread = dbquery("UPDATE ".DB_THREADS." SET thread_postcount=$posts, thread_lastpost=$time, thread_lastuser=$author WHERE thread_id=$thread_id");
	$items_per_page=$setari['posts_per_page'];
	$page = ceil($posts/$items_per_page);
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
} else if ($do=="delpost") {
	if (!isset($_POST['id']) || !isnum($_POST['id'])) {
		die("ID postare invalid.");
	} else {
		if (iADMIN || dbcount("(post_id)",DB_POSTS,"post_id=".$_POST['id']." AND post_author=".$userdata['user_id'])) {
			$thread = dbquery("SELECT thread_id FROM ".DB_POSTS." WHERE post_id='".$_POST['id']."'");
			$result = dbquery("DELETE FROM ".DB_POSTS." WHERE post_id=".$_POST['id']);
			$result2 = dbquery("UPDATE ".DB_POSTS." SET post_quote=0 WHERE post_quote=".$_POST['id']);
			$thread = dbarray($thread); $thread = $thread['thread_id'];
			$thread_info = dbquery("SELECT post_author,post_datestamp FROM ".DB_POSTS." WHERE thread_id=$thread ORDER BY post_datestamp DESC LIMIT 1");
			$thread_info = dbarray($thread_info); $lastuser = $thread_info['post_author']; $lastpost = $thread_info['post_datestamp'];
			$updateThread = dbquery("UPDATE ".DB_THREADS." SET thread_postcount=thread_postcount-1, thread_lastuser=$lastuser, thread_lastpost=$lastpost");
			if ($result) {
				echo "<div class='notegreen' style='text-align:left;' onclick='this.className=\"ascuns\"'>Postarea a fost ștearsă cu succes!</div>";
			} else {
				echo "<div class='notered' style='text-align:left;' onclick='this.className=\"ascuns\"'>Am întâmpinat o eroare la ștergerea postării. Încearcă din nou.</div>";
			}
		} else {
			die("Access respins.");
		}
	}
} else if ($do=="delthread") {
	if (!isset($_POST['id']) || !isnum($_POST['id'])) {
		die("ID discuție invalid.");
	} else {
		if (iADMIN || dbcount("(thread_id)",DB_THREADS,"thread_author=".$userdata['user_id']." AND thread_id=".$_POST['id'])) {
			$result = dbquery("DELETE FROM ".DB_THREADS." WHERE thread_id=".$_POST['id']);
			$result2 = dbquery("DELETE FROM ".DB_POSTS." WHERE thread_id=".$_POST['id']);
			if ($result && $result2) {
				echo "<div class='notegreen' style='text-align:left;'>Discuția a fost ștearsă cu succes!</div>";
			} else {
				echo "<div class='notered' style='text-align:left;'>Am întâmpinat o eroare la ștergerea discuției.</div>";
			}
		} else {
			die("Access respins.");
		}
	}
} else if ($do=="edit") {
	if (!isset($_POST['post']) || !isnum($_POST['post']) || !dbcount("(post_id)",DB_POSTS,"post_id=".$_POST['post'])) {
		die("ID postare invalid.");
	} else if (!isset($_POST['text']) || strlen($_POST['text']) < 5) {
		die("Mesaj prea scurt.");
	} else if (!iADMIN || !dbcount("(post_id)",DB_POSTS,"post_author=".$userdata['user_id']." AND post_id=".$_POST['post'])) {
		die("Acces respins");
	} else {
		$post = $_POST['post'];
		$text = trim(htmlsafe(urldecode($_POST['text'])));
		$user = $userdata['user_id'];
		$time = time();
		
		dbquery("UPDATE ".DB_POSTS." SET post_message='$text', post_edittime=$time, post_edituser=$user WHERE post_id=$post");
		dbquery("UPDATE ".DB_SEARCH." SET search_text='".killRoChars($text)."' WHERE search_type='$type' AND search_item='$id'");

		$search = array("<", ">", "&nbsp;");
		$replace = array("&lt;", "&gt;", " ");
		echo nl2br(str_replace($search,$replace,stripslashes($_POST['text'])));
	}
} else if ($do=="move") {
	if (!isset($_POST['to']) || !isnum($_POST['to']) || !isset($_POST['thread']) || !isnum($_POST['thread'])) {
		die("ID discuție invalid.");
	} else {
		if (iADMIN || dbcount("(thread_id)",DB_THREADS,"thread_author=".$userdata['user_id']." AND thread_id=".$_POST['thread'])) {
			if (!dbcount("(forum_id)",DB_FORUMS,"forum_id=".$_POST['to']." AND forum_cat!=0")) { die("Forum destinație inexistent."); }
			$result = dbquery("UPDATE ".DB_THREADS." SET forum_id=".$_POST['to']." WHERE thread_id=".$_POST['thread']);
			if ($result) {
				echo "<div class='notegreen' style='text-align:left;'>Discuția a fost mutată cu succes. <a href='javascript:window.location.reload();'>Mergi la forum-ul discuției!</a></div>";
			} else {
				echo "<div class='notered' style='text-align:left;'>Am întâmpinat o eroare la ștergerea discuției. <a href='javascript:formpage(1);'>Reîncarcă discuția!</a></div>";
			}
		} else {
			die("Access respins.");
		}
	}
}

mysql_close();
?>
