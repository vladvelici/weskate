<?php
require_once "../mainfile.php";
if (!iMEMBER) redirect("conectare.php?redirto=".PAGE_REQUEST);
require_once SCRIPTS."header.php";
opentable("Contul meu");
if (isset($_POST['url'])) {
	$error = "";
	$url = sqlsafe($_POST['url']);
	if (preg_match('/[^a-zA-Z0-9]/', $url)) {
		$error .= "Conține caractere invalide.";
		die();
	}
	if (dbcount("(user_id)",DB_USERS,"user_profileurl='$url'".(iMEMBER ? " AND user_id!=".$userdata['user_id'] : ""))) {
		$error .= "<br />Este deja folosit.";

	}
	if (strlen($url) < 3) {
		$error .= "<br />URL prea scurt. Minim 3 caractere.";
	}
	if ($error) {
		echo "<div class='notered'>$error</div>";
	} else {
		if (isset($_POST['key']) && $_POST['key'] == $_SESSION['user_key']) {
			$url = strtolower($url);
			$result = dbquery("UPDATE ".DB_USERS." SET user_profileurl='$url' WHERE user_id=".$userdata['user_id']."");
			echo "<div class='notegreen'>URL actualizat cu succes!</div>";
			$userdata['user_profileurl']=$url;
		} else {
			echo "<div class='notered'>Acces respins.</div>";
		}
	}
} else if (isset($_POST['email'])) {
	if (isset($_POST['key']) && $_POST['key'] == $_SESSION['user_key']) {
		$email = sqlsafe($_POST['email']);
		require_once SCRIPTS."validate_email.php";
		if (validEmail($email)) {
			$exista = dbcount("(user_id)",DB_USERS,"user_email='".sqlsafe($_POST['email'])."' AND user_id!=".$userdata['user_id']);
			if (!$exista) {
				$result = dbquery("UPDATE ".DB_USERS." SET user_email='$email' WHERE user_id=".$userdata['user_id']);
				echo "<div class='notegreen'>E-mail actualizat cu succes!</div>";
				$userdata['user_email'] = $email;
			} else {
				echo "<div class='notered'>E-mail folosit deja</div>";
			}
		} else {
			echo "<div class='notered'>E-mail invalid</div>";
		}
	} else {
		echo "<div class='notered'>Acces respins.</div>";
	}
} else if (isset($_POST['password']) && isset($_POST['passwordCheck']) && isset($_POST['oldpass']) && isset($_POST['key'])) {
	if ($_POST['key'] != $_SESSION['user_key']) {
		echo "<div class='notered'>Acces respins</div>";
	} else {
		$oldpass = hash("sha512",$_POST['oldpass'].max($userdata['user_lastvisit'],$userdata['user_joined']));
		if ($oldpass == $userdata['user_password']) {
			if (passwordStrengh($_POST['password']) >= 26) {
				if ($_POST['password'] == $_POST['passwordCheck']) {
					$newpass = hash("sha512",$_POST['password'].max($userdata['user_lastvisit'],$userdata['user_joined']));
					$result = dbquery("UPDATE ".DB_USERS." SET user_password='$newpass' WHERE user_id=".$userdata['user_id']);
					$userdata['user_password']=$newpass;
					echo "<div class='notegreen'>Parola ta a fost schimbată cu succes!</div>";
				} else {
					echo "<div class='notered'>Parolele noi nu coincid.</div>";
				}
			} else {
				echo "<div class='notered'>Parolă nouă prea slabă.</div>";
			}
		} else {
			echo "<div class='notered'>Ai greșit parola veche!</div>";
		}
	}
} else if (isset($_POST['profile_privacy']) && isset($_POST['email_privacy'])) {
	if (isset($_POST['key']) && $_POST['key'] == $_SESSION['user_key']) {
		$profile = intval($_POST['profile_privacy']);
		$email = ($_POST['email_privacy'] == 1 ? "1" : "0");
		if ($profile >= 1 && $profile <=4) {
			$result = dbquery("UPDATE ".DB_USERS." SET user_hide_email=$email, user_visibility=$profile WHERE user_id=".$userdata['user_id']);
			$userdata['user_hide_email']=$email; $userdata['user_visibility']=$profile;
			echo "<div class='notegreen'>Setările de intimitate au fost actualizate cu succes.</div>";
		} else {
			echo "<div class='notered'>Am întâmpinat o eroare la salvarea noilor setări!</div>";
		}
	} else {
		echo "<div class='notered'>Acces respins</div>";
	}
}

if (isset($_GET['url'])) {
	echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Schimbă URL-ul profilului</strong><br /><br />";
	echo "<form action='account.php' method='post'>";
	add_to_head("<script type='text/javascript' src='http://weskate.ro/membri/register.js'></script>");
	echo "URL profil<br />";
	echo "<div class='small' style='text-indent:-125px;'>";
	echo "http://profil.weskate.ro/<input type='text' value='".$userdata['user_profileurl']."' name='url' onkeyup='validateURL(this.value);' onblur='validateURL(this.value);' />";
	echo "</div>";
	echo "<span id='urlstatus' style='font-size:12px;font-weight:bold;display:block;padding:3px;height:15px;'></span><span class='small'>Doar caracterele alfanumerice (a-z 0-9) sunt permise.</span><br /><br />";
	echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
	echo "<input type='submit' name='newurl' value='Schimbă URL-ul' />";
	echo "</form>";
	echo "</div>";
} else if (isset($_GET['email'])) {
	echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Schimbă adresa de e-mail</strong><br /><br />";
	echo "<form action='account.php' method='post'>";
	add_to_head("<script type='text/javascript' src='http://weskate.ro/membri/register.js'></script>");
	echo "E-mail nou<br />";
	echo "<div class='small'>";
	echo "<input type='text' value='' name='email' onkeyup='validateEmail(this.value);' onblur='validateEmail(this.value);' />";
	echo "</div>";
	echo "<span id='email_status' style='font-size:12px;font-weight:bold;display:block;padding:3px;height:15px;'></span><span class='small'>Trebuie să fie valid. Vei primi un e-mail de verificare.</span><br /><br />";
	echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
	echo "<input type='submit' value='Actualizează e-mail' />";
	echo "</form>";
	echo "</div>";
} else if (isset($_GET['parola'])) {
	echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Schimbă parola</strong><br /><br />";
	echo "<form action='account.php' method='post'>";
	add_to_head("<script type='text/javascript' src='http://weskate.ro/membri/register.js'></script>");
	add_to_head("<link rel='stylesheet' href='http://weskate.ro/membri/register.css' type='text/css' media='screen' />");
	echo "<div>";
	echo "Parolă veche<br />";
	echo "<input type='password' value='' name='oldpass' />";
	echo "</div>";
	echo "<div>";
	echo "Parolă nouă<br />";
	echo "<div id='password_status' style='height:18px;width:100px;' class='flright'></div>";
	echo "<input type='password' value='' name='password' id='password' onkeyup='passCheck(this.value);' onblur='validateEmail(this.value);' /><br />";
	echo "</div>";
	echo "<div>";
	echo "Parolă nouă (verificare)<br />";
	echo "<input type='password' value='' name='passwordCheck' id='passwordCheck' onkeyup='pass2(this.value);' onblur='pass2(this.value);' />";
	echo "<div id='passwordCheck_status' style='height:18px;width:100px;' class='flright'></div>";
	echo "</div>";
	echo "<span class='small'>Folosește minim o literă mare, una mică, o cifră și un caracter special. Lungimea recomandată este de 14 caractere.</span><br /><br />";
	echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
	echo "<input type='submit' value='Schimbă parola' />";
	echo "</form>";
	echo "</div>";
} else if (isset($_GET['avatar'])) {
	echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Indisponibil momentan</strong><br /><br />";

	echo "<strong>Ne pare rău, dar momentan suportam doar Gravatar.</strong><br /><br /><a href='account.php'>înapoi</a>";

	echo "</div>";
} else if (isset($_GET['privacy'])) {
	echo "<div style='width:300px;margin:5px auto 5px auto;font-size:16px;'><strong>Opțiuni intimitate</strong><br /><br />";
	echo "<form action='account.php' method='post'>";
	echo "Următorul grup de utilizatori îmi poate vedea profilul:<br />";
	echo "<div>";
	echo "<select name='profile_privacy'>";
	echo "<option value='1'".($userdata['user_visibility'] == 1 ? " selected='selected'" : "").">vizitatori</option>";
	echo "<option value='2'".($userdata['user_visibility'] == 2 ? " selected='selected'" : "").">vizitatori înregistrați</option>";
	echo "<option value='3'".($userdata['user_visibility'] == 3 ? " selected='selected'" : "").">prietenii mei</option>";
	echo "<option value='4'".($userdata['user_visibility'] == 4 ? " selected='selected'" : "").">doar eu</option>";
	echo "</select>";
	echo "</div>";
	echo "Adresa mea de e-mail este:<br />";
	echo "<div>";
	echo "<select name='email_privacy'>";
	echo "<option value='1'".($userdata['user_hide_email'] ? " selected='selected'" : "").">ascunsă</option>";
	echo "<option value='2'".(!$userdata['user_hide_email'] ? " selected='selected'" : "").">vizibilă</option>";
	echo "</select>";
	echo "</div>";
	echo "<input type='hidden' name='key' value='".$_SESSION['user_key']."' />";
	echo "<input type='submit' value='Actualizează setările' />";
	echo "</form>";
	echo "</div>";
} else {
	echo "<table cellpadding='4' cellspacing='2' class='round' style='border:1px solid #999;background-color:#eee;' width='100%'>";
	echo "<tr class='tbl2 lightonhoverF' valign='top'><td colspan='2' style='font-weight:bold;font-size:14px;'>Informații principale</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>Nume utilizator</td><td>".$userdata['user_name']."</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>URL profil</td><td>
	http://profil.weskate.ro/<strong>".$userdata['user_profileurl']."</strong><br /><a href='account.php?url'>schimbă</a>
	</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>E-Mail</td><td>
	".hide_email($userdata['user_email'])."<br />
	<em>".($userdata['user_hide_email'] ? "nu este public" : "este public")."</em><br /><a href='account.php?email'>schimbă</a>
	</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>Parolă</td><td><em>nu se poate afișa</em><br /><a href='account.php?parola'>schimbă parola</a></td></tr>";
	echo "<tr class='tbl2 lightonhoverF' valign='top'><td colspan='2' style='font-weight:bold;font-size:14px;'>Imagine personală (avatar)</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>".($userdata['user_avatar'] == "yahoo" || $userdata['user_avatar'] == "gravatar" ? $userdata['user_avatar'] : "imagine încărcată")."<br /><a href='account.php?avatar'>schimbă</a></td><td>".showAvatar($userdata['user_avatar'],$userdata['user_email'],$userdata['user_yahoo'])."</td></tr>";
	echo "<tr class='tbl2 lightonhoverF' valign='top'><td colspan='2' style='font-weight:bold;font-size:14px;'>Blog</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>Tip blog</td><td>";
	if (!$userdata['user_blog']) {
		echo "<em>nu folosești blog-ul</em>";
	} elseif ($userdata['user_blog'] == 1) {
		echo "<em>blog WeSkate</em>";
	} else {
		echo "<em>blog extern :</em><br /><a href='".$userdata['user_blog']."'>".$userdata['user_blog']."</a>";
	}
	echo "<br /><a href='blog.php'>Administrează blog-ul</a></td></tr>";
	echo "<tr class='tbl2 lightonhoverF' valign='top'><td colspan='2' style='font-weight:bold;font-size:14px;'>Intimitate - <a href='account.php?privacy'>schimbă</a></td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>Vizibilitate profil:</td><td>";
	if ($userdata['user_visibility'] == 1) {
		echo "public";
	} else if ($userdata['user_visibility'] == 2) {
		echo "doar utilizatori înregistrați";
	} else if ($userdata['user_visibility'] == 3) {
		echo "doar prieteni";
	} else {
		echo "doar eu";
	}
	echo "</td></tr>";
	echo "<tr class='tbl1 lightonhoverF' valign='top'><td>E-mail:</td><td>".($userdata['user_hide_email'] ? "ascuns" : "vizibil")."</td></tr>";
	echo "</table>";
}

require_once SCRIPTS."footer.php";
?>
