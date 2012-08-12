<?php
require_once "../../mainfile.php";
if (!iMEMBER) {
	echo "Te rog <a href='../conectare.php?redirto=/membri/my/'>conecteaza-te</a>.";
} else {
	if (isset($_GET['t']) && $_GET['t']=="n") {
		$result = dbquery("SELECT news_subject,news_id FROM ".DB_NEWS." WHERE news_name='".$userdata['user_id']."' AND news_draft='1'");
		if (dbrows($result)) {
			while ($data=dbarray($result)) {
				echo "<a href='stiri.php?edit=".$data['news_id']."&amp;key=".$_SESSION['user_key']."' class='ciorna-stirelink ciornalink'><span>&rsaquo;</span> ".$data['news_subject']."</a>";	
			}
		} else {
			echo "Nici o &#351;tire salvata ca ciorn&#259;.";
		}
	} elseif (isset($_GET['t']) && $_GET['t']=="a") {
		$result = dbquery("SELECT article_subject,article_id FROM ".DB_ARTICLES." WHERE article_name='".$userdata['user_id']."' AND article_draft='1'");
		if (dbrows($result)) {
			while ($data=dbarray($result)) {
			echo "<a href='articole.php?edit=".$data['article_id']."&amp;key=".$_SESSION['user_key']."' class='ciorna-articollink ciornalink'><span>&rsaquo;</span> ".$data['article_subject']."</a>";				}
		} else {
			echo "Nici un articol salvat ca ciorn&#259;.";
		}

	}

}

mysql_close();
?>
