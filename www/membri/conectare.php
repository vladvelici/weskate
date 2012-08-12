<?php
require_once "../mainfile.php";
require_once BASEDIR."scripts/header.php";

$domains = array(1 => "profil", 2 => "ajutor");

if (iMEMBER && isset($_GET['logout']) && $_GET['logout'] == $_SESSION['user_key']) {
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}
	session_destroy();
	session_start();
	$_SESSION['logged_out'] = true;
	if ($userdata['user_cookie'] && $_COOKIE['wskusr']) {
		setcookie("wskusr", "", time() - 42000, "/", ".weskate.ro",false,true);
		$result = dbquery("UPDATE ".DB_USERS." SET user_cookie='' AND user_cookie_exp=0 WHERE user_id=".$userdata['user_id']);
	}
	$userdata = array();
	//"fine" redirect
	$redir = (isset($_GET['redirto']) ? $_GET['redirto'] : false);
	if (!$redir || stripos("conectare.php",$redir)) redirect("../index.php");
	if ($redir == "subd") {
		if (isset($_GET['subd']) && isnum($_GET['subd'])) {
			$subd = $_GET['subd'];
			if ($subd > 1 || $subd < 0) {
				$redirto = BASEDIR."index.php";
			} else {
				$redirto = "http://".$domains[$subd].".weskate.ro";
				if (isset($_GET['pr']) && $_GET['pr'][0]=="/") {
					$redirto .= $_GET['pr'];
				} else {
					$redirto = BASEDIR."index.php";
				}
			}
		} else {
			$redirto = BASEDIR."index.php";
		}
	} elseif ($redir[0] == "/") {
		$redirto = $redir;
	} else {
		$redirto = BASEDIR."index.php";
	}
	redirect($redirto);

}

if (iMEMBER) { redirect(BASEDIR."?err=c1"); }
$username=false; $err=false;
if (isset($_POST['user']) && isset($_POST['pass'])) {
	$user = htmlsafe($_POST['user']);
	$raw_pass = $_POST['pass'];
	$result = dbquery("SELECT user_lastvisit,user_joined FROM ".DB_USERS." WHERE user_name='$user' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		$salt = max($data['user_lastvisit'],$data['user_joined']);
		$pass = hash("sha512",$raw_pass.$salt);
		$result = dbquery("SELECT user_id FROM ".DB_USERS." WHERE user_name='$user' AND user_password='$pass' LIMIT 1");
		if (dbrows($result)) {
			//login successfully
			$data = dbarray($result);
			$user_id = $data['user_id'];

			//update user_lastvisit, update user_password
			$lastvisit = time();
			$new_password = hash("sha512",$raw_pass.$lastvisit);

			//set up remember me cookie (if requested and not already set)
			$update = "";
			$nextPasswordCheck = time()+(60*60*24*5); //5 days in future

			if (isset($_POST['remember'])) {
				$expire = time()+(60*60*24*30); //30 days in future
				$checkUnique = true;
				while ($checkUnique) {
					$unique = getRandomString(mt_rand(64,128));
					$checkUnique = dbrows(dbquery("SELECT user_id FROM ".DB_USERS." WHERE user_cookie='".$unique."' LIMIT 1"));
				}
				if (setcookie("wskusr", $unique, $expire, "/", ".weskate.ro",false,true)) {
					$update = ", user_cookie=$unique";
				}
			}
			$result = dbquery("UPDATE ".DB_USERS." SET user_lastvisit=$lastvisit, user_password='$new_password', user_cookie_exp=$nextPasswordCheck $update WHERE user_id='$user_id'");
			//set up the session login vars
			$_SESSION['user_id']=$user_id;
			$_SESSION['user_key']=getRandomString(64);


			//redirect to the link user was few times ago
			$redir = (isset($_GET['redirto']) ? $_GET['redirto'] : false);
			if (!$redir || stripos("conectare.php",$redir)) redirect("../index.php");
			if ($redir == "subd") {

				if (isset($_GET['subd']) && isnum($_GET['subd'])) {
					$subd = $_GET['subd'];
					if ($subd > 1 || $subd < 0) {
						$redirto = BASEDIR."index.php";
					} else {
						$redirto = "http://".$domains[$subd].".weskate.ro";
						if (isset($_GET['pr']) && $_GET['pr'][0]=="/") {
							$redirto .= $_GET['pr'];
						} else {
							$redirto = BASEDIR."index.php";
						}
					}
				} else {
					$redirto = BASEDIR."index.php";
				}
			} elseif ($redir[0] == "/") {
				$redirto = $redir;
			} else {
				$redirto = BASEDIR."index.php";
			}
			redirect($redirto);
		} else {
			$err=true;
		}

	} else {
		$err = true;
	}
} elseif (isset($_COOKIE['wskusr'])) {
	$login = sqlsafe($_COOKIE['wskusr']);
	$result = dbquery("SELECT user_name,user_cookie_exp WHERE user_cookie='$login' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		if ($data['user_cookie_exp'] <= time()) {
			$username = $data['user_name'];
		}
	}
} else {
	$username=false;
}





echo "<div class='flright smallround' style='background-color:#eee;border:3px solid #999;width:250px;'>";
echo "<form name='login' method='post' action='".PAGE_REQUEST."'>";

echo "<div style='border-bottom:1px dotted #333;font-size:18px;font-weight:bold;padding:5px 5px 5px 26px;background-image:url(http://img.weskate.ro/conectare.png);background-repeat:no-repeat;background-position:5px center;margin-bottom:6px;'>Conectare</div>";

echo "<div style='padding:6px;'>";
echo "<span style='font-weight:bold;font-size:16px;'>";
if ($err) {
	echo "Ai greșit numele de utilizator sau parola. Încearcă din nou.";
} else {
	echo ($username ? "Bună $username! Te rog autentifică-te. (<a href='http://ajutor.weskate.ro/conectare#pass'>De ce?</a>)" : "Bun venit pe WeSkate!");
}
echo "</span><hr />";
echo "<span style='font-size:14px;".($err ? "color:#f00;" : "")."'>Nume utilizator:</span><br />";
if (!$username) {
	echo "<input type='text' name='user' value='' style='width:233px;' /><br />";
} else {
	echo "<strong>$username</strong>\n<input type='hidden' name='user' value='$user' />";
}
echo "<span style='font-size:14px;".($err ? "color:#f00;" : "")."'>Parolă:</span><br />";
echo "<input type='password' name='pass' value='' style='width:233px;' />";
if (!$username) {
	echo "<hr />";
	echo "<label class='vizibil'><input type='checkbox' name='remember' value='1' /> <strong>Ține-mă conectat 30 de zile.</strong></label>";
}
echo "<hr />";
echo "<div style='text-align:right;'><input type='submit' name='login_sumbit' value='Conectează-mă' /></div>";
echo "<hr />";
echo "<div style='font-size:14px;'>";
if ($username) {
	echo "<strong>Nu ești <em>$username</em>?</strong><br /><a href='conectare.php?other'>Autentifică-te</a> cu alt cont.</div>";
} else {
	echo "<strong>Nu ai cont?</strong><br /><a href='inregistrare.php'>Înregistrarea</a> este ușoară și rapidă.</div>";
}
echo "<hr />";
echo "<div style='font-size:14px;'><strong>Probleme?</strong><br /><a href='resetare.php'>Resetare parolă</a><br /><a href='http://ajutor.weskate.ro/conectare'>Nu mă pot conecta!</a></div>";
echo "</div>";

echo "</form>";

echo "</div>";
opentable("Conectează-te");
if (isset($_GET['activat'])) {
	echo "<div class='notegreen' style='width:690px;'>Contul tău a fost activat cu succes! Acum te poți conecta!</div>";
} else if (isset($_GET['update']) && $_GET['update'] == "success") {
	echo "<div class='notegreen' style='width:690px;'>Contul tău a fost actualizat pentru WeSkate 5.1 cu succes! Acum te poți conecta!</div>";
} else {
	echo "<div class='noteyellow' style='width:690px;'>Nu te vei putea conecta până nu îți actualizezi contul pentru versiunea WeSkate 5.1. <a href='newpass.php'>Actualizează!</a></div>";
}
echo "<img src='http://img.weskate.ro/login_img.jpg' alt='box' style='border:2px solid #ccc;margin-left:3px;'/>";
echo "<p style='font-size:17px;margin-left:7px;'><strong>Demonstrează că ești bun!</strong><br /><span style='font-size:15px;'>Validează-ți tricurile încarcând video-uri și skill-ul din profil îți va crește.</span></p>";
echo "<p style='font-size:17px;margin-left:7px;'><strong>HORSE online</strong><br /><span style='font-size:15px;'>Renumitul joc <em>HORSE</em> este acum și online - doar cu tricurile tale!</span></p>";
echo "<div style='clear:both;'></div>";

require_once BASEDIR."scripts/footer.php";
?>
