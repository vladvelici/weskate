<?php
require_once "../mainfile.php";
if (iMEMBER) redirect("../index.php");
require_once SCRIPTS."header.php";
require_once SCRIPTS."recaptchalib.php";
require_once SCRIPTS."validate_email.php";
opentable("Înregistrare");

add_to_head("<link rel='stylesheet' href='http://weskate.ro/membri/register.css' type='text/css' media='screen' />");
add_to_head("<script type='text/javascript' src='http://weskate.ro/membri/register.js'></script>");

$recaptcha_error=false;
$userErr = false;
$pass2Err = false;
$passErr = false;
$emailErr = false;
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordCheck']) && isset($_POST['email'])) {
	$error = false;
	if (isset($_POST['recaptcha_challenge_field']) && isset($_POST['recaptcha_response_field'])) {
		$recaptcha = recaptcha_check_answer($recaptcha_private,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if (!$recaptcha->is_valid) {
			$error=true;
			$recaptcha_error=$recaptcha->error;
		}
	} else {
		$error = true;
	}
	$user = htmlsafe($_POST['username']);
	$pass1 = $_POST['password'];
	$pass2 = $_POST['passwordCheck'];
	$email = $_POST['email'];
	if (strlen($user) < 3) {
		$userErr = "Prea scurt!";
		$error = true;
	} elseif (strlen($user) > 200) {
		$userErr = "Prea lung!";
	} else {
		if (dbcount("(*)",DB_USERS,"user_name='$user'") || dbcount("(*)",DB_NEW_USERS,"user_name='$user'")) { 
			$userErr = "Deja folosit. Încearcă altul.";
			$error = true;
		}
	}
	if ($pass1!=$pass2) {
		$error = true;
		$pass2Err = "Parolele nu coincid.";
	} else {
		if (passwordStrengh($pass1)<26) {
			$error = true;
			$passErr = "Parolă prea slabă";
		}
	}
	if (!validEmail($email)) {
		$error = true;
		$emailErr = "E-mail invalid.";
	} else {
		$email = sqlsafe($email);
		if (dbcount("(*)",DB_USERS,"user_email='$email'") || dbcount("(*)",DB_NEW_USERS,"user_email='$email'")) { 
			$error=true;
			$emailErr = "E-mail deja folosit.";
		}		
	}
	if (isset($_POST['terms']) && $_POST['terms']) {
		$terms = true;
	} else {
		echo "<div class='noteyellow'>Nu te poți înregistra pe site dacă nu accepți termenii și condițiile de utilizare!</div>";
		$terms = false; $error=true;
	}
	if (isset($_POST['hide_mail']) && $_POST['hide_mail']) {
		$hide_mail = true;
	} else {
		$hide_mail = false;
	}
	if (!$error) {
		$code = getRandomString(64);
		while (dbcount("(*)",DB_NEW_USERS,"user_code='$code' LIMIT 1")) {
			$code = getRandomString(64);
		}
		$time = time();
		$hash_password = hash("sha512",$pass1.$time);
		$result = dbquery("INSERT INTO ".DB_NEW_USERS."
			(user_code,user_email,user_password,user_name,user_hide_email,user_datestamp) VALUES
			('$code','$email','$hash_password','$user','".($hide_mail ? 1 : 0)."','$time')");
		$mail_headers = "Content-type: text/html; charset=UTF-8\r\nReply-To: contact@weskate.ro";
		$mail_txt = "<img src='http://img.weskate.ro/logo.png' /><br /><br /><h2>Bună, $user!</h2>Primești acest e-mail pentru că tocmai te-ai înregistrat pe <a href='http://www.weskate.ro/'>WeSkate</a>. Bun venit!<br /><br />Pentru a-ți activa contul, deschide următorul link: <br /><a href='http://weskate.ro/membri/activate.php?account=$code'>http://weskate.ro/membri/activate.php?account=$code</a><br /><br />Dacă nu merge, încearcă să copiezi adresa și să o lipești în browser sau accesează <a href='http://weskate.ro/membri/activate.php'>http://weskate.ro/membri/activate.php</a> și scrie codul de activare manual. <br /><br />Codul tău de activare este: <strong>$code</strong>.<br /><br /><br />Dacă nu ești tu cel care s-a înscris cu adresa ta de e-mail, nu activa contul. Codul expiră în 24 de ore de când a fost generat (".showdate("longdate",time()).").<br /><br />Echipa <a href='http://weskate.ro/'>WeSkate.Ro</a>";
		mail($email,"Inregistrare pe WeSkate",$mail_txt,$mail_headers);
		echo "<div class='notegreen'>Primul pas din înregistrare este complet!</div>";
		echo "<div style='text-align:center;font-size:16px;font-weight:bold;'>Vei primi un e-mail cu un cod de activare și niște instrucțiuni pentru terminarea înregistrării.<br /><br /><span style='font-size:14px;'>Verifică și directoarele <em>SPAM</em> și <em>BULK</em> pentru că unele servicii de e-mail filtrează mesajele noastre ca spam.</span></div>";
	}
}
if (!isset($error) || (isset($error) && $error!=false)) {

echo "<form method='post' action='inregistrare.php'>";
echo "<div style='width:500px;margin:5px auto 5px auto;position:relative;'>";
echo "<div class='reg_field' id='username_div' onclick='setfocus(\"username\");'>
<div>".($userErr ? "Eroare: ".$userErr : "Numele pe care îl vei avea în cadrul site-ului WeSkate, cu care te vei și conecta.<br /> Este sensibil la litere MARI și mici.")."</div>
<span><span id='username_status'></span>".($userErr ? "<span style='color:#f00;float:left;'>" : "")."Nume de utilizator".($userErr ? "</span>" : "")."</span>
<input type='text' name='username' value='".(isset($user) ? $user : "")."' id='username' onblur='usernameCheck(this.value);refreshform();' onfocus='setclass(\"username\");'/>
</div>";

echo "<div class='reg_field' id='password_div' onclick='setfocus(\"password\");'>
<div>".($passErr ? "Eroare: ".$passErr : "Folosește litere mari (ABC), mici (abc), simboluri (@#$), numere (123) și spații astfel să creezi o combinație unică pe care o vei putea reține.")."</div>
<span><span id='password_status'></span>".($passErr ? "<span style='color:#f00;float:left;'>" : "")."Parolă".($passErr ? "</span>" : "")."</span>
<input type='password' name='password' id='password' onkeyup='passCheck(this.value);' onchange='passCheck(this.value);' onblur='passCheck(this.value);refreshform();'  onfocus='setclass(\"password\");'/>
</div>";

echo "<div class='reg_field' id='passwordCheck_div' onclick='setfocus(\"passwordCheck\");'>
<div>".($pass2Err ? "Eroare: ".$pass2Err : "Doar ca să ne asigurăm că ai scris bine parola mai sus.")."</div>
<span><span id='passwordCheck_status'></span>".($pass2Err ? "<span style='color:#f00;float:left;'>" : "")."Verificare parolă".($pass2Err ? "</span>" : "")."</span>
<input type='password' name='passwordCheck' id='passwordCheck' onkeyup='pass2();' onblur='pass2();refreshform();'  onfocus='setclass(\"passwordCheck\");'/>
</div>";

echo "<div class='reg_field' id='email_div' onclick='setfocus(\"email\");'>
<div>".($emailErr ? "Eroare: ".$emailErr : "Vei primi un e-mail de verificare.")."</div>
<span><span id='email_status'></span>".($emailErr ? "<span style='color:#f00;float:left;'>" : "")."Adresă de e-mail".($emailErr ? "</span>" : "")."</span>
<input type='text' name='email' id='email' onkeyup='validateEmail(this.value)' onblur='validateEmail(this.value);refreshform();'  onfocus='setclass(\"email\");'/><br />
<label><input type='checkbox' checked='checked' name='hide_mail' id='hide_mail' />Ascunde-mi e-mail-ul.</label><br />
</div>";

echo "<script type='text/javascript'>
	var RecaptchaOptions = {
	        custom_translations : {
	                instructions_visual : \"Scire codul din imagine\",
	                instructions_audio : \"Scrie cuvintele pe care le auzi\",
	                play_again : \"Mai pornește odată\",
	                cant_hear_this : \"Descarcă în format MP3\",
	                visual_challenge : \"Vizual\",
	                audio_challenge : \"Auditiv\",
	                refresh_btn : \"Încearcă altul\",
	                help_btn : \"Ajutor\",
	                incorrect_try_again : \"Ai greșit! Mai încearcă\",
	        },
	        lang : 'en',
	        theme : 'white',
	};
</script>";

echo recaptcha_get_html($recaptcha_public,$recaptcha_error);

echo "<div class='reg_field' id='terms_div'>
<label><input type='checkbox' name='terms' id='terms' />Accept <a href='http://ajutor.weskate.ro/termeni' target='_blank'>termenii și condițiile</a> de utilizare a site-ului.</label>
</div>";

echo "<div class='reg_field'><input type='submit' value='Înregistrează-mă' style='width:100%;text-align:center;' /></div>";
echo "</div>";
echo "</form>";

}
require_once SCRIPTS."footer.php";
?>
