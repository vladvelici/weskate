<?php
require_once "../../mainfile.php";
$CuloarePagina = "galben";
require_once SCRIPTS."header.php";
if (!iMEMBER) { redirect("../conectare.php?redirto=".urlencode(PAGE_REQUEST)); }
if (!isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) redirect(BASEDIR."index.php");

$key="?key=".$_SESSION['user_key'];

add_to_head("<link rel='stylesheet' href='my.css' type='text/css' media='screen' />");
add_to_head("<script language='javascript' type='text/javascript' src='myeditor.js'></script>");

if (isset($_GET['delete'])) {
	if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("A",$_GET['edit'])) {
		$edit = $_GET['edit'];
		$result = dbquery("SELECT article_subject,article_datestamp,article_thumb FROM ".DB_ARTICLES." WHERE article_id='$edit'");
		$data = dbarray($result);
		$subject = $data['article_subject'];
		$check = md5($data['article_subject'].$data['article_datestamp']);
		if ($check != $_GET['delete']) {
			redirect(PAGE_SELF.$key."&amp;edit=$edit&msg=del_deny");
		} else {
			$thumb = $data['article_thumb'];
			$result = dbquery("DELETE FROM ".DB_ARTICLES." WHERE article_id='$edit'");
			if (!$result) redirect(PAGE_SELF.$key."&amp;msg=del_err");
			if (file_exists(IMAGES."articles/thumbs/".$thumb)) {
				@unlink(IMAGES."articles/thumbs/".$thumb);
			}
			if (file_exists(IMAGES."articles/thumbs/s_".$thumb)) {
				@unlink(IMAGES."articles/thumbs/s_".$thumb);
			}
			redirect(PAGE_SELF.$key."&amp;msg=del_ok");
		}
		cleanup("A",$edit);
	} else {
		redirect(PAGE_SELF.$key."&amp;msg=del_err");
	}
} else {

add_to_head("<script language='javascript' type='text/javascript' src='myimages.js'></script>
<script language='javascript' type='text/javascript' src='/scripts/js/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript'>

	tinyMCE.init({
		mode:'textareas',
		theme:'advanced',
		width:'100%',
		height:'500',
		language:'ro',
		entities:'60,lt,62,gt',
		document_base_url:'".$setari['siteurl']."',
		relative_urls:'false',
		convert_newlines_to_brs: false,
		forced_root_block: false,
		force_br_newlines: true,
		force_p_newlines: false,
		plugins:'advlink,insertdatetime,searchreplace,contextmenu,fullscreen',
		theme_advanced_buttons1 : 'bold, italic, underline, strikethrough,|,sub,sup,|, justifyleft, justifycenter, justifyright, justifyfull,|, forecolor, backcolor, formatselect, fontselect, fontsizeselect',
		theme_advanced_buttons2 : 'undo, redo,|, insertdate, inserttime,|, bullist, numlist,|, outdent, indent, blockquote,|, search, replace,|,link, unlink, anchor, image, cleanup ,|, hr,|,removeformat,visualaid,|,charmap,fullscreen',
		theme_advanced_buttons3 : '',
		theme_advanced_toolbar_align:'left',
		theme_advanced_toolbar_location:'top',
		theme_advanced_statusbar_location : 'bottom',
		plugin_insertdate_dateFormat:'%d-%m-%Y',
		plugin_insertdate_timeFormat:'%H:%M:%S',
		invalid_elements:'script,object,applet,iframe',
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		apply_source_formatting : true,
		convert_urls : false,
		onchange_callback : 'savedCheck',
		extended_valid_elements:'a[name|href|target|title],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]'
	});
checkDirty();
</script>
");

if (isset($_POST['save'])) {
	$edit = false;
	$errors = array();
	if (isset($_POST['draft'])) {
		$draft = 1;
	} else {
		$draft = 0;
	}

	if (isset($_GET['edit']) && isnum($_GET['edit'])) {
		if (checkMyAccess("A",$_GET['edit'])) {
			$edit = $_GET['edit'];
		} else {
			$errors[1] = 1;
			$draft = 1;
		}
	}

	if (isset($_POST['cat']) && isnum($_POST['cat']) && dbcount("(article_cat_id)",DB_ARTICLE_CATS,"article_cat_id='".$_POST['cat']."'")) {
		$cat = $_POST['cat'];
	} else {
		$errors[2] = 2;
		$draft = 1;
	}

	if (isset($_POST['photoalbum']) && isnum($_POST['photoalbum']) && dbcount("(album_id)",DB_PHOTO_ALBUMS,"album_id='".$_POST['photoalbum']."'")) {
		$photoalbum = $_POST['photoalbum'];
	} else {
		$photoalbum = 0;
	}

	if (isset($_POST['subject']) && trim(htmlsafe($_POST['subject'])) != "") {
		$subject = trim(htmlsafe($_POST['subject']));
	} else {
		$subject = "Ciorna ".showdate("shortdate",time());
		$errors[3] = 3;
		$draft = 1;
	}

	if (isset($_POST['article']) && strlen($_POST['article']) > 0) {
		$articol = sqlsafe(fixRoChars($_POST['article']));
	} else {
		$articol = "";
		$draft = 1;
		$errors[4] = 4;
	}


	$breaks = "n";
	$comments = (isset($_POST['comments']) ? 1 : 0);
	$ratings = (isset($_POST['ratings']) ? 1 : 0);

	$snippet = trimlink(strip_tags(nl2br($articol)),200);

	$keywords = (isset($_POST['keywords']) && strlen($_POST['keywords']) > 5 ? keywordize(trim(htmlsafe($_POST['keywords']))) : keywordize($subject));
	$descriere = (isset($_POST['descriere']) && strlen($_POST['descriere']) > 7 ? trim(fixRoChars(htmlsafe($_POST['descriere']))) : trimlink($snippet,70));

	if (isset($_FILES['article_pic']) && is_uploaded_file($_FILES['article_pic']['tmp_name'])) {
		$pic_types = array(".gif",".jpg",".jpeg",".png");
		$article_pic = $_FILES['article_pic'];
		$article_ext = strtolower(strrchr($article_pic['name'],"."));
		if (!preg_match("/^[-0-9A-Z_\.\[\]\s]+$/i", $article_pic['name'])) {
			$errors[5] = 5;
		} elseif ($article_pic['size'] > $setari['photo_max_b']) {
			$errors[6] = 6;
		} elseif (!in_array($article_ext, $pic_types)) {
			$errors[7] = 7;
		} else {
			require_once SCRIPTS."photo_functions_include.php";
			@unlink(IMAGES."articles/thumbs/temp".$article_ext);
			move_uploaded_file($article_pic['tmp_name'], IMAGES."articles/thumbs/temp".$article_ext);
			chmod(IMAGES."articles/thumbs/temp".$article_ext, 0644);
			$imagefile = @getimagesize(IMAGES."articles/thumbs/temp".$article_ext);
			$article_thumb = image_exists(IMAGES."articles/thumbs/", md5($article_pic['name']).$article_ext);
			createFixedThumb($imagefile[2], IMAGES."articles/thumbs/temp".$article_ext, IMAGES."articles/thumbs/".$article_thumb, 550, 300);
			createFixedThumb($imagefile[2], IMAGES."articles/thumbs/temp".$article_ext, IMAGES."articles/thumbs/s_".$article_thumb, 100, 100);
			@unlink(IMAGES."articles/thumbs/temp".$article_ext);
		}
	}

	if (isset($_POST['sursatxt']) && isset($_POST['sursaurl'])) {
		$sTxt = $_POST['sursatxt'];
		$sUrl = $_POST['sursaurl'];
		if (is_array($sTxt)) {
			$sources = "";
			foreach ($sTxt as $nr => $text) {
				if ($text || $sUrl[$nr]) {
					if ($text) {
						$sources .= trim(htmlsafe(str_replace(array("\n","->")," ",$text)));
					}
					if ($sUrl[$nr]) {
						$sources .= "->".trim(htmlsafe(str_replace(array("\n","->")," ",$sUrl[$nr])));
					}
					$sources .= "\n";
				}
			}
		}
	} else {
		$sources="";
	}

	
	//saving into DB:
	if ($edit) {
		$result = dbquery("UPDATE ".DB_ARTICLES." SET article_subject = '$subject', article_article='$articol', article_draft='$draft', article_snippet='$snippet', article_keywords='$keywords', article_descriere = '$descriere', article_photoalbum='$photoalbum', article_sources='$sources', article_allow_comments='$comments', article_allow_ratings='$ratings'".(isset($article_thumb) ? ", article_thumb='$article_thumb' " : "").(isset($cat) ? ", article_cat='$cat' " : "")." WHERE article_id='$edit'");
		if (!$result) { 
			$errors[8] = 8;
		} else {
			if (!$draft) {
				indexItem($edit,"A",killRoChars($subject),strip_tags(killRoChars($articol)),$keywords,0,0,0);
			} else {
				deleteIndex("A",$edit);
			}
		}
	} else {
		$timestamp = time();
		$result = dbquery("INSERT INTO ".DB_ARTICLES."
				(article_cat, article_snippet, article_subject, article_draft, article_article, article_descriere, article_keywords, article_breaks, article_name, article_datestamp, article_allow_comments, article_allow_ratings, article_photoalbum, article_thumb, article_sources)
				VALUES
				('".(isset($cat) ? $cat : 0)."', '$snippet', '$subject', '$draft', '$articol', '$descriere', '$keywords', '$breaks', '".$userdata['user_id']."', '$timestamp', '$comments', '$ratings', '$photoalbum', '".(isset($article_thumb) ? $article_thumb : "")."', '$sources')");
		if (!$result) {
			$errors[8] = 8;
		} else {
			$result2 = dbquery("SELECT article_id FROM ".DB_ARTICLES." WHERE article_datestamp = '$timestamp' AND article_name = '".$userdata['user_id']."'");
			$data2 = dbarray($result2);
			$_GET['edit'] = $data2['article_id'];
			if (!$draft) {
				indexItem($edit,"A",killRoChars($subject),strip_tags(killRoChars($articol)),$keywords,0,0,$timestamp);
			}
		}
	}

}



if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("A",$_GET['edit'])) {
	$edit = $_GET['edit'];
	$result = dbquery("SELECT * FROM ".DB_ARTICLES." WHERE article_id='".$edit."'");
	$data = dbarray($result);
	$cat = $data['article_cat'];
	$photoalbum = $data['article_photoalbum'];
	$subject = $data['article_subject'];
	$snippet = $data['article_snippet'];
	$article = $data['article_article'];
	$draft = $data['article_draft'];
	$breaks = $data['article_breaks'];
	$comments = $data['article_allow_comments'];
	$ratings = $data['article_allow_ratings'];
	$keywords = $data['article_keywords'];
	$descriere = $data['article_descriere'];
	$thumb = $data['article_thumb'];
	$sources = $data['article_sources'];
	$datestamp = $data['article_datestamp'];
} else {
	$edit = false;
	$cat = 0;
	$photoalbum = 0;
	$subject = "";
	$snippet = "";
	$article = "";
	$draft = 1;
	$breaks = "n";
	$comments = 1;
	$ratings = 1;
	$keywords = "";
	$descriere = "";
	$thumb = "";
	$sources = "";
}

$ciorne = dbcount("(article_id)",DB_ARTICLES,"article_name='".$userdata['user_id']."' AND article_draft='1'".($edit ? " AND article_id!='$edit'" : ""));
$articole = dbcount("(article_id)",DB_ARTICLES,"article_name='".$userdata['user_id']."' AND article_draft='0'".($edit ? " AND article_id!='$edit'" : ""));

echo "<table cellpadding='4' cellspacing='0' width='100%'><tr>";
if ($ciorne || $articole) echo "<td id='myContainerB'><a href='javascript:toggleMyContainer();' id='myContainerBa'>&darr;</a></td>";
echo "<td style='border-bottom:2px solid #999;'><span class='capmain_color' style='font-size:30px;padding-bottom:7px;'>".($subject ? $subject : "Articol nou")."</span></td>
<td align='right' class='my-navigationtd' style='border-bottom:2px solid #999;'>
<a href='/membri/my/' class='my-albastru'>tot</a> <strong>-</strong> 
<a href='stiri.php' class='my-oranj'>&#351;tiri</a> <strong>-</strong> 
<span class='my-galben'>articole</span> <strong>-</strong> 
<a href='spoturi.php' class='my-mov'>locuri de skate</a> <strong>-</strong> 
<a href='video.php' class='my-rosu'>video</a></td>
</tr></table>";


if ($ciorne || $articole) {
	echo "<div  id='myContainer'>";
	echo "<table cellpadding='3' cellspacing='3' style='color:#000;background-color:#fff;'><tr valign='top'>";
	$limit = 33 - ($ciorne % 6);
	$getArts = dbquery("SELECT article_id,article_subject,article_draft FROM ".DB_ARTICLES." WHERE article_name='".$userdata['user_id']."' ORDER BY article_draft DESC, article_datestamp DESC LIMIT 0,$limit");
	$t = 0; //0=default. 1=primul articol. 2=urmatoarele articole
	$c = 0;
	echo "<td style='width:200px;'>";
	while ($data = dbarray($getArts)) {
		if ($data['article_draft'] == 0 && $t == 0) {
			$t = 1;
		}
		if ($c == 0) {
			if ($t == 0) {
				echo "<strong>CIORNELE MELE</strong>";
			} else {
				echo "<strong>ARTICOLELE MELE</strong>";
				$t=2;
			}
			$c++;
		} else if (($c%6 == 0 || $t==1) && $c != 0) {
			echo "</td><td style='width:200px;'>";
			$c = 6;
			if ($t==1) { echo "<strong>ARTICOLELE MELE</strong>"; $c++; $t = 2; }
		}
		echo "<a href='".PAGE_SELF.$key."&amp;edit=".$data['article_id']."' class='articole my-navlink ciornalink' style='font-size:12px;padding:2px;' title='".$data['article_subject']."'>".trimlink($data['article_subject'],26)."</a>";
		$c++;
	}
	echo "</td>";
	echo "</tr></table>";
	echo "</div>";
}

//NOTIFICARI, ERORI
if (isset($_GET['msg']) && $_GET['msg'] == "del_err") {
	echo "<div class='notered'>A fost intampinata o eroare la stergerea articolului. Incearca din nou.</div>";
} else if (isset($_GET['msg']) && $_GET['msg'] == "del_deny") {
	echo "<div class='notered'>Eroare: Nu ai drepturile necesare pentru a sterge articolul sau incerci sa stergi un articol inexistent.</div>";
} else if (isset($_GET['msg']) && $_GET['msg'] == "del_ok") {
	echo "<div class='notegreen'>Articolul a fost sters cu succes.</div>";
}
if (isset($errors) && is_array($errors)) {
	$eroareMsg = "";
	$forcedDraft = false;
	if (in_array(1,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#001): Accesul la articolul dorit a fost respins. Un articol nou a fost creat.</span>";
		$forcedDraft = true;
	}
	if (in_array(2,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#002): Categoria in care doresti sa salvezi articolul este invalida. Alege alta.</span>";
		$forcedDraft = true;
	}
	if (in_array(3,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#003): Te rugam sa completezi subiectul articolului.</span>";
		$forcedDraft = true;
	}
	if (in_array(4,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#004): Articolul pe care incerci sa-l salvezi este gol.</span>";
		$forcedDraft = true;
	}
	if (in_array(5,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#005): PICTOGRAMA : Numele fisierului imagine este invalid. Articolul a fost salvat fara pictograma.</span>";
	}
	if (in_array(6,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#006): PICTOGRAMA : Dimensiunea fisierului imagine trebuie sa fie de maxim ".parsebytesize($setari['photo_max_b']).". Articolul a fost salvat fara pictograma.</span>";
	}
	if (in_array(7,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#007): PICTOGRAMA : Tipul fisierului incarcat este invalid. Sunt permise doar imagini <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>.</span>";
	}
	if (in_array(8,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#008): Eroare la salvarea articolului/modificarilor in baza de date.</span>";
	}
	if ($eroareMsg) {
		echo "<span style='display:block;padding:2px;font-size:14px;font-weight:bold;color:#0c0;'>Articolul a fost salvat, dar au aparut urmatoarele probleme:</span>";
	}
	if ($forcedDraft) {
		echo "<span style='display:block;padding:2px;font-size:13px;color:#990;'>Articolul a fost salvat fortat ca ciorna din cauza erorilor #001, #002, #003 sau #004.</span>";
	}
	if ($eroareMsg) {
		echo $eroareMsg;
	} else {
		echo "<div class='notegreen'>Articolul a fost salvat cu succes.</div>";
	}
}
//END NOTIFICARI, ERORI


echo "<form name='inputform' method='post' action='".PAGE_SELF.$key.($edit ? "&amp;edit=".$edit : "")."' onsubmit='return ValidateForm();'  enctype='multipart/form-data'>\n";


echo "<table cellpadding='4' cellspacing='0' width='100%' style='margin-top:5px;'>";
echo "<tr class='tbl1'><td style='border-top:1px solid #CCCCCC;border-left:1px solid #CCCCCC;'>";
echo "<div style='display:block;' class='spacer'><strong>Subiect</strong><br />
<input name='subject' type='text' value='$subject' style='width:200px;' class='textbox' /></div>";

$result = dbquery("SELECT article_cat_name,article_cat_id FROM ".DB_ARTICLE_CATS." ORDER BY article_cat_name");
$cat_opts = ""; $sel = "";
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		if (isset($cat)) $sel = ($cat == $data['article_cat_id'] ? " selected='selected'" : "");
		$cat_opts .= "<option value='".$data['article_cat_id']."'$sel>".$data['article_cat_name']."</option>\n";
	}
}
echo "<div style='display:block;'><strong>Categorie</strong><br /><select name='cat' style='width:200px;' class='textbox'>$cat_opts</select></div>";
echo "</td><td style='border-top:1px solid #CCCCCC;'>";

$result = dbquery("SELECT album_id,album_title FROM ".DB_PHOTO_ALBUMS." ORDER BY album_title");
$photo_opts = "<option value='0'>-nici unul-</option>"; $sel = "";
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		if (isset($photoalbum)) $sel = ($photoalbum == $data['album_id'] ? " selected='selected'" : "");
		$photo_opts .= "<option value='".$data['album_id']."'$sel>".$data['album_title']."</option>\n";
	}
}
echo "<div style='display:block;' class='spacer'><div id='att' style='padding:3px;display:inline-block;'><strong>Ataseaza album foto</strong><br /><select name='photoalbum' onchange='refreshPageOne(this.value);' style='width:200px;' id='albm' class='textbox'>$photo_opts</select></div></div>";

echo "<div style='display:block;padding:3px;'><strong>Ataseaza video</strong><br /><select name='videofile' style='width:200px;' class='textbox'>$photo_opts</select></div>";

echo "</td><td style='border-top:1px solid #CCCCCC;'>";
echo "<div style='display:block;' class='spacer'><strong>Cuvinte cheie</strong> <span class='small'>(separati cu virgula)</span><br /><input name='keywords' type='text' value='$keywords' style='width:200px;' class='textbox' /></div>";
echo "<div style='display:block;'><strong>Scurta descriere</strong><br /><input name='descriere' type='text' value='$descriere' style='width:200px;' class='textbox' /></div>";
echo "</td><td style='border-top:1px solid #CCCCCC;border-right:1px solid #CCCCCC;'>";
echo "<label class='lightonhoverF' style='display:block;padding:4px;'><input type='checkbox' name='comments' value='y'".($comments ? " checked='checked'" : "")."/>Activeaza comentarii</label>";
echo "<label class='lightonhoverF' style='display:block;padding:4px;'><input type='checkbox' name='ratings' value='y'".($ratings ? " checked='checked'" : "")."/>Activeaza evaluari</label>";
echo "<label class='lightonhoverF' style='display:block;padding:4px;'><input type='checkbox' name='draft' value='y'".($draft ? " checked='checked'" : "")."/>Salveaza ca ciorna</label>";
echo "</td></tr>";

echo "<tr valign='middle'><td colspan='4' id='savebar'>";
echo "<div class='flright'>".($edit ? "<a href='".PAGE_SELF."$key' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/new.png); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF' onclick='return sureNew();'>Articol nou</a><a href='".PAGE_SELF.$key."&amp;edit=$edit&amp;delete=".md5($subject.$datestamp)."' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/uncheck.gif); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF' onclick='return deleteItem();'>Sterge articolul</a>" : "")."<input type='submit' value='Salveaza' name='save' /></div>";
echo "<span id='savebarText'>Nu te descurci? Viziteaza centrul de ajutor WeSkate.</span><div id='saveTimeC' style='display:none;'>(de <div id='notSavedTime' style='display:inline;'></div> minute)</div>";
echo "</td></tr>";

echo "<tr><td class='ths tbl2' colspan='4' style='padding:0px;'>";

$thumbError = ($thumb && (!file_exists(IMAGES."articles/thumbs/".$thumb) || !file_exists(IMAGES."articles/thumbs/s_".$thumb)) ? true : false);

echo "<div class='toggleTabBar'>";
echo "<a href='javascript:void(0);' onclick='toggleTab(1);' id='toggleTabButton1' class='toggleTabButton' title='Alege o pictograma pentru acest articol'".($thumbError ? " style='background-image:url(http://img.weskate.ro/info.png);background-position:center left;background-repeat:no-repeat;padding-left:17px;'" : "").">Pictograma</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(2);' id='toggleTabButton2' class='toggleTabButton' title='Adauga o imagine articolului'>Imagini</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(3);' id='toggleTabButton3' class='toggleTabButton' title='Spune-ne de unde te-ai inspirat'>Surse...</a>";
echo "</div>";

echo "<div class='toggleTabContainer'>";
echo "<div id='menuitem1' class='ascuns'>";
if (!$thumb) {
	echo "<span style='font-size:13px;' class='spacer vizibil'>Incarca o pictograma pentru acest articol:</span>";
	echo "<input type='file' name='article_pic' value='' /><br />";
	echo "Dimensiune maxima fisier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>";} else {
	if ($thumbError) {
		$delThumb = dbquery("UPDATE ".DB_ARTICLES." SET article_thumb='' WHERE article_id='$edit'");
		if (file_exists(IMAGES."articles/thumbs/".$thumb)) {
			@unlink(IMAGES."articles/thumbs/".$thumb);
		}
		if (file_exists(IMAGES."articles/thumbs/s_".$thumb)) {
			@unlink(IMAGES."articles/thumbs/s_".$thumb);
		}
		echo "<div class='noteyellow'>Pictograma veche a fost stearsa, deoarece lipsea cel putin un fisier imagine.</div>";
		echo "<span style='font-size:13px;' class='spacer vizibil'>Incarca alta pictograma pentru acest articol:</span>";
		echo "<input type='file' name='article_pic' value='' /><br />";
		echo "Dimensiune maxima fisier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>";
	} else {
		echo "<div id='thumbimg'><img src='http://img.weskate.ro/articles/thumbs/".$thumb."' alt='pictograma' /><br /><a href='javascript:void(0);' onclick='deleteThumb(".$edit.",\"a\",\"".$_SESSION['user_key']."\");' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/uncheck.gif); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF'>Sterge pictograma</a></div>";
	}
}
echo "</div>";

echo "<div id='menuitem2' class='ascuns'>";

echo "<div class='flright' style='padding:5px;'>";
echo "<a href='javascript:void(0);' onclick='changePage(1);' id='images_B1' title='Adauga imagini din albumul foto atasat la articol'>album foto atasat</a> / "; //page 1
echo "<a href='javascript:void(0);' onclick='changePage(2);' id='images_B2' title='Adauga imagini din galeria foto'>toate albumele foto</a>"; //page 2
echo "</div>";
echo "<span style='font-weight:bold;font-size:16px;display:block;' class='spacer'>Imagini</span>";

echo "<div class='imagesDiv'>
<div id='images_1' class='ascuns'></div>
<div id='images_2' class='ascuns'></div>
<div id='images_3' class='ascuns'></div>
</div>";
echo "<script type='text/javascript'>";
if ($photoalbum) {
	echo "changePage(1);";
} else {
	echo "changePage(2);";
}
echo "</script>";

echo "</div>";

echo "<div id='menuitem3' class='ascuns'>";

if ($edit) {
	if (strpos($sources,"\n") === false) {
		if (strpos($sources,"->") !== false) {
			list($txt,$url) = explode("->",$sources);
		} else {
			$txt = $sources;
			$url = "";
		}
	} else {
		$sourceArray = explode("\n",$sources);
		$renderedSources = array();
		$cs = 0;
		foreach ($sourceArray as $nr => $sursa) {
			if (strpos($sursa,"->") === false) {
				$txt = $sursa;
				$url = "";
			} else {
				list($txt,$url) = explode("->",$sursa);
			}
			$renderedSources[$cs][0] = $txt;
			$renderedSources[$cs][1] = $url;
			$cs ++;
		}

	}
}

echo "<div id='sources'>";

echo "<div id='sourceCon1'>";
echo "<span style='font-weight:bold;font-size:14px;' id='indice1'>1</span>";
echo "Nume sau titlu : ";
echo "<input type='text' name='sursatxt[]' id='sursaTxt1'".($edit ? " value='".(isset($renderedSources) ? $renderedSources[0][0] : $txt)."'" : "")."/>";
echo "&nbsp;&nbsp;Link : ";
echo "<input type='text' name='sursaurl[]' id='sursaUrl1'".($edit ? " value='".(isset($renderedSources) ? $renderedSources[0][1] : $url)."'" : "")."/>";
echo "</div>";

echo "</div>"; //close sources
if (isset($renderedSources)) {
	echo "<script type='text/javascript'>";
	echo "newSource(".($cs-2).");";
	foreach ($renderedSources as $r => $s) {
		if ($r > 0) {
			echo "fillSource(".($r+1).",\"".$s[0]."\",\"".$s[1]."\");\n";
		}
	}
	echo "</script>";
}

echo "<span style='font-size:13px;color:#090;'> <strong>+</strong> <a href='javascript:newSource(1);'>Mai multe surse</a></span>";
echo "</div>";

echo "</div>"; //close container
echo "</td></tr>";

if ($breaks == 'y') $article = nl2br($article);
$article = stripslashes($article);
echo "<tr><td colspan='4' style='padding:0px;'>
<textarea name='article' cols='48' rows='10' style='width:450px;' id='myContent'>".$article."</textarea></td></tr>";
echo "</table>";
echo "</form>";
} //end the if for the delete article option's ELSE statement

require_once SCRIPTS."footer.php";
?>
