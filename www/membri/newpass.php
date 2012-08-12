<?php
require_once "../mainfile.php";
if (iMEMBER) { redirect(BASEDIR."?err=c1"); }
require_once BASEDIR."scripts/header.php";

if (isset($_POST['user']) && isset($_POST['old_pass']) && isset($_POST['new_pass1']) && isset($_POST['new_pass2'])) {
	$user = htmlsafe($_POST['user']);
	$old_pass = md5(md5($_POST['old_pass']));
	if ($_POST['new_pass1'] == $_POST['new_pass2']) {
		$new_pass = $_POST['new_pass1'];
	} else {
		redirect("newpass.php?err=1");
	}
	$result = dbquery("SELECT user_id,user_lastvisit,user_joined FROM ".DB_USERS." WHERE user_name='$user' AND user_password='$old_pass' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		$salt = max($data['user_lastvisit'],$data['user_joined']);
		$pass = hash("sha512",$new_pass.$salt);
		$result = dbquery("UPDATE ".DB_USERS." SET user_password='$pass' WHERE user_id=".$data['user_id']);
		if ($result) {
			redirect("conectare.php?update=success");
		} else {
			redirect("newpass.php?err=3");
		}
	} else {
		redirect("newpass.php?err=2");
	}
} else if (isset($_POST['user']) || isset($_POST['old_pass']) || isset($_POST['new_pass1']) || isset($_POST['new_pass2'])) {
	redirect("newpass.php?err=4");
}

opentable("Generare parolă nouă");

echo "<div style='font-size:14px;padding:7px;'><span style='font-weight:bold;padding:5px;display:block;'>De ce trebuie să-mi schimb parola?</span>
Am făcut o grămadă de schimbări în structura site-ului WeSkate, printre care am schimbat și metoda de stocare a parolelor, care este mai securizată. Din acest motiv, vechea ta parolă nu mai este compatibilă cu sistemul de autentificare și nu poate fi actualizată automat.
<span style='font-weight:bold;padding:5px;display:block;'>Cum fac asta?</span>
Simplu. Completează datele de mai jos și apasă <em>Actualizează-mi contul</em>.
</div>";

if (isset($_GET['err'])) {
	$err = $_GET['err'];
	if ($err == 1) {
		echo "<div class='notered'>Parolele noi nu se potrivesc.</div>";
	} else if ($err == 2) {
		echo "<div class='notered'>Ai greșit numele de utilizator sau parola veche.</div><br /><div style='text-align:center;'>Dacă ți-ai acutalizat parola la noul sistem deja, <a href='conectare.php'>click aici</a> pentru conectare.</div>";
	} else if ($err == 3) {
		echo "<div class='notered'>Eroare la actualizarea parolei. Încearcă din nou mai târziu.</div>";
	} else if ($err == 4) {
		echo "<div class='noteyellow'>Toate câmpurile sunt obligatorii!</div>";
	}
}

echo "<div class='round' style='width:300px;margin:5px auto 5px auto;border:2px solid #ccc;font-size:14px;padding:5px;'>
<form method='post' name='update_pass' action='newpass.php'>
<div style='padding:5px;'><strong>Nume utilizator</strong><br /><input type='text' name='user' value='".(isset($user) ? $user : "")."' style='width:100%;'/></div>
<div style='padding:5px;'><strong>Parolă veche</strong><br /><input type='password' name='old_pass' value='' style='width:100%;'/></div>
<div style='padding:5px;'><strong>Parolă nouă</strong><br /><input type='password' name='new_pass1' value='' style='width:100%;'/></div>
<div style='padding:5px;'><strong>Parolă nouă (verificare)</strong><br /><input type='password' name='new_pass2' value='' style='width:100%;'/></div>
<div style='text-align:center;'><input type='submit' name='update_pass_submit' value='Actualizează-mi contul'></div>
</form>
</div>
<div style='text-align:center;'>Dacă ți-ai acutalizat parola la noul sistem deja, <a href='conectare.php'>click aici</a> pentru conectare.<br />Dacă aveți probleme, vizitați <a href='http://ajutor.weskate.ro/conectare'>centrul de ajutor</a>.</div>";

require_once BASEDIR."scripts/footer.php";
?>
