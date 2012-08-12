<?php
require_once "../mainfile.php";

if (!isset($_REQUEST['act'])) { 
	die("Acțiune inexistentă.");
}

$do = $_REQUEST['act'];

if ($do == "post") {
	if (!iMEMBER || !isset($_POST['type']) || !isset($_POST['id']) || !isnum($_POST['id']) || !isset($_POST['text']) || strlen($_POST['text']) < 3) {
		die("Am întâmpinat o eroare la adăugarea comentariului.");
	} else {
		//user key check
		if ($_POST['key'] != $_SESSION['user_key']) { die("<div class='notered'>Acces respins. Încearcă din nou.</div>"); }
		//anti flood
		$flood=true;
		$result = dbquery("SELECT comment_datestamp FROM ".DB_COMMENTS." WHERE comment_name=".$userdata['user_id']." ORDER BY comment_datestamp DESC LIMIT 1");
		if (!dbrows($result)) {
			$flood=false;
		} else {
			$check = dbarray($result);
			$flood = (time() - $check['comment_datestamp'] > $setari['flood_time'] ? false : true);
		}
		if ($flood) die("<div class='noteyellow'>Așteaptă câteva secunde înainte de a posta alt comentariu.</div>");
		$type = htmlsafe($_POST['type']);
		$id = $_POST['id'];
		$text = trim(htmlsafe(urldecode($_POST['text'])));
		$time = time();
		$result = dbquery("INSERT INTO ".DB_COMMENTS." (comment_type,comment_item_id,comment_name,comment_datestamp,comment_message) VALUES ('$type', $id, ".$userdata['user_id'].", $time, '$text')");
		if ($result) {
			$result = dbquery("SELECT comment_id FROM ".DB_COMMENTS." WHERE comment_name=".$userdata['user_id']." AND comment_datestamp=".$time);
			$data = dbarray($result);
			echo "<div id='comment_".$data['comment_id']."' class='comment'>";
			echo "<div class='comment_head'>";
			echo "<span class='flright'>";
			echo "<a href='javascript:editComment(".$data['comment_id'].");'>editează</a> / ";
			echo "<a href='javascript:deleteComment(".$data['comment_id'].",\"".$_SESSION['user_key']."\");'>șterge</a>";
			echo "</span>";
			echo "<a href='http://profil.weskate.ro/".$userdata['user_profileurl']."'>".$userdata['user_name']."</a> - <div class='agodate'>proaspăt adăugat</div>";
			echo "</div>";
			echo "<div id='comment_msg_".$data['comment_id']."' class='vizibil'>";
			echo parsebb($_POST['text']);
			echo "</div>";
			echo "<div id='comment_editdiv_".$data['comment_id']."' class='ascuns'>";
			echo "<textarea id='comment_edit_".$data['comment_id']."' rows='3' cols='48'>".$text."</textarea><br />";
			echo "<a href='javascript:comment_cancelEdit(".$data['comment_id'].")'>renunță</a> - <a href='javascript:comment_saveEdit(".$data['comment_id'].",\"".$_SESSION['user_key']."\");'>salvează</a>";
			echo "</div>";
			echo "</div>";
		}
	}	
} elseif ($do == "page") {
	if (!isset($_GET['type']) || !isset($_GET['id']) || !isnum($_GET['id'])) { die("<div class='notered'>Date esențiale nespecificate.</div>"); }

	if (isset($_GET['page']) && isnum($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		die("<div class='noteyellow'>Pagină nespecificată. <a href='javascript:comments_page(1);'>Încarcă prima pagină</a></div>");
	}

	$type = htmlsafe($_GET['type']);
	$item = $_GET['id'];

	$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_item_id=$item AND comment_type='$type'");

	if (!$comments) {
		echo "<strong>Nici un comentariu postat încă.</strong>";
	} else {
		$result = dbquery("SELECT c.*,u.user_name,u.user_profileurl FROM ".DB_COMMENTS." c 
				LEFT JOIN ".DB_USERS." u ON c.comment_name = u.user_id
				WHERE comment_type='$type' AND comment_item_id=$item
				ORDER BY comment_datestamp DESC, comment_id DESC
				LIMIT ".firstitem($page,10).",10");
		while ($data=dbarray($result)) {
			echo "<div id='comment_".$data['comment_id']."' class='comment'>";
			echo "<div class='comment_head'>";
			if (iMEMBER) {
				$canEdit = (dbcount("(comment_id)",DB_COMMENTS,"comment_name=".$userdata['user_id']." AND comment_id=".$data['comment_id']) || iADMIN ? true : false);
			} else {
				$canEdit = false;
			}
			if ($canEdit) {
				echo "<span class='flright'>";
				echo "<a href='javascript:editComment(".$data['comment_id'].");'>editează</a> / ";
				echo "<a href='javascript:deleteComment(".$data['comment_id'].",\"".$_SESSION['user_key']."\");'>șterge</a>";
				echo "</span>";
			}
			echo "<a href='http://profil.weskate.ro/".$data['user_profileurl']."'>".$data['user_name']."</a> - ".showdate("datehover longdate",$data['comment_datestamp']);
			echo "</div>";
			echo "<div id='comment_msg_".$data['comment_id']."' class='vizibil'>";
			echo nl2br($data['comment_message']);
			echo "</div>";
			if ($canEdit) {
				echo "<div id='comment_editdiv_".$data['comment_id']."' class='ascuns'>";
				echo "<textarea id='comment_edit_".$data['comment_id']."' rows='3' cols='48'>".$data['comment_message']."</textarea><br />";
				echo "<a href='javascript:comment_cancelEdit(".$data['comment_id'].")'>renunță</a> - <a href='javascript:comment_saveEdit(".$data['comment_id'].",\"".$_SESSION['user_key']."\");'>salvează</a>";
				echo "</div>";
			}
			echo "</div>";
		}
		if ($comments > 10) {
			echo pagenav($page,$comments,10,"javascript:void(0);' onclick='javascript:comments_page(",2,",\"$type\",\"$item\");");
		}
	}
} else if ($do == "del") {
	if (!isset($_GET['id']) || !isnum($_GET['id'])) { die("<div class='notered'>ID comentariu invalid.</div>"); }
	if (iMEMBER && $_GET['key']==$_SESSION['user_key'] && (dbcount("(comment_id)",DB_COMMENTS,"comment_id=".$_GET['id']." AND comment_name=".$userdata['user_id']) || iADMIN)) {
		$result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_id=".$_GET['id']);
		if ($result) { 
			echo "<div class='notegreen' onclick='javascript:document.getElementById(\"comment_".$_GET['id']."\").className=\"ascuns\";'>Comentariu șters cu succes!</div>";
		} else {
			echo "<div class='notered' onclick='javascript:document.getElementById(\"comment_".$_GET['id']."\").className=\"ascuns\";'>Am întâmpinat probleme la ștergerea comentariului.</div>";
		}
	} else {
		die("<div class='notered'>Nu ai acces să ștergi acest articol.</div>");
	}
} else if ($do == "edit") {
	if (!isset($_POST['id']) || !isnum($_POST['id'])) { die("<div class='notered'>ID comentariu invalid.</div>"); }
	if (iMEMBER && $_POST['key']==$_SESSION['user_key'] && (dbcount("(comment_id)",DB_COMMENTS,"comment_id=".$_POST['id']." AND comment_name=".$userdata['user_id']) || iADMIN)) {
		if (isset($_POST['text']) && strlen($_POST['text']) > 3) {
			$text = trim(htmlsafe(urldecode($_POST['text'])));
			$result = dbquery("UPDATE ".DB_COMMENTS." SET comment_message='$text' WHERE comment_id=".$_POST['id']);
			if (!$result) { die("<div class='notered'>Am întâmpinat o eroare la actualizarea articolului.</div>"); }
			$search = array("<", ">", "&nbsp;");
			$replace = array("&lt;", "&gt;", " ");
			echo nl2br(str_replace($search,$replace,stripslashes($_POST['text'])));
		} else {
			echo "<div class='notered'>Text prea scurt.</div>";
		}
	} else {
		echo "<div class='notered'>Nu ai acces să editezi acest articol.</div>";
	}
} else {
	die("Acțiunea nu a fost recunoscută.");
}

mysql_close();
?>
