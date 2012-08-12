<?php

require_once "../../mainfile.php";

if (!iMEMBER) {
	echo "Doar pentru utilizatorii inregistrati.";
} else {

if (isset($_GET['p']) && $_GET['p'] == 1) {
	if (isset($_GET['id']) && isnum($_GET['id']) && $_GET['id'] != 0) {
		$result = dbquery("SELECT photo_thumb1,photo_thumb2,photo_filename,photo_id,photo_title FROM ".DB_PHOTOS." WHERE album_id='".$_GET['id']."'");
		if (dbrows($result)) {
			echo "<table cellpadding='3' cellspacing='2' width='100%'><tr valign='middle'>";
			$c = 0;
			while ($data=dbarray($result)) {
				if ($c%5 == 0 && $c!=0) { echo "</tr><tr valign='middle'>";}
				echo "<td class='jeditImageTd'><div><img src='http://img.weskate.ro/photoalbum/".$data['photo_thumb1']."' /><div>";
				echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_thumb1']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_thumb1'])."\")'>mica</a>";
				if ($data['photo_thumb2']) {
					echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_thumb2']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_thumb2'])."\")'>medie</a>";
					echo "<a href='javascript:void(0);'  onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_filename']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_filename'])."\")'>mare</a>";
				} else {
					echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_filename']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_filename'])."\")'>medie</a>";
				}
				echo "</div></div></td>";
				$c++;

			}
			echo "</tr></table>";
		} else {
			echo "001";
		}
	} else {
		echo "002";
	}
} else if (isset($_GET['p']) && $_GET['p'] == 2) {
	if (isset($_GET['id']) && isnum($_GET['id']) && $_GET['id'] != 0) {
		echo "<div style='text-align:left;display:block;padding:3px;font-weight:bold;'><a href='javascript:void(0);' onclick='javascript:ajaxpage(\"jedit.php?p=2\",\"images_2\");'>&lsaquo;&lsaquo; Inapoi la albume</a></div>";
		$result = dbquery("SELECT photo_thumb1,photo_thumb2,photo_filename,photo_id,photo_title FROM ".DB_PHOTOS." WHERE album_id='".$_GET['id']."'");
		if (dbrows($result)) {
			echo "<table cellpadding='3' cellspacing='2' width='100%'><tr valign='middle'>";
			$c = 0;
			while ($data=dbarray($result)) {
				if ($c%5 == 0 && $c!=0) { echo "</tr><tr valign='middle'>";}
				echo "<td class='jeditImageTd'><div><img src='http://img.weskate.ro/photoalbum/".$data['photo_thumb1']."' /><div>";
				echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_thumb1']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_thumb1'])."\")'>mica</a>";
				if ($data['photo_thumb2']) {
					echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_thumb2']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_thumb2'])."\")'>medie</a>";
					echo "<a href='javascript:void(0);'  onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_filename']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_filename'])."\")'>mare</a>";
				} else {
					echo "<a href='javascript:void(0);' onclick='addImage(\"http://img.weskate.ro/photoalbum/".$data['photo_filename']."\",\"".($data['photo_title'] ? $data['photo_title'] : $data['photo_filename'])."\")'>medie</a>";
				}
				echo "</div></div></td>";
				$c++;

			}

			echo "</tr></table>";
			if ($c/5>2) {
				echo "<div style='text-align:left;display:block;padding:3px;font-weight:bold;'><a href='javascript:void(0);' onclick='javascript:ajaxpage(\"jedit.php?p=2\",\"images_2\");'>&lsaquo;&lsaquo; Inapoi la albume</a></div>";
			}
		} else {
			echo "001";
		}
	} else {
		$result = dbquery("SELECT album_id,album_title,album_thumb FROM ".DB_PHOTO_ALBUMS);
		if (dbrows($result)) {
			echo "<table cellpadding='3' cellspacing='2' width='100%'><tr valign='middle'>";
			$c=0;
			while ($data=dbarray($result)) {
				if ($c%5 == 0 && $c!=0) { echo "</tr><tr valign='middle'>"; }
				echo "<td align='center'><a href='javascript:openAlbum(".$data['album_id'].");' class='side lightonhoverF' style='display:inline-block;padding:3px;'><img src='http://img.weskate.ro/photoalbum/".$data['album_thumb']."' style='border:0pt none;' /><br /><strong>".$data['album_title']."</strong></a></td>";
				$c++;
			}
			echo "</tr></table>";
		} else {
			echo "001";
		}
	}
} else if (isset($_GET['p']) && $_GET['p'] == 4) {
	if (isset($_GET['t']) && isset($_GET['id']) && isnum($_GET['id']) && isset($_GET['key']) && $_GET['key'] == $_SESSION['user_key']) {
		if ($_GET['t'] == "a" && checkMyAccess("A",$_GET['id'])) {
			$result = dbquery("SELECT article_thumb FROM ".DB_ARTICLES." WHERE article_id='".$_GET['id']."'");
			if (dbrows($result)) {
				$data = dbarray($result);
				$thumb = $data['article_thumb'];
				if (file_exists(IMAGES."articles/thumbs/".$thumb)) {
					@unlink(IMAGES."articles/thumbs/".$thumb);
				}
				if (file_exists(IMAGES."articles/thumbs/s_".$thumb)) {
					@unlink(IMAGES."articles/thumbs/s_".$thumb);
				}
				$result = dbquery("UPDATE ".DB_ARTICLES." SET article_thumb='' WHERE article_id='".$_GET['id']."'");
				if ($result) {
					echo "<div class='notegreen'>Pictograma veche a fost ștearsă cu succes.</div>";
					echo "<span style='font-size:13px;' class='spacer vizibil'>Încarcă o noua pictogramă pentru acest articol:</span>";
					echo "<input type='file' name='article_pic' value='' /><br />";
					echo "Dimensiune maxima fisier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>";

				} else {
					echo "Ștergerea pictogramei a eșuat.<br /><br /><a href='javascript:void(0);' onclick='deleteThumb(".$_GET['id'].",\"a\");' style='font-weight:bold;'>Încearcă din nou</a>";
				}
			} else {
				echo "Articol inexistent in baza de date.";
			}
		} else if ($_GET['t'] == "n" && checkMyAccess("N",$_GET['id'])) {
			$result = dbquery("SELECT news_thumb FROM ".DB_NEWS." WHERE news_id='".$_GET['id']."'");
			if (dbrows($result)) {
				$data = dbarray($result);
				$thumb = $data['news_thumb'];
				if (file_exists(IMAGES."news/thumbs/".$thumb)) {
					@unlink(IMAGES."news/thumbs/".$thumb);
				}
				if (file_exists(IMAGES."news/thumbs/s_".$thumb)) {
					@unlink(IMAGES."news/thumbs/s_".$thumb);
				}
				$result = dbquery("UPDATE ".DB_NEWS." SET news_thumb='' WHERE news_id='".$_GET['id']."'");
				if ($result) {
					echo "<div class='notegreen'>Pictograma veche a fost ștearsă cu succes.</div>";
					echo "<span style='font-size:13px;' class='spacer vizibil'>Încarcă o nouă pictogramă pentru acesta știre:</span>";
					echo "<input type='file' name='article_pic' value='' /><br />";
					echo "Dimensiune maxima fișier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> și <strong>PNG</strong>";
				} else {
					echo "Ștergerea pictogramei a eșuat.<br /><br /><a href='javascript:void(0);' onclick='deleteThumb(".$_GET['id'].",\"n\");' style='font-weight:bold;'>Încearcă din nou</a>";
				}
			} else {
				echo "Știrea nu există în baza de date.";
			}
		} else {
			echo "Eroare: Cererea este incorecta sau accesul la acțiunea dorită este respins.<br /><br />Probabil sesiunea ta a expirat și ai rămas pe pagina curenta. Încearcă să te reconectezi.";
		}
	}
}
}//end if iMEMBER
mysql_close();
?>
