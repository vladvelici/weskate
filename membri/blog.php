<?php
require_once "../mainfile.php";

if (!iMEMBER) { redirect("conectare.php?redirto=/membri/blog.php"); }
$user_data = $userdata;
$CuloarePagina = $userdata['user_culoarepagina'];

require_once SCRIPTS."header.php";

if (isset($_GET['enable'])) {
	if (!isset($_GET['key']) || $_GET['key']!=$_SESSION['user_key']) {
		redirect("blog.php?done=key");
	}
	if ($_GET['enable'] == "true" && $userdata['user_blog'] != 1) {
		$result = dbquery("UPDATE ".DB_USERS." SET user_blog='1' WHERE user_id=".$userdata['user_id']);
		redirect("blog.php?done=enabled");
	} else if ($_GET['enable'] == "false" && $userdata['user_blog'] != 0) {
		$result = dbquery("UPDATE ".DB_USERS." SET user_blog='0' WHERE user_id=".$userdata['user_id']);
		redirect("blog.php?done=disabled");
	}
}
if (isset($_POST['newblog'])) {
	if (!isset($_POST['userkey']) || $_POST['userkey']!=$_SESSION['user_key']) {
		redirect("blog.php?done=key");
	}
	$newblog = htmlsafe($_POST['newblog']);
	if (stripos($newblog,"http://") !== 0) {
		$newblog = "http://".$newblog;
	}
	$result = dbquery("UPDATE ".DB_USERS." SET user_blog='$newblog' WHERE user_id=".$userdata['user_id']);
	redirect("blog.php?done=external");
}

add_to_head("<link rel='stylesheet' href='http://weskate.ro/look/stilprofil.php?user_id=".$userdata['user_id']."' type='text/css' media='screen' />\n");

	echo "<table cellpadding='25' cellspacing='0' width='100%'><tr>

	<td align='left'>
	<span style='font-size:20px;text-transform:uppercase;'>Profil</span><span class='namecolor' id='username_color'>".$user_data['user_name']."</span>
	</td>

	<td align='right' width='50%'><div class='round MeniuRotunjit'>
	<a href='http://profil.weskate.ro/".$user_data['user_profileurl']."' style='border-left:0px;' onclick='return CheckReturnToBlog();'>Profil</a><span>Blog</span><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/my' onclick='return CheckReturnToBlog();'>My</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/favorite' onclick='return CheckReturnToBlog();'>Favorite</a><a href='http://profil.weskate.ro/".$user_data['user_profileurl']."/prieteni' onclick='return CheckReturnToBlog();'>Prieteni</a>";
	echo "</div>
	</td>

	</tr></table>
	";

echo "<div style='text-align:right;display:block;padding:5px;'><a href='http://profil.weskate.ro/".$userdata['user_profileurl']."/blog' onclick='return CheckReturnToBlog();' style='font-size:14px;'><strong>&lsaquo;&lsaquo;</strong> Intoarce-te la blog</a></div>";

if (isset($_GET['done'])) {
	$msg_class = "notegreen";
	if ($_GET['done'] == "up") {
		$message = "Actualizat cu succes";
	} elseif ($_GET['done'] == "add") {
		$message = "Adaugat cu succes.";
	} elseif ($_GET['done'] == "del") {
		$message = "Sters cu succes.";
	} elseif ($_GET['done'] == "uf") {
		$message = "Actualizare nereusita. Nu poti modifica doar postarile din blogul tau.";
		$msg_class = "notered";
	} elseif ($_GET['done'] == "ndl") {
		$message = "Imposibil de sters. Postarea nu iti apartine sau nu exista.";
		$msg_class = "notered";
	} elseif ($_GET['done'] == "external") {
		$message = "Acum folosești un blog extern.";
		$msg_class = "notegreen";
	} elseif ($_GET['done'] == "enabled") {
		$message = "Blog-ul tău WeSkate a fost activat.";
		$msg_class = "notegreen";
	} elseif ($_GET['done'] == "disabled") {
		$message = "Blogul tău a fost dezactivat.";
		$msg_class = "notegreen";
	} elseif ($_GET['done'] == "key") {
		$message = "Acces respins";
		$msg_class = "notered";
	}
	if (isset($message)) { echo "<div class='".$msg_class."'>".$message."</div>\n"; }
}

if (isnum($userdata['user_blog']) && $userdata['user_blog']==0) {
	echo "<div style='padding:20px;text-align:center;'><span style='font-size:18px;'>Blogul tău este inactiv!</span><br/><br/><a href='blog.php?enable=true&amp;key=".$_SESSION['user_key']."'>Activează!</a><br /><br /><strong>Am deja un blog! Adresa url:</strong><br />
<form name='change' action='blog.php' method='post'>
<input type='hidden' name='userkey' value='".$_SESSION['user_key']."' />
<input type='text' name='newblog' value='' />
<input type='submit' value='Schimbă' />
</form></div>";
} else if ($userdata['user_blog']!=1) {
	echo "<div style='padding:20px;text-align:center;'><span style='font-size:18px;'>Folosești un blog extern.</span><br/><br/><a href='blog.php?enable=true&amp;key=".$_SESSION['user_key']."'>Folosește blog-ul WeSkate</a><br /><a href='".$userdata['user_blog']."' target='_blank'>Vizitează blogul extern</a><br /><br />
<strong>Schimbă blog-ul extern:</strong><br />
<form name='change' action='blog.php' method='post'>
<input type='hidden' name='userkey' value='".$_SESSION['user_key']."' />
<input type='text' name='newblog' value='".$userdata['user_blog']."' />
<input type='submit' value='Schimbă' />
</form></div>";
} else if (isnum($userdata['user_blog'])) {
echo "<a href='blog.php?enable=false&amp;key=".$_SESSION['user_key']."'>Dezactivează-mi blogul</a>";

echo "<div><strong>Vreau blog extern!</strong>
<form name='change' action='blog.php' method='post'>
<input type='hidden' name='userkey' value='".$_SESSION['user_key']."' />
URL : <input type='text' name='newblog' value='' />
<input type='submit' value='Schimbă' />
</form></div>";

if (isset($_POST['save'])) {
	$blog_subject = trim(htmlsafe($_POST['blog_subject']));

	$blog_blog = trim(htmlsafe($_POST['blog_blog']));
	$blog_visibility = isnum($_POST['blog_visibility']) ? $_POST['blog_visibility'] : "0";
		//0 = orice, 1= membru, 2=prieten, 3=privat.
	$blog_draft = isset($_POST['blog_draft']) ? "1" : "0";
	$blog_page = isset($_POST['blog_page']) ? "1" : "0";
	$blog_comments = isset($_POST['blog_comments']) ? "1" : "0";
	$blog_ratings = isset($_POST['blog_ratings']) ? "1" : "0";
	$blog_time = time();
	if (isset($_POST['blog_id']) && isnum($_POST['blog_id'])) {
		if (dbcount("(blog_id)",DB_BLOG,"blog_id='".$_POST['blog_id']."' AND blog_user='".$userdata['user_id']."'")) {
			$result = dbquery("UPDATE ".DB_BLOG." SET 
			blog_subject='$blog_subject',
			blog_blog='$blog_blog',
			blog_edit_datestmp='$blog_time',
			blog_edit_user='".$userdata['user_id']."',
			blog_visibility='$blog_visibility',
			blog_draft='$blog_draft',
			blog_page='$blog_page',
			blog_allow_comments='$blog_comments',
			blog_allow_ratings='$blog_ratings'
			WHERE blog_id='".$_POST['blog_id']."'");
			if (!$blog_draft) {
				indexItem($_POST['blog_id'],"B",killRoChars($blog_subject),killRoChars($blog_blog),keywordize($blog_subject),0,$blog_visibility,$blog_datestamp);
			}
			redirect(PAGE_SELF."?done=up");
		} else {
			redirect(PAGE_SELF."?done=uf");
		}
	} else {
		$result = dbquery("INSERT INTO ".DB_BLOG." (blog_subject,  blog_blog, blog_user, blog_datestamp, blog_visibility, blog_draft, blog_page, blog_reads, blog_allow_comments, blog_allow_ratings) VALUES ('$blog_subject', '$blog_blog', '".$userdata['user_id']."', '$blog_time', '$blog_visibility', '$blog_draft', '$blog_page', '0', '$blog_comments', '$blog_ratings')");
		if (!$blog_draft) {
			$result2=dbquery("SELECT blog_id FROM ".DB_BLOG." WHERE blog_datestamp='$blog_time' AND blog_user='".$userdata['user_id']."'");
			$getID = dbarray($result2);
			indexItem($getID['blog_id'],"B",killRoChars($blog_subject),killRoChars($blog_blog),keywordize($blog_subject),0,$blog_visibility,$blog_datestamp);
		}
		redirect(PAGE_SELF."?done=add");

	}
} else if (isset($_POST['delete']) && (isset($_POST['blog_id']) && isnum($_POST['blog_id']))) {
	
	if (dbcount("(blog_id)",DB_BLOG,"blog_id='".$_POST['blog_id']."' AND blog_user='".$userdata['user_id']."'")) {

		$result = dbquery("DELETE FROM ".DB_BLOG." WHERE blog_id='".$_POST['blog_id']."'");
		$result = dbquery("DELETE FROM ".DB_COMMENTS."  WHERE comment_item_id='".$_POST['blog_id']."' and comment_type='B'");
		$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_item_id='".$_POST['blog_id']."' and rating_type='B'");
		$result = dbquery("DELETE FROM ".DB_FAVORITE." WHERE item_id='".$_POST['blog_id']."' AND fav_type='B'");
		deleteIndex("B",$_POST['blog_id']);
		redirect(PAGE_SELF."?done=del");
	} else {
		redirect(PAGE_SELF."?done=ndl");
	}

} else {
	if (isset($_POST['preview'])) {
		$blog_subject = trim(htmlsafe($_POST['blog_subject']));

		$blog_blog = trim(htmlsafe($_POST['blog_blog']));
		$blog_preview = nl2br($blog_blog);
		$blog_visibility = isnum($_POST['blog_visibility']) ? $_POST['blog_visibility'] : "0";
		$blog_draft = isset($_POST['blog_draft']) ? " checked='checked'" : "";
		$blog_page = isset($_POST['blog_page']) ? " checked='checked'" : "";
		$blog_comments = isset($_POST['blog_comments']) ? " checked='checked'" : "";
		$blog_ratings = isset($_POST['blog_ratings']) ? " checked='checked'" : "";

			echo "<div class='blog-inside-dark'>\n";

			echo "<table cellpadding='7' cellspacing='0' width='100%'>";
			echo "<tr valign='top'>\n";
			echo "<td class='title-blog'>\n";
			echo "<a href='#' onclick='return false;'>".str_replace(array("&amp;#537;","&amp;#539;","&amp;#536;","&amp;#538;"),array("&#351;","&#355;","&#350;","&#354;"),$blog_subject)."</a>";
			echo "</td></tr><tr><td>";
			echo str_replace(array("&amp;#537;","&amp;#539;","&amp;#536;","&amp;#538;"),array("&#351;","&#355;","&#350;","&#354;"),$blog_preview);
			echo "</td></tr><tr><td style='padding:4px;background-image:url(http://t.img.weskate.ro/panou-mid.png);background-repeat:repeat-x;border-top:1px solid #ccc;'>";
			echo " <strong> &middot; </strong> Postat in <a href='http://profil.weskate.ro/".$userdata['user_profileurl']."/blog' onclick='return false;'><strong>blogul lui ".$userdata['user_name']."</strong></a> la <strong>".date("j F Y",time())."</strong>.";
			echo "</td></tr>";
			echo "</table>\n";

			echo "</div>\n";

			echo "<script type=\"text/javascript\">\n";
			echo "Rounded('blog-inside-dark',10,10);\n";
			echo "</script>\n";


	}
	$result = dbquery("SELECT * FROM ".DB_BLOG." WHERE blog_user='".$userdata['user_id']."' ORDER BY blog_draft DESC, blog_datestamp DESC");
	if (dbrows($result) != 0) {
		$editlist = ""; $sel = "";
		while ($data = dbarray($result)) {
			if ((isset($_POST['blog_id']) && isnum($_POST['blog_id'])) || (isset($_GET['blog_id']) && isnum($_GET['blog_id']))) {
				$blog_id = isset($_POST['blog_id']) ? $_POST['blog_id'] : $_GET['blog_id'];
				$sel = ($blog_id == $data['blog_id'] ? " selected='selected'" : "");
			}
			$editlist .= "<option value='".$data['blog_id']."'$sel>".($data['blog_draft'] ? "[ciorna] " : "").$data['blog_subject']."</option>\n";
		}

		echo "<div style='text-align:center;display:block;border:1px solid #ccc;background-color:#eee;padding:5px;margin-bottom:7px;'>\n
		<span style='font-size:13px;font-weight:bold;display:block;padding-bottom:3px;'>Alege un blog existent pentru editare sau stergere :</span>
		<form name='selectform' method='post' action='".PAGE_SELF."?action=edit'>\n";
		echo "<select name='blog_id' class='textbox' style='width:250px'>\n".$editlist."</select>\n";
		echo "<input type='submit' name='edit' value='Editeaza' class='button' />\n";
		echo "<input type='submit' name='delete' value='Sterge' onclick='return Deleteblog();' class='button' />\n";
		echo "</form>\n</div>\n";

	}

	if ((isset($_GET['action']) && $_GET['action'] == "edit") && (isset($_POST['blog_id']) && isnum($_POST['blog_id'])) || (isset($_GET['blog_id']) && isnum($_GET['blog_id']))) {
		$result = dbquery("SELECT * FROM ".DB_BLOG." WHERE blog_id='".(isset($_POST['blog_id']) ? $_POST['blog_id'] : $_GET['blog_id'])."' AND blog_user='".$userdata['user_id']."'");
		if (dbrows($result)) {
			$data = dbarray($result);
			$blog_subject = str_replace(array("&amp;#537;","&amp;#539;","&amp;#536;","&amp;#538;"),array("&#351;","&#355;","&#350;","&#354;"),$data['blog_subject']);

			$blog_blog = str_replace(array("&amp;#537;","&amp;#539;","&amp;#536;","&amp;#538;"),array("&#351;","&#355;","&#350;","&#354;"),$data['blog_blog']);
			$blog_visibility = intval($data['blog_visibility']);
			$blog_draft = $data['blog_draft'] == "1" ? " checked='checked'" : "";
			$blog_page = ($data['blog_page'] == "1" && $data['blog_visibility'] == "0") ? " checked='checked'" : "";
			$blog_comments = $data['blog_allow_comments'] == "1" ? " checked='checked'" : "";
			$blog_ratings = $data['blog_allow_ratings'] == "1" ? " checked='checked'" : "";
		} else {
			redirect(PAGE_SELF);
		}
	}
	if ((isset($_POST['blog_id']) && isnum($_POST['blog_id'])) || (isset($_GET['blog_id']) && isnum($_GET['blog_id']))) {
		$titlul="Editare blog";
	} else {
		if (!isset($_POST['preview'])) {
			$blog_subject = "";

			$blog_blog = "";
			$blog_visibility = 0;
			$blog_draft = "";
			$blog_page = " checked='checked'";
			$blog_comments = " checked='checked'";
			$blog_ratings = " checked='checked'";
		}
		$titlul = "Adauga blog";
	}

		//0=public, 1=membru, 2=prieten, 3=privat
	$visibility_opts = "<option value='0' ".($blog_visibility == 0 ? " selected='selected'" : "").">Oricine poate citi aceasta postare</option>";
	$visibility_opts .= "<option value='1' ".($blog_visibility == 1 ? " selected='selected'" : "").">Postare vizibila doar pentru membri inregistrati</option>";
	$visibility_opts .= "<option value='2' ".($blog_visibility == 2 ? " selected='selected'" : "").">Doar prietenii mei pot citi aceasta postare</option>";
	$visibility_opts .= "<option value='3' ".($blog_visibility == 3 ? " selected='selected'" : "").">Postarea este privata. Doar eu o pot vedea</option>";

	echo "<form name='inputform' method='post' action='".PAGE_SELF."' onsubmit='return ValidateForm(this);'>\n";
	echo "<table cellpadding='4' cellspacing='0' class='center round' width='100%' style='border:1px solid #ccc;background-color:#eee;' >\n<tr>\n";
	echo "<td>&nbsp;</td><td class='capmain'>Adauga blog</td>\n";
	echo "</tr><tr>\n";	
	echo "<td width='100' style='text-align:right;padding-right:3px;font-weight:bold;'>Subiect : </td>\n";
	echo "<td width='80%' ><input type='text' name='blog_subject' id='bl_subj' value='".stripslashes($blog_subject)."' class='textbox' style='width: 250px' /></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td valign='top' width='100' style='text-align:right;padding-right:3px;font-weight:bold;'>Continut :</td>\n";
	echo "<td width='80%' ><textarea name='blog_blog' id='bl_blog' cols='95' rows='10' class='textbox' style='width:98%'>".stripslashes($blog_blog)."</textarea></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<td style='text-align:right;padding-right:3px;font-weight:bold;'>Vizibilitate : </td>\n";
	echo "<td ><select name='blog_visibility' id='bl_visibility' class='textbox' onchange=\"SetPage();\">\n".$visibility_opts."</select></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td style='text-align:right;padding-right:3px;font-weight:bold;vertical-align:top;'>Optiuni : </td><td >\n";
	echo "<div style='padding:3px;display:block;'><label><input type='checkbox' name='blog_draft' value='yes'".$blog_draft." /> Ciorna?</label></div>\n";
	echo "<div style='padding:3px;display:block;'><label><input type='checkbox' name='blog_page' id='bl_page' value='yes'".$blog_page." /> Afiseaza pe prima pagina a sectiunii Blog? (trebuie sa fie public)</label></div>\n";
	echo "<div style='padding:3px;display:block;'><label><input type='checkbox' name='blog_comments' id='bl_comments' value='yes'".$blog_comments." /> Activeaza comentarii</label></div>\n";
	echo "<div style='padding:3px;display:block;'><label><input type='checkbox' name='blog_ratings' id='bl_ratings' value='yes'".$blog_ratings." /> Activeaza evaluari</label></div></td>\n";
	echo "</tr>\n<tr>\n";
	echo "<td align='center' colspan='2' ><br />\n";
	if ((isset($_POST['edit']) && (isset($_POST['blog_id']) && isnum($_POST['blog_id']))) || (isset($_POST['preview']) && (isset($_POST['blog_id']) && isnum($_POST['blog_id']))) || (isset($_GET['blog_id']) && isnum($_GET['blog_id']))) {
		echo "<input type='hidden' name='blog_id' value='".(isset($_POST['blog_id']) ? $_POST['blog_id'] : $_GET['blog_id'])."' />\n";
		$save_msg = "Actualizeaza";
	} else {
		$save_msg = "Salveaza";
	}
	echo "<input type='submit' name='preview' value='Previzualizeaza' class='button' />\n";
	echo "<input type='submit' name='save' value='$save_msg' class='button' /></td>\n";
	echo "</tr>\n</table>\n</form>\n";
	closetable();
	echo "<script type='text/javascript'>\n
		function Deleteblog() {\n
			return confirm('Esti sigur ca vrei sa stergi?');\n
		}\n
		function ValidateForm(frm) {\n
			if(frm.blog_subject.value=='' || frm.blog_blog.value=='') {\n
				alert('Nu poti salva un blog fara continut sau subiect.');\n
				return false;\n
			}\n
	      	}\n
		function SetPage() {\n
			if (document.getElementById('bl_visibility').value == 0) {\n
				document.getElementById('bl_page').disabled = false;\n
			} else {\n
				document.getElementById('bl_page').checked = false;\n
				document.getElementById('bl_page').disabled = true;\n
			}\n
		}\n
		SetPage();

	     </script>\n";
}

echo "	<script type='text/javascript'>\n
	function CheckReturnToBlog() {\n
			if(document.getElementById('bl_subj').value!='' || document.getElementById('bl_blog').value!='') {\n
				return confirm('Daca vei continua vei pierde toate modificarile efectuate nesalvate. Daca vrei sa salvezi fara sa publici articolul, salveaza-l ca ciorna si nimeni nu va putea sa-l vada pana nu il faci public. Esti sigur ca vrei sa pleci de pe aceasta pagina?');\n
			}\n
	      	}\n</script>\n";
}
require_once SCRIPTS."footer.php";
?>
