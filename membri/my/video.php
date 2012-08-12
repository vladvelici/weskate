<?php
require_once "../../mainfile.php";
$CuloarePagina = "rosu";
require_once SCRIPTS."header.php";
if (!iMEMBER) { redirect("/membri/conectare.php?redirto=".urlencode(PAGE_REQUEST)); die("Acces respins."); }
if (!isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) { redirect("/index.php"); }
add_to_head("<link rel='stylesheet' href='http://weskate.ro/membri/my/my.css' type='text/css' media='screen' />");
$key = "?key=".$_SESSION['user_key'];
require_once BASEDIR."video/functions.php";

if (isset($_GET['edit']) && isnum($_GET['edit']) && checkMyAccess("V",$_GET['edit'])) {
	$edit = $_GET['edit'];
	if (isset($_GET['delete'])) {
		$result = dbquery("SELECT video_id,video_url FROM ".DB_VIDEOS."
				WHERE video_id='$edit'");
		$data = dbarray($result);
		$check = md5($data['video_id'].$data['video_url']);
		if ($check!=$_GET['delete']) {
			redirect(PAGE_SELF."?key=".$_SESSION['user_key']."&edit=$edit&msg=1");
		} else {
			//delete from db
			$result=dbquery("DELETE FROM ".DB_VIDEOS." WHERE video_id='$edit'");
			//delete "v" (video,id) comments and ratings (and search indexes)
			cleanup("V",$edit);
			//redirect to "add video" page and show the "deleted successfully" message:
			redirect(PAGE_SELF."?msg=2&key=".$_SESSION['user_key']);
		}
	}
} else {
	$edit = 0;
}

$videos = dbcount("(video_id)",DB_VIDEOS,"video_owner='".$userdata['user_id']."'");

echo "<table cellpadding='4' cellspacing='0' width='100%'><tr>";
if ($videos) echo "<td id='myContainerB'><a href='javascript:toggleMyContainer();' id='myContainerBa'>&darr;</a></td>";
echo "<td style='border-bottom:2px solid #999;'><span class='capmain_color' style='font-size:30px;padding-bottom:7px;'>".($edit ? "Editeaza video" : "Adauga un video")."</span></td>";
echo "<td align='right' class='my-navigationtd' style='border-bottom:2px solid #999;'>";
echo "<a href='/membri/my' class='my-albastru'>tot</a> <strong>-</strong> ";
echo "<a href='stiri.php$key' class='my-oranj'>&#351;tiri</a> <strong>-</strong> ";
echo "<a href='articole.php$key' class='my-galben'>articole</a> <strong>-</strong> ";
echo "<a href='spoturi.php$key' class='my-mov'>locuri de skate</a> <strong>-</strong> ";
echo "<span class='my-rosu'>video</span></td>";
echo "</tr></table>";

//myContainer panel
if ($videos) {
	add_to_head("<script type='text/javascript' src='video.js'></script>");
	echo "<div id='myContainer'>";
	echo "<div class='flright' style='font-size:13px;display:inline;padding:5px;'>Ai adaugat <strong>$videos</strong> video".($videos > 1 && $videos != 0 ?  "-uri" : "")." pana acum.</div>";
	echo "<a href='".PAGE_SELF."$key' style='font-size:13px;display:inline-block;padding:5px 5px 5px 24px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3% 50%;' class='video my-navlink'>Adauga video nou</a>";
	if (dbrows($result) < $videos) {
		echo "<a href='javascript:showAllVideos();' style='font-size:13px;display:inline-block;padding:5px 5px 5px 24px;background-image:url(http://img.weskate.ro/video.png);background-repeat:no-repeat;background-position:3% 50%;margin-left:3px;' class='video my-navlink'>Arata toate videourile</a>";

	}
	echo "<div class='vizibil' id='mySpots'>";

	$result = dbquery("SELECT video_id,video_thumb,video_title,video_datestamp FROM ".DB_VIDEOS."
			WHERE video_owner='".$userdata['user_id']."'
			ORDER BY video_datestamp DESC
			LIMIT 0,9");

	while ($data=dbarray($result)) {
		echo "<div><a href='".PAGE_SELF."$key&amp;edit=".$data['video_id']."' class='video' title='Editeaza: ".$data['video_title']."'".($edit == $data['video_id'] ? " style='border-color:#555;'" : "")."><strong>".trimlink($data['video_title'],30)."</strong>".($data['video_thumb'] ? "<img src='".$data['video_thumb']."' alt='".$data['video_title']."' /><br />" : "<span>fara pictograma</span><br />")."<small>adaugat ".showdate("shortdate",$data['video_datestamp'])."</small></a></div>";
	}

	echo "</div>";
	echo "</div>";
}

echo "<div class='spacer'></div>";

if (isset($_POST['savevideo'])) {
	$error=array(1 => "", 2 => "", 3 => "", 4 => "", 5 => "");

	$vid = "";
	if (isset($_POST['url'])) {
		$vid = get_video_info($_POST['url']);
		if (!$vid) {
			$error[1] = "Linkul nu e suportat. <a href='#'>Mai multe &rsaquo;</a>";
		} else if (dbcount("(video_id)",DB_VIDEOS,"video_embed='".$vid[0]."'") && !$edit) {
			$error[1] = "Acest video este adaugat deja.";

		}
	} else {
		$error[1] = "URL-ul e obligatoriu!";
	}

	$title = "";
	if (isset($_POST['title'])) {
		$title = trim(htmlsafe($_POST['title']));
		if (strlen($title) < 3) {
			$error[2] = "Scrieti minim 3 caractere.";
		}
	} else {
		$error[2] = "Completati titlul!";
	}

	$desc = "";
	if (isset($_POST['meta_desc'])) {
		$desc = trim(htmlsafe($_POST['meta_desc']));
		if (strlen($desc) < 5) {
			$error[3] = "Scrieti minim 3 caractere.";
		}
	} else {
		$error[3] = "Scrie o descriere pentru acest video.";
	}

		
	if (isset($_POST['meta_keywords'])) {
		$keywords = keywordize(trim(htmlsafe($_POST['meta_keywords'])));
		if (strlen($keywords) < 5) {
			$keywords = keywordize($title);
			$error[5] = "Prea scurte. Au fost generate altele automat.";
		}
	} else {
		$keywords = keywordize($title);
		$error[5] = "Generate automat.";
	}

	if (isset($_POST['cat']) && isnum($_POST['cat']) && dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_id='".$_POST['cat']."'")) {
		$cat = $_POST['cat'];
	} else {
		$error[4] = "Categoria selectata este invalida.";
	}

	if (isset($_POST['ratings']) && $_POST['ratings']) {
		$ratings = 1;
	} else {
		$ratings = 0;
	}

	if (isset($_POST['comments']) && $_POST['comments']) {
		$comments = 1;
	} else {
		$comments = 0;
	}

	if (!$error[1] && !$error[2] && !$error[3] && !$error[4]) {
		$embed = $vid[0];
		$thumb = $vid[1];
		$url = trim(htmlsafe($_POST['url']));
		if ($edit) {
			$result = dbquery("UPDATE ".DB_VIDEOS." SET video_title='$title', video_cat='$cat', video_url='$url', video_thumb='$thumb', video_embed='$embed', video_meta_description='$desc', video_meta_keywords='$keywords', video_allow_comments='$comments', video_allow_ratings='$ratings' WHERE video_id='$edit'");
			if ($result) {
				indexItem($edit,"V",killRoChars($title),killRoChars($desc),$keywords,0,0,0,"/video/".urltext($title).".$edit");
				echo "<div class='notegreen'>Video actualizat cu succes. Vedeti un preview sau continuati modificarile mai jos:</div>";
			} else {
				echo "<div class='notered'>Eroare la actualizarea video-ului. Incearca din nou.</div>";
			}
		} else {
			$owner = $userdata['user_id'];
			$datestamp = time();
			$result = dbquery("INSERT INTO ".DB_VIDEOS." (video_title, video_cat, video_url, video_thumb, video_embed, video_meta_description, video_meta_keywords, video_allow_comments, video_allow_ratings, video_datestamp, video_owner) VALUES ('$title', '$cat', '$url', '$thumb', '$embed', '$desc', '$keywords', '$comments', '$ratings', '$datestamp', '$owner')");
			if ($result) { 
				$result2 = dbquery("SELECT video_id FROM ".DB_VIDEOS." WHERE video_datestamp='$datestamp' AND video_owner='$owner'");
				$data2 = dbarray($result2);
				$edit = $data2['video_id'];
				indexItem($edit,"V",killRoChars($title),killRoChars($desc),$keywords,0,0,$datestamp,"/video/".urltext($title).".$edit");
				echo "<div class='notegreen'>Video adaugat cu succes. Vedeti un preview sau editati mai jos:</div>";
			} else {
				echo "<div class='notered'>Eroare la salvarea video-ului. Incearca din nou.</div>";
			}
		}
	}
}

if ($edit) {
	$result = dbquery("SELECT * FROM ".DB_VIDEOS." WHERE video_id='$edit'");
	$data = dbarray($result);
	$title = $data['video_title'];
	$url = $data['video_url'];
	$cat = $data['video_cat'];
	$embed = $data['video_embed'];
	$datestamp = $data['video_datestamp'];
	$desc = $data['video_meta_description'];
	$keywords = $data['video_meta_keywords'];
	$comments = $data['video_allow_comments'];
	$ratings = $data['video_allow_ratings'];
} else {
	$title = (isset($_POST['title']) ? trim(htmlsafe($_POST['title'])) : "");
	$url = (isset($_POST['url']) ? trim(htmlsafe($_POST['url'])) : "");
	$cat = (isset($_POST['cat']) && isnum($_POST['cat']) ? $_POST['cat'] : 0);
	$desc = (isset($_POST['desc']) ? trim(htmlsafe($_POST['desc'])) : "");
	$keywords = (isset($_POST['keywords']) ? trim(htmlsafe($_POST['keywords'])) : "");
	$comments = (isset($_POST['comments']) && $_POST['comments'] ? 1 : 0);
	$ratings = (isset($_POST['ratings']) && $_POST['ratings'] ? 1 : 0);
}

if (isset($_GET['msg'])) {
	if ($_GET['msg'] == 1) {
		echo "<div class='notered'>A fost intampinata o eroare la stergerea video-ului.</div>";
	} elseif ($_GET['msg'] == 2) {
		echo "<div class='notegreen'>Video-ul a fost sters cu succes.</div>";
	}
}


echo "<form name='inputform' method='post' action='".PAGE_SELF.$key.($edit ? "&amp;edit=$edit" : "")."'  enctype='multipart/form-data'>\n";
if ($edit) {
	echo "<div align='center' style='text-align:left;margin-left:auto;margin-right:auto;width:598px;background-color:#eee;padding:0px 0px 0px 2px;'>";
	echo "<div class='flright'>";
	echo "<a href='".PAGE_SELF."$key' onclick='return confirm(\"Modificarile vor fi pierdute. Continui?\")' style='font-size:13px;display:inline-block;padding:5px 5px 5px 19px;background-image:url(http://img.weskate.ro/new.png);background-repeat:no-repeat;background-position:3% 50%;' class='lightonhoverF my-navlink'>Nou</a>";
	echo "<a href='".PAGE_SELF."$key&amp;edit=$edit&amp;delete=".md5($edit.$url)."' onclick='return confirm(\"Sigur vrei sa stergi?\")' style='font-size:13px;display:inline-block;padding:5px 5px 5px 19px;background-image:url(http://img.weskate.ro/uncheck.gif);background-repeat:no-repeat;background-position:3% 50%;' class='lightonhoverF my-navlink'>Sterge</a>";
	echo "</div>";
	echo "<span style='font-size:21px' class='capmain_color'>$title</span>";
	echo "</div>";
}
echo "<table cellpadding='4' cellspacing='2' width='600' class='tbl-border spacer' align='center'>";

echo "<tr class='lightonhoverF".(isset($error[2]) && $error[2] ? " notered" : "")."'><td><div style='font-size:15px;font-weight:bold;'>Titlu video</div><small>".(isset($error[2]) && $error[2] ? $error[2] : "Nu repetati numele categoriei!")."</small></td>";
echo "<td><input type='text' value='$title' name='title' class='my-textboxBig' /></td></tr>";

echo "<tr class='lightonhoverF".(isset($error[3]) && $error[3] ? " notered" : "")."'><td><div style='font-size:15px;font-weight:bold;'>Scurta descriere</div><small>".(isset($error[3]) && $error[3] ? $error[3] : "Scurt si la subiect!")."</small></td>";
echo "<td><input type='text' value='$desc' name='meta_desc' class='my-textboxBig' /></td></tr>";

echo "<tr class='lightonhoverF".(isset($error[5]) && $error[5] ? " noteyellow" : "")."'><td><div style='font-size:15px;font-weight:bold;'>Cuvinte cheie</div><small>".(isset($error[5]) && $error[5] ? $error[5] : "Separate prin virgula")."</small></td>";
echo "<td><input type='text' value='$keywords' name='meta_keywords' class='my-textboxBig' /></td></tr>";

$spaces = 0;
function getCats($searchIt) {
	global $spaces,$cat;
	$resultC = dbquery("SELECT video_cat_name,video_cat_id FROM ".DB_VIDEO_CATS." WHERE video_cat_sub='$searchIt'");
	while($dataC=dbarray($resultC)) {
		echo "<option value='".$dataC['video_cat_id']."'".($cat==$dataC['video_cat_id'] ? " selected='selected'" : "").">".str_repeat("&nbsp;",$spaces*2).($spaces ? "-> " : "").$dataC['video_cat_name']."</option>";
		if (dbcount("(video_cat_id)",DB_VIDEO_CATS,"video_cat_sub='".$dataC['video_cat_id']."'")) {
			$spaces++;
			getCats($dataC['video_cat_id']);
			$spaces--;
		}
	}
}

echo "<tr class='lightonhoverF".(isset($error[4]) && $error[4] ? " notered" : "")."'><td><div style='font-size:15px;font-weight:bold;'>Categorie video</div><small>".(isset($error[4]) && $error[4] ? $error[4] : "Alege si cea mai potrivita subcategorie")."</small></td>";
echo "<td><select name='cat' class='my-textboxBig'>";
getCats(0);
echo "</select></td></tr>";

echo "<tr class='lightonhoverF".(isset($error[1]) && $error[1] ? " notered" : "")."'><td><div style='font-size:15px;font-weight:bold;'>URL (YouTube link)</div><small>".(isset($error[1]) && $error[1] ? $error[1] : "Nu confunda cu codul embed!")."</small></td>";
echo "<td><input type='text' value='$url' name='url' class='my-textboxBig' /></td></tr>";

if (isset($embed)) {
	echo "<tr valign='top'><td><div style='font-size:15px;font-weight:bold;'>Previzualizare</div><a href='/video/".urltext($title).".".$edit."' target='_blank'>Deschide in pagina noua</a></td><td>".showVideo($embed,0,356,267)."</td></tr>";
}

echo "<tr valign='top'><td><div style='font-size:15px;font-weight:bold;'>Optiuni</div></td><td>";
echo "<label class='vizibil lightonhover' style='padding:4px;'><input type='checkbox' name='comments' value='c'".($comments ? " checked='checked'" : "")." /> Activeaza comentariile</label>";
echo "<label class='vizibil lightonhover spacer' style='padding:4px;'><input type='checkbox' name='ratings' value='r'".($ratings ? " checked='checked'" : "")." /> Activeaza evaluarile</label><br />";
echo "<input type='submit' value='".($edit ? "Actualizeaza" : "Salveaza")." video' name='savevideo' />".($edit ? " <a href='".PAGE_SELF."$key'>Renunta</a>" : "")."</td></tr>";
echo "</table></form>";

require_once SCRIPTS."footer.php";
?>
