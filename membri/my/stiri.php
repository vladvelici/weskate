<?php
require_once "../../mainfile.php";
$CuloarePagina = "oranj";
$UseAJAX = true;
require_once SCRIPTS."header.php";

if (!iMEMBER) { redirect("../conectare.php?redirto=".urlencode(PAGE_REQUEST)); die();}

add_to_head("<link rel='stylesheet' href='my.css' type='text/css' media='screen' />");
add_to_head("<script language='javascript' type='text/javascript' src='myeditor.js'></script>");

if (isset($_GET['delete'])) {
	if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("N",$_GET['edit'])) {
		$edit = $_GET['edit'];
		$result = dbquery("SELECT news_subject,news_datestamp,news_thumb FROM ".DB_NEWS." WHERE news_id='$edit'");
		$data = dbarray($result);
		$subject = $data['news_subject'];
		$check = md5($data['news_subject'].$data['news_datestamp']);
		if ($check != $_GET['delete']) {
			redirect(PAGE_SELF."?edit=$edit&msg=del_deny");
		} else {
			$thumb = $data['news_thumb'];
			$result = dbquery("DELETE FROM ".DB_NEWS." WHERE news_id='$edit'");
			if (!$result) redirect(PAGE_SELF."?msg=del_err");
			if (file_exists(IMAGES."news/thumbs/".$thumb)) {
				@unlink(IMAGES."news/thumbs/".$thumb);
			}
			if (file_exists(IMAGES."news/thumbs/s_".$thumb)) {
				@unlink(IMAGES."news/thumbs/s_".$thumb);
			}
			cleanup("N",$edit);
			redirect(PAGE_SELF."?msg=del_ok");
		}
	} else {
		redirect(PAGE_SELF."?msg=del_err");
	}
} else {

add_to_head("<script language='javascript' type='text/javascript' src='myimages.js'></script>
<script language='javascript' type='text/javascript' src='".SCRIPTS."js/tiny_mce/tiny_mce.js'></script>
<script type='text/javascript'>

	tinyMCE.init({
		mode:'specific_textareas',
		editor_selector:'tinymce',
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
		if (checkMyAccess("N",$_GET['edit'])) {
			$edit = $_GET['edit'];
		} else {
			$errors[1] = 1;
			$draft = 1;
		}
	}

	if (isset($_POST['cat']) && isnum($_POST['cat']) && dbcount("(news_cat_id)",DB_NEWS_CATS,"news_cat_id='".$_POST['cat']."'")) {
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

	if (isset($_POST['city']) && isnum($_POST['city']) && dbcount("(city_id)",DB_CITIES,"city_id='".$_POST['city']."' AND city_type=0")) {
		$city = $_POST['city'];
	} else {
		$city = 0;
	}

	if (isset($_POST['subject']) && trim(htmlsafe($_POST['subject'])) != "") {
		$subject = trim(htmlsafe($_POST['subject']));
	} else {
		$subject = "Ciorna ".showdate("shortdate",time());
		$errors[3] = 3;
		$draft = 1;
	}

	$news_start_date = 0; $news_end_date = 0;
	$Day = (isnum($_POST['news_start']['mday']) ? $_POST['news_start']['mday'] : 0);
	$Mon = (isnum($_POST['news_start']['mon']) ? $_POST['news_start']['mon'] : 0);
	$Year = (isnum($_POST['news_start']['year']) ? $_POST['news_start']['year'] : 0);
	if ($Day && $Mon && $Year) {
		$news_start_date = mktime(0,0,0,$Mon,$Day,$Year);
	}
	$Day = (isnum($_POST['news_end']['mday']) ? $_POST['news_end']['mday'] : 0);
	$Mon = (isnum($_POST['news_end']['mon']) ? $_POST['news_end']['mon'] : 0);
	$Year = (isnum($_POST['news_end']['year']) ? $_POST['news_end']['year'] : 0);

	if ($Day && $Mon && $Year) {
		$news_end_date = mktime(0,0,0,$Mon,$Day,$Year);
	}

	if (isset($_POST['news']) && strlen($_POST['news']) > 0) {
		$stire = sqlsafe(fixRoChars($_POST['news']));
	} else {
		$stire = "";
		$draft = 1;
		$errors[4] = 4;
	}


	$breaks = "n";
	$comments = (isset($_POST['comments']) ? 1 : 0);
	$ratings = (isset($_POST['ratings']) ? 1 : 0);

	$snippet = (isset($_POST['news_frag']) && strlen($_POST['news_frag']) > 0 ? trim(htmlsafe(nl2br($_POST['news_frag']))) : "");

	if (strlen($snippet) <= 0) {
		$size = strpos("\n",$stire)<255 ? 255 : strpos("\n",$stire);
		$snippet = trimlink(nl2br(strip_tags($stire)),$size);
	}

	$keywords = (isset($_POST['keywords']) && strlen($_POST['keywords']) > 5 ? keywordize(trim(htmlsafe($_POST['keywords']))) : keywordize($subject));
	$descriere = (isset($_POST['descriere']) && strlen($_POST['descriere']) > 7 ? trim(fixRoChars(htmlsafe($_POST['descriere']))) : trimlink($snippet,70));

	if (isset($_FILES['news_pic']) && is_uploaded_file($_FILES['news_pic']['tmp_name'])) {
		$pic_types = array(".gif",".jpg",".jpeg",".png");
		$news_pic = $_FILES['news_pic'];
		$news_ext = strtolower(strrchr($news_pic['name'],"."));
		if (!preg_match("/^[-0-9A-Z_\.\[\]\s]+$/i", $news_pic['name'])) {
			$errors[5] = 5;
		} elseif ($news_pic['size'] > $setari['photo_max_b']) {
			$errors[6] = 6;
		} elseif (!in_array($news_ext, $pic_types)) {
			$errors[7] = 7;
		} else {
			require_once SCRIPTS."photo_functions_include.php";
			@unlink(IMAGES."news/thumbs/temp".$news_ext);
			move_uploaded_file($news_pic['tmp_name'], IMAGES."news/thumbs/temp".$news_ext);
			chmod(IMAGES."news/thumbs/temp".$news_ext, 0644);
			$imagefile = @getimagesize(IMAGES."news/thumbs/temp".$news_ext);
			$news_thumb = image_exists(IMAGES."news/thumbs/", md5($news_pic['name']).$news_ext);
			createFixedThumb($imagefile[2], IMAGES."news/thumbs/temp".$news_ext, IMAGES."news/thumbs/".$news_thumb, 550, 300);
			createFixedThumb($imagefile[2], IMAGES."news/thumbs/temp".$news_ext, IMAGES."news/thumbs/s_".$news_thumb, 100, 100);
			@unlink(IMAGES."news/thumbs/temp".$news_ext);
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
		$result = dbquery("UPDATE ".DB_NEWS." SET news_subject = '$subject', news_extended='$stire', news_draft='$draft', news_news='$snippet', news_keywords='$keywords', news_descriere = '$descriere', news_photoalbum='$photoalbum', news_city='$city', news_sources='$sources', news_allow_comments='$comments', news_start='$news_start_date', news_end='$news_end_date', news_allow_ratings='$ratings'".(isset($news_thumb) ? ", news_thumb='$news_thumb' " : "").(isset($cat) ? ", news_cat='$cat' " : "")." WHERE news_id='$edit'");
		if (!$result) {
			$errors[8] = 8;
		} else {
			if (!$draft) {
				indexItem($edit,"N",killRoChars($subject),strip_tags(killRoChars($stire)),$keywords,0,0,0,"/stiri/".urltext($subject).".$edit");
			} else {
				deleteIndex("N",$edit);
			}
		}
	} else {
		$timestamp = time();
		$result = dbquery("INSERT INTO ".DB_NEWS."
				(news_cat, news_news, news_subject, news_draft, news_extended, news_descriere, news_keywords, news_breaks, news_name, news_datestamp, news_allow_comments, news_allow_ratings, news_photoalbum, news_city, news_thumb, news_sources, news_start, news_end)
				VALUES
				('".(isset($cat) ? $cat : 0)."', '$snippet', '$subject', '$draft', '$stire', '$descriere', '$keywords', '$breaks', '".$userdata['user_id']."', '$timestamp', '$comments', '$ratings', '$photoalbum', '$city', '".(isset($news_thumb) ? $news_thumb : "")."', '$sources', '$news_start_date', '$news_end_date')");
		if (!$result) {
			$errors[8] = 8;
		} else {
			$result2 = dbquery("SELECT news_id FROM ".DB_NEWS." WHERE news_datestamp = '$timestamp' AND news_name = '".$userdata['user_id']."'");
			$data2 = dbarray($result2);
			$_GET['edit'] = $data2['news_id'];
			if (!$draft) {
				indexItem($_GET['edit'],"N",killRoChars($subject),strip_tags(killRoChars($stire)),$keywords,0,0,$timestamp,"/stiri/".urltext($subject).".".$_GET['edit']);
			}
		}
	}

}



if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("N",$_GET['edit'])) {
	$edit = $_GET['edit'];
	$result = dbquery("SELECT * FROM ".DB_NEWS." WHERE news_id='".$edit."'");
	$data = dbarray($result);
	$cat = $data['news_cat'];
	$photoalbum = $data['news_photoalbum'];
	$city = $data['news_city'];
	$subject = $data['news_subject'];
	$snippet = $data['news_news'];
	$news = $data['news_extended'];
	$draft = $data['news_draft'];
	$breaks = $data['news_breaks'];
	$comments = $data['news_allow_comments'];
	$ratings = $data['news_allow_ratings'];
	$keywords = $data['news_keywords'];
	$descriere = $data['news_descriere'];
	$thumb = $data['news_thumb'];
	$sources = $data['news_sources'];
	$datestamp = $data['news_datestamp'];
	if ($data['news_start'] > 0) $news_start = getdate($data['news_start']);
	if ($data['news_end'] > 0) $news_end = getdate($data['news_end']);

} else {
	$edit = false;
	$cat = 0;
	$photoalbum = 0;
	$city = 0;
	$subject = "";
	$snippet = "";
	$news = "";
	$draft = 1;
	$breaks = "n";
	$comments = 1;
	$ratings = 1;
	$keywords = "";
	$descriere = "";
	$thumb = "";
	$sources = "";
}

$ciorne = dbcount("(news_id)",DB_NEWS,"news_name='".$userdata['user_id']."' AND news_draft='1'".($edit ? " AND news_id!='$edit'" : ""));
$stiree = dbcount("(news_id)",DB_NEWS,"news_name='".$userdata['user_id']."' AND news_draft='0'".($edit ? " AND news_id!='$edit'" : ""));

echo "<table cellpadding='4' cellspacing='0' width='100%'><tr>";
if ($ciorne || $stiree) echo "<td id='myContainerB'><a href='javascript:toggleMyContainer();' id='myContainerBa'>&darr;</a></td>";
echo "<td style='border-bottom:2px solid #999;'><span class='capmain_color' style='font-size:30px;padding-bottom:7px;'>".($subject ? $subject : "Stire noua")."</span></td>
<td align='right' class='my-navigationtd' style='border-bottom:2px solid #999;'>
<a href='/membri/my/' class='my-albastru'>tot</a> <strong>-</strong> 
<span class='my-oranj'>&#351;tiri</span> <strong>-</strong> 
<a href='articole.php' class='my-galben'>articole</a> <strong>-</strong> 
<a href='spoturi.php' class='my-mov'>locuri de skate</a> <strong>-</strong> 
<a href='video.php' class='my-rosu'>video</a></td>
</tr></table>";


if ($ciorne || $stiree) {
	echo "<div  id='myContainer'>";
	echo "<table cellpadding='3' cellspacing='3' style='color:#000;background-color:#fff;'><tr valign='top'>";
	$limit = 33 - ($ciorne % 6);
	$getArts = dbquery("SELECT news_id,news_subject,news_draft FROM ".DB_NEWS." WHERE news_name='".$userdata['user_id']."' ORDER BY news_draft DESC, news_datestamp DESC LIMIT 0,$limit");
	$t = 0; //0=default. 1=primul articol. 2=urmatoarele articole
	$c = 0;
	echo "<td style='width:200px;'>";
	while ($data = dbarray($getArts)) {
		if ($data['news_draft'] == 0 && $t == 0) {
			$t = 1;
		}
		if ($c == 0) {
			if ($t == 0) {
				echo "<strong>CIORNELE MELE</strong>";
			} else {
				echo "<strong>STIRILE MELE</strong>";
				$t=2;
			}
			$c++;
		} else if (($c%6 == 0 || $t==1) && $c != 0) {
			echo "</td><td style='width:200px;'>";
			$c = 6;
			if ($t==1) { echo "<strong>STIRILE MELE</strong>"; $c++; $t = 2; }
		}
		echo "<a href='".PAGE_SELF."?edit=".$data['news_id']."' class='stiri my-navlink ciornalink' style='font-size:12px;padding:2px;' title='".$data['news_subject']."'>".trimlink($data['news_subject'],26)."</a>";
		$c++;
	}
	echo "</td>";
	echo "</tr></table>";
	echo "</div>";
}

//NOTIFICARI, ERORI
if (isset($_GET['msg']) && $_GET['msg'] == "del_err") {
	echo "<div class='notered'>A fost intampinata o eroare la stergerea stirii. Incearca din nou.</div>";
} else if (isset($_GET['msg']) && $_GET['msg'] == "del_deny") {
	echo "<div class='notered'>Eroare: Nu ai drepturile necesare pentru a sterge stirea sau incerci sa stergi o stire inexistenta.</div>";
} else if (isset($_GET['msg']) && $_GET['msg'] == "del_ok") {
	echo "<div class='notegreen'>Stirea a fost stersa cu succes.</div>";
}
if (isset($errors) && is_array($errors)) {
	$eroareMsg = "";
	$forcedDraft = false;
	if (in_array(1,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#001): Accesul la stirea dorita a fost respins. O stire noua a fost creaat.</span>";
		$forcedDraft = true;
	}
	if (in_array(2,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#002): Categoria in care doresti sa salvezi stirea este invalida. Alege alta.</span>";
		$forcedDraft = true;
	}
	if (in_array(3,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#003): Te rugam sa completezi subiectul stirii.</span>";
		$forcedDraft = true;
	}
	if (in_array(4,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#004): Incerci sa salvezi o stire goala.</span>";
		$forcedDraft = true;
	}
	if (in_array(5,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#005): PICTOGRAMA : Numele fisierului imagine este invalid. Stirea a fost salvata fara pictograma.</span>";
	}
	if (in_array(6,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#006): PICTOGRAMA : Dimensiunea fisierului imagine trebuie sa fie de maxim ".parsebytesize($setari['photo_max_b']).". Stirea a fost salvata fara pictograma.</span>";
	}
	if (in_array(7,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#880;'>Eroare (#007): PICTOGRAMA : Tipul fisierului incarcat este invalid. Sunt permise doar imagini <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>.</span>";
	}
	if (in_array(8,$errors)) {
		$eroareMsg .= "<span style='display:block;padding:2px;font-size:14px;color:#c00;'>Eroare (#008): Eroare la salvarea stirii in baza de date.</span>";
	}
	if ($eroareMsg) {
		echo "<span style='display:block;padding:2px;font-size:14px;font-weight:bold;color:#0c0;'>Stirea a fost salvata, dar au aparut urmatoarele probleme:</span>";
	}
	if ($forcedDraft) {
		echo "<span style='display:block;padding:2px;font-size:13px;color:#990;'>Stirea a fost salvata fortat ca ciorna din cauza erorilor #001, #002, #003 sau #004.</span>";
	}
	if ($eroareMsg) {
		echo $eroareMsg;
	} else {
		echo "<div class='notegreen'>Stirea a fost salvata cu succes.</div>";
	}
}
//END NOTIFICARI, ERORI


echo "<form name='inputform' method='post' action='".PAGE_SELF.($edit ? "?edit=".$edit : "")."' onsubmit='return ValidateForm();'  enctype='multipart/form-data'>\n";


echo "<table cellpadding='4' cellspacing='0' width='100%' style='margin-top:5px;'>";
echo "<tr class='tbl1'><td style='border-top:1px solid #CCCCCC;border-left:1px solid #CCCCCC;'>";
echo "<div style='display:block;' class='spacer'><strong>Subiect</strong><br />
<input name='subject' type='text' value='$subject' style='width:200px;' class='textbox' /></div>";

$result = dbquery("SELECT news_cat_name,news_cat_id FROM ".DB_NEWS_CATS." ORDER BY news_cat_name");
$cat_opts = ""; $sel = "";
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		if (isset($cat)) $sel = ($cat == $data['news_cat_id'] ? " selected='selected'" : "");
		$cat_opts .= "<option value='".$data['news_cat_id']."'$sel>".$data['news_cat_name']."</option>\n";
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
echo "<div class='flright'>".($edit ? "<a href='".PAGE_SELF."' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/new.png); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF' onclick='return sureNew();'>Stire noua</a><a href='".PAGE_SELF."?edit=$edit&amp;delete=".md5($subject.$datestamp)."' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/uncheck.gif); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF' onclick='return deleteItem();'>Sterge stirea</a>" : "")."<input type='submit' value='Salveaza' name='save' /></div>";
echo "<span id='savebarText'>Nu te descurci? Viziteaza centrul de ajutor WeSkate.</span><div id='saveTimeC' style='display:none;'>(de <div id='notSavedTime' style='display:inline;'></div> minute)</div>";
echo "</td></tr>";

echo "<tr><td class='ths tbl2' colspan='4' style='padding:0px;'>";

$thumbError = ($thumb && (!file_exists(IMAGES."news/thumbs/".$thumb) || !file_exists(IMAGES."news/thumbs/s_".$thumb)) ? true : false);

echo "<div class='toggleTabBar'>";
echo "<a href='javascript:void(0);' onclick='toggleTab(1);' id='toggleTabButton1' class='toggleTabButton' title='Alege o pictograma pentru aceasta stire'".($thumbError ? " style='background-image:url(http://img.weskate.ro/info.png);background-position:center left;background-repeat:no-repeat;padding-left:17px;'" : "").">Pictograma</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(2);' id='toggleTabButton2' class='toggleTabButton' title='Adauga o imagine stirii'>Imagini</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(3);' id='toggleTabButton3' class='toggleTabButton' title='Spune-ne de unde te-ai inspirat'>Surse...</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(4);' id='toggleTabButton4' class='toggleTabButton' title='Alege orasul si judetul acestei stiri'>Localizare</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(5);' id='toggleTabButton5' class='toggleTabButton' title='Scire un fragment introductiv pentru aceasta stire'>Fragment</a>";
echo "<a href='javascript:void(0);' onclick='toggleTab(6);' id='toggleTabButton6' class='toggleTabButton' title='Optiuni avansate stire'>Avansat</a>";
echo "</div>";

echo "<div class='toggleTabContainer'>";
echo "<div id='menuitem1' class='ascuns'>";
if (!$thumb) {
	echo "<span style='font-size:13px;' class='spacer vizibil'>Incarca o pictograma pentru acesta stire:</span>";
	echo "<input type='file' name='news_pic' value='' /><br />";
	echo "Dimensiune maxima fisier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>";} else {
	if ($thumbError) {
		$delThumb = dbquery("UPDATE ".DB_NEWS." SET news_thumb='' WHERE news_id='$edit'");
		if (file_exists(IMAGES."news/thumbs/".$thumb)) {
			@unlink(IMAGES."news/thumbs/".$thumb);
		}
		if (file_exists(IMAGES."news/thumbs/s_".$thumb)) {
			@unlink(IMAGES."news/thumbs/s_".$thumb);
		}
		echo "<div class='noteyellow'>Pictograma veche a fost stearsa, deoarece lipsea cel putin un fisier imagine.</div>";
		echo "<span style='font-size:13px;' class='spacer vizibil'>Incarca alta pictograma pentru acesta stire:</span>";
		echo "<input type='file' name='news_pic' value='' /><br />";
		echo "Dimensiune maxima fisier : <strong>".parsebytesize($setari['photo_max_b'])."</strong>.<br/>Tipuri permise : <strong>JPEG</strong>, <strong>JPG</strong>, <strong>GIF</strong> si <strong>PNG</strong>";
	} else {
		echo "<div id='thumbimg'><img src='http://img.weskate.ro/news/thumbs/".$thumb."' alt='pictograma' /><br /><a href='javascript:void(0);' onclick='deleteThumb(".$edit.",\"n\");' style='display:inline-block; padding:5px 5px 5px 17px; background-image:url(http://img.weskate.ro/uncheck.gif); background-position:center left; background-repeat:no-repeat;margin-right:5px;' class='lightonhoverF'>Sterge pictograma</a></div>";
	}
}
echo "</div>";

echo "<div id='menuitem2' class='ascuns'>";

echo "<div class='flright' style='padding:5px;'>";
echo "<a href='javascript:void(0);' onclick='changePage(1);' id='images_B1' title='Adauga imagini din albumul foto atasat la stire'>album foto atasat</a> / "; //page 1
echo "<a href='javascript:void(0);' onclick='changePage(2);' id='images_B2' title='Adauga imagini din galeria ta foto'>pozele mele</a>"; //page 2
echo "<a href='javascript:void(0);' onclick='changePage(3);' id='images_B3'  style='display:none;' title='Cauta imagini pe site si adauga-le in stire'>cautare</a>"; //page 3
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

if (isset($_GET['oras']) && isnum($_GET['oras']) && !$edit) $city = $_GET['oras'];

echo "<div id='menuitem4' class='ascuns'>";

echo "<span style='font-size:14px;font-weight:bold;padding:5px;' class='vizibil'>Alege orasul:</span>";
$result = dbquery("SELECT city_id,city_name FROM ".DB_CITIES." WHERE city_type=0 ORDER BY city_name");
$news_city_opts = ""; $sel = "";
if (dbrows($result)) {
	while ($data = dbarray($result)) {
		if (isset($city)) $sel = ($data['city_id'] == $city ? " selected='selected'" : "");
		$news_city_opts .= "<option value='".$data['city_id']."'$sel>".$data['city_name']."</option>\n";
	}
}	

echo "<select name='city'><option>-- stirea nu e locala --</option>$news_city_opts</select><br /><br />";
echo "Ignora acest tab daca sitrea pe care o scrii nu este locala.";
echo "</div>";

echo "<div id='menuitem5' class='ascuns'>";
echo "<span style='font-size:14px;font-weight:bold;padding:5px;' class='vizibil'>Scire un scurt fragment introductiv pentru aceasta stire:</span>";
echo "<textarea name='news_frag' cols='48' rows='6' style='width:99%;'>$snippet</textarea>";
echo "</div>";

echo "<div id='menuitem6' class='ascuns'>";
echo "<span style='font-size:14px;font-weight:bold;padding:5px;' class='vizibil'>Afiseaza stirea pe site doar in intervalul de timp urmator :</span>";

function luna ($nr) {
	switch ($nr) {
		case 1:
		return "Ianuarie"; break;
		case 2:
		return "Februarie"; break;
		case 3:
		return "Martie"; break;
		case 4:
		return "Aprilie"; break;
		case 5:
		return "Mai"; break;
		case 6:
		return "Iunie"; break;
		case 7:
		return "Iulie"; break;
		case 8:
		return "August"; break;
		case 9:
		return "Septembrie"; break;
		case 10:
		return "Octombrie"; break;
		case 11:
		return "Noiembrie"; break;
		case 12:
		return "Decembrie"; break;
	}
}

echo "<strong>Din</strong> <select name='news_start[mday]' class='textbox'>\n<option value='0'>-zi-</option>\n";
for ($i=1;$i<=31;$i++) echo "<option".(isset($news_start['mday']) && $news_start['mday'] == $i ? " selected='selected'" : "").">$i</option>\n";
echo "</select> <select name='news_start[mon]' class='textbox'>\n<option value='0'>-- luna --</option>\n";
for ($i=1;$i<=12;$i++) echo "<option value='$i'".(isset($news_start['mon']) && $news_start['mon'] == $i ? " selected='selected'" : "").">".luna($i)."</option>\n";
echo "</select> <select name='news_start[year]' class='textbox'>\n<option value='0'>-an-</option>\n";
$an = date("Y");
if ($edit && isset($news_start['year']) && $news_start['year'] < $an)  {
	for ($i=$news_start['year'];$i<=($an+1);$i++) echo "<option".($news_start['year'] == $i ? " selected='selected'" : "").">$i</option>\n";
} else {
	echo "<option".(isset($news_start['year']) && $news_start['year'] == $an ? " selected='selected'" : "").">$an</option>\n";
	echo "<option".(isset($news_start['year']) && $news_start['year'] == $an+1 ? " selected='selected'" : "").">".($an+1)."</option>\n";
}
echo "</select> <strong> pana in </strong> ";
echo "<select name='news_end[mday]' class='textbox'>\n<option value='0'>-zi-</option>\n";
for ($i=1;$i<=31;$i++) echo "<option".(isset($news_end['mday']) && $news_end['mday'] == $i ? " selected='selected'" : "").">$i</option>\n";
echo "</select> <select name='news_end[mon]' class='textbox'>\n<option value='0'>-- luna --</option>\n";
for ($i=1;$i<=12;$i++) echo "<option value='$i'".(isset($news_end['mon']) && $news_end['mon'] == $i ? " selected='selected'" : "").">".luna($i)."</option>\n";
echo "</select> <select name='news_end[year]' class='textbox'>\n<option value='0'>-an-</option>\n";
if ($edit && isset($news_end['year']) && $news_end['year'] < $an)  {
	for ($i=$news_end['year'];$i<=($an+1);$i++) echo "<option".($news_end['year'] == $i ? " selected='selected'" : "").">$i</option>\n";
} else {
	echo "<option".(isset($news_end['year']) && $news_end['year'] == $an ? " selected='selected'" : "").">$an</option>\n";
	echo "<option".(isset($news_end['year']) && $news_end['year'] == $an+1 ? " selected='selected'" : "").">".($an+1)."</option>\n";
}
echo "</select>";

echo "</div>";

echo "</div>"; //close container
echo "</td></tr>";

if ($breaks == 'y') $news = nl2br($news);
$news = stripslashes($news);
echo "<tr><td colspan='4' style='padding:0px;'>
<textarea name='news' cols='48' rows='10' style='width:450px;' id='myContent' class='tinymce'>".$news."</textarea></td></tr>";
echo "</table>";
echo "</form>";
if (isset($_GET['oras']) && isnum($_GET['oras']) && !$edit) echo "<script type='text/javascript'>toggleTab(4);</script>";
} //end the if for the delete news option's ELSE statement

require_once SCRIPTS."footer.php";
?>
