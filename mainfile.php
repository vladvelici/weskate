<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('session.cookie_domain', '.weskate.ro');

session_start();
header("Content-Type: text/html; charset=UTF-8");

//WeSkate 5.1
//mainfile.php

if (stripos($_SERVER['PHP_SELF'],"mainfile.php")) { redirect("http://www.weskate.ro/"); }

ob_start();

//get confing
$path = ""; $i=0;
while (!file_exists($path."../config.php")) {
	$path .= "../"; $i++;
	if ($i>=3) { die("Nu pot găsi fișierul de configurație"); }
}
require_once $path."../config.php";
define("BASEDIR",$path);
define("IMAGES",BASEDIR."images/");
define("PANELS",BASEDIR."panels/");
define("SCRIPTS",BASEDIR."scripts/");
if (!isset($db_name)) { die("Fișierul de configurație este gol. Citiți readme pentru instalarea corectă."); }

//declaram inWeSkateCheck
define("inWeSkateCheck",true);


//scurtaturi baza de date
require_once "scripts/db_shortcuts.php";

// Functii pentru baza de date, imprumutate din PHP-Fusion.
function dbquery($query) {
	$result = @mysql_query($query);
	if (!$result) {
		echo mysql_error();
		return false;
	} else {
		return $result;
	}
}

function dbcount($field, $table, $conditions = "") {
	$cond = ($conditions ? " WHERE ".$conditions : "");
	$result = @mysql_query("SELECT Count".$field." FROM ".$table.$cond);
	if (!$result) {
		echo mysql_error();
		return false;
	} else {
		$rows = mysql_result($result, 0);
		return $rows;
	}
}

function dbrows($query) {
	$result = @mysql_num_rows($query);
	return $result;
}

function dbarray($query) {
	$result = @mysql_fetch_assoc($query);
	if (!$result) {
		echo mysql_error();
		return false;
	} else {
		return $result;
	}
}

//conectare la baza de date
$db_connect = @mysql_connect($db_host, $db_user, $db_pass);
$db_select = @mysql_select_db($db_name);

if (!$db_connect) {
	die("Nu mă pot conecta la MySQL.<br />".mysql_errno()." : ".mysql_error());
} elseif (!$db_select) {
	die("Nu pot selecta baza de date.<br />".mysql_errno()." : ".mysql_error());
}
unset($db_connect);
unset($db_select);
unset($db_host);
unset($db_user);
unset($db_pass);
unset($db_name);

//Setare UTF-8
dbquery("set NAMES utf8");

// Verificare IP-uri banate
$sub_ip1 = substr($_SERVER['REMOTE_ADDR'], 0, strlen($_SERVER['REMOTE_ADDR']) - strlen(strrchr($_SERVER['REMOTE_ADDR'], ".")));
$sub_ip2 = substr($sub_ip1, 0, strlen($sub_ip1) - strlen(strrchr($sub_ip1, ".")));

if (dbcount("(*)", DB_BLACKLIST, "(blacklist_ip='".$_SERVER['REMOTE_ADDR']."' OR blacklist_ip='$sub_ip1' OR blacklist_ip='$sub_ip2') AND (blacklist_expire=0 OR blacklist_expire<".time().")")) {
	$data = dbarray(dbquery("SELECT blacklist_expire,blacklist_why FROM ".DB_BLACKLIST." WHERE (blacklist_ip='".$_SERVER['REMOTE_ADDR']."' OR blacklist_ip='$sub_ip1' OR blacklist_ip='$sub_ip2') AND (blacklist_expire=0 OR blacklist_expire<".time().")"));
	redirect(BASEDIR."sorry/ban.php?e=".$data['blacklist_expire']."&w=".$data['blacklist_why']);
}

$getSettings = dbquery("SELECT * FROM ".DB_SETTINGS);
$setari = array();
while ($setting = dbarray($getSettings)) {
	$setari[$setting['setting_name']] = $setting['setting_value'];
}

define("PAGE_REQUEST", isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != "" ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME']);
define("PAGE_SELF", basename($_SERVER['PHP_SELF']));

if (!isset($_COOKIE["weskate_daily"])) {
	$azi = intval(time() / (60*60*24));
	$maine = $azi + (60*60*24);
	if (dbcount("(date)",DB_STATISTICS,"date=$azi")) {
		$result = dbquery("UPDATE ".DB_STATISTICS." SET counter=counter+1 WHERE date='".$azi."'");
	} else {
		$result = dbquery("INSERT INTO ".DB_STATISTICS." (counter,date) VALUES (1,$azi)");
	}
	setcookie("weskate_daily", "1", $maine, "/", ".weskate.ro");
}

function redirect($where) {
	header("Location: ".str_replace("&amp;", "&", $where));
	die();
}

function sqlsafe($text) {
	if (get_magic_quotes_gpc()) {
		$text = stripslashes($text);
	}
	return mysql_real_escape_string($text);
}

function htmlsafe($text) {
	$text=sqlsafe($text);
	$search = array("<", ">", "&nbsp;");
	$replace = array("&lt;", "&gt;", " ");
	return str_replace($search,$replace,$text);
}

function isnum($value) {
	if (!is_array($value)) {
		return (preg_match("/^[0-9]+$/", $value));
	} else {
		return false;
	}
}

function imageCheck($file) {
	$txt = file_get_contents($file);
	if (preg_match('#&(quot|lt|gt|nbsp|<?php);#i', $txt)) { return false; }
	elseif (preg_match("#&\#x([0-9a-f]+);#i", $txt)) { return false; }
	elseif (preg_match('#&\#([0-9]+);#i', $txt)) { return false; }
	elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $txt)) { return false; }
	elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $txt)) { return false; }
	elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $txt)) { return false; }
	elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $txt)) { return false; }
	elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $txt)) { return false; }
	elseif (preg_match("#</*(applet|link|style|script|iframe|frame|frameset)[^>]*>#i", $txt)) { return false; }
	return true;
}

function trimlink($text, $length) {
	if (strlen($text) > $length) $text = substr($text, 0, ($length-3))."...";
	return $text;
}

function firstitem($pag,$per_page) {
	return (($pag-1) * $per_page);
}

function pagenav($pag,$total,$per_page,$link=false,$disp=2,$after_pagno="") {
	if (!$link) $link = PAGE_REQUEST;
	$pages = ceil($total / $per_page);
	if ($pages <= 1) { return ""; }

	$left = max($pag-$disp, 1);
	$right = min($pag+$disp, $pages);
	$output = ($pag > 1 ? "<a href='".$link.($pag-1).$after_pagno."'>&lsaquo; precedenta</a>" : "");
	if ($left != 1) {
		$output .= "<a href='".$link."1".$after_pagno."'>1</a>";
		$output .= ($left-1 == 1 ? "" : "...");
	}
	for ($i=$left; $i<=$right; $i++) {
		$output .= ($i == $pag ? "<span>$i</span>" : "<a href='".$link.$i.$after_pagno."'>$i</a>");
	}
	if ($right < $pages) {
		$output .= ($right+1 == $pages ? "" : "...");
		$output .= "<a href='".$link.$pages.$after_pagno."'>$pages</a>";
	}
	$output .= ($pag < $pages ? "<a href='".$link.($pag+1).$after_pagno."'>următoarea &rsaquo;</a>" : "");
	return "<div class='pagenav'>\n".$output."</div>\n";
}

//check if the user is logged in
if (isset($_SESSION['user_id']) && isnum($_SESSION['user_id'])) {
	if (dbcount("(user_id)",DB_USERS,"user_id=".$_SESSION['user_id'])) {
		define("iMEMBER",true);
		$userdata = dbarray(dbquery("SELECT * FROM ".DB_USERS." WHERE user_id=".$_SESSION['user_id'].""));
		if ($userdata['user_level'] > 1) {
			define("iADMIN",true);
		} else {
			define("iADMIN",false);
		}
		if ($userdata['user_level'] > 2) {
			define("iSUPERADMIN",true);
		} else {
			define("iSUPERADMIN",false);
		}
	} else {
		define("iMEMBER",false);
		$userdata = "";
	}
} else if (isset($_COOKIE['wskusr'])) { //check for "remember me" cookie
	$login = sqlsafe($_COOKIE['wskusr']);
	$result = dbquery("SELECT user_id,user_cookie_exp FROM ".DB_USERS." WHERE user_cookie='$login' LIMIT 1");
	if (dbrows($result)) {
		$data = dbarray($result);
		if ($data['user_cookie_exp'] <= time() && PAGE_REQUEST!="/membri/conectare.php") {
			redirect("/membri/conectare.php");
			define("iMEMBER",false);
			define("iADMIN",false);
			define("iSUPERADMIN",false);
			$userdata = "";
		} else {
			$_SESSION['user_id'] = $data['user_id'];
			$_SESSION['user_key'] = getRandomString(64);
			define("iMEMBER",true);
			$userdata = dbarray(dbquery("SELECT * FROM ".DB_USERS." WHERE user_id=".$_SESSION['user_id']));
			if ($userdata['user_level'] > 1) {
				define("iADMIN",true);
			} else {
				define("iADMIN",false);
			}
			if ($userdata['user_level'] > 2) {
				define("iSUPERADMIN",true);
			} else {
				define("iSUPERADMIN",false);
			}
		}
	} else {
		define("iMEMBER",false);
		define("iADMIN",false);
		define("iSUPERADMIN",false);
		$userdata = "";
	}
} else {
	define("iMEMBER",false);
	define("iADMIN",false);
	define("iSUPERADMIN",false);
	$userdata = "";
}

// Javascript email encoder by Tyler Akins
// http://rumkin.com/tools/mailto_encoder/
function hide_email($email, $title = "", $subject = "") {
	if (strpos($email, "@")) {
		$parts = explode("@", $email);
		$MailLink = "<a href='mailto:".$parts[0]."@".$parts[1];
		if ($subject != "") { $MailLink .= "?subject=".urlencode($subject); }
		$MailLink .= "'>".($title?$title:$parts[0]."@".$parts[1])."</a>";
		$MailLetters = "";
		for ($i = 0; $i < strlen($MailLink); $i++) {
			$l = substr($MailLink, $i, 1);
			if (strpos($MailLetters, $l) === false) {
				$p = rand(0, strlen($MailLetters));
				$MailLetters = substr($MailLetters, 0, $p).$l.substr($MailLetters, $p, strlen($MailLetters));
			}
		}
		$MailLettersEnc = str_replace("\\", "\\\\", $MailLetters);
		$MailLettersEnc = str_replace("\"", "\\\"", $MailLettersEnc);
		$MailIndexes = "";
		for ($i = 0; $i < strlen($MailLink); $i ++) {
			$index = strpos($MailLetters, substr($MailLink, $i, 1));
			$index += 48;
			$MailIndexes .= chr($index);
		}
		$MailIndexes = str_replace("\\", "\\\\", $MailIndexes);
		$MailIndexes = str_replace("\"", "\\\"", $MailIndexes);
		
		$res = "<script type='text/javascript'>";
		$res .= "ML=\"".str_replace("<", "xxxx", $MailLettersEnc)."\";";
		$res .= "MI=\"".str_replace("<", "xxxx", $MailIndexes)."\";";
		$res .= "ML=ML.replace(/xxxx/g, '<');";
		$res .= "MI=MI.replace(/xxxx/g, '<');";	$res .= "OT=\"\";";
		$res .= "for(j=0;j < MI.length;j++){";
		$res .= "OT+=ML.charAt(MI.charCodeAt(j)-48);";
		$res .= "}document.write(OT);";
		$res .= "</script>";
	
		return $res;
	} else {
		return $email;
	}
}

//date and time
function showdate($format, $val) {
	global $setari;
	$hover=false;

	if ($format == "ago") {
		return timeago($val);
	}

	if (strpos($format,"datehover") !== false) {
		$hover = 1;
	} else if (strpos($format,"agohover") !== false) {
		$hover = 2;
	}

	$format = trim(str_replace(array("datehover","agohover"),"",$format));

	if ($format == "shortdate" || $format == "longdate" || $format == "forumdate") {
		$t = strftime($setari[$format], $val);
	} else {
		$t = strftime($format, $val);
	}
	$eng = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
	$ro = array("Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie");
	$date = str_replace($eng,$ro,$t);
	if ($hover) { 
		$ago = timeago($val);
		return "<div class='agodate'>".($hover==1 ? $ago : $date)."<span>(".($hover==1 ? $date : $ago).")</span></div>";
	} else {
		return $date;
	}
}

function timeago($datestamp) {
	$interval = time() - $datestamp;
	if ($interval < 0) return false;
	$min = 60; $h=60*$min; $zi=24*$h; $sapt=7*$zi; $luna=30*$zi; $an=365*$zi;
	if ($interval >= $an) {
		$nr = round($interval/$an);
		return "acum ".($nr == 1 ? "un an" : "$nr ani");
	} else if ($interval >= $luna) {
		$nr = round($interval/$luna);
		return "acum ".($nr == 1 ? "o lună" : "$nr luni");
	} else if ($interval >= $sapt) {
		$nr = round($interval/$sapt);
		return "acum ".($nr == 1 ? "o săptămâna" : "$nr săptămâni");
	} else if ($interval >= $zi) {
		$nr = round($interval/$zi);
		return ($nr == 1 ? "ieri" : "acum $nr zile");
	} else if ($interval >= $h) {
		$nr = round($interval/$h);
		return "acum ".($nr == 1 ? "o oră" : "$nr ore");
	} else if ($interval >= $min) {
		$nr = round($interval/$min);
		return "acum ".($nr == 1 ? "un minut" : "$nr minute");
	} else {
		return "acum ".($interval == 1 ? "o secundă" : "$interval secunde");
	}
}

function getRandomString($len,$chars="ABCDEFGHIJKLMOPQRSTUVWXYZabcdefghijklmopqrstuvwxyz0123456789") {
	$max = strlen($chars)-1; $out = "";
	for ($i=1;$i<=$len;$i++) {
		$out .= $chars[mt_rand(0,$max)];
	}
	return $out;
}

// Translate bytes into kb, mb, gb or tb by CrappoMan
function parsebytesize($size, $digits = 2) {
	$kb = 1024; $mb = 1024 * $kb; $gb= 1024 * $mb; $tb = 1024 * $gb;
	if ($size == 0) { return "Empty"; }
	elseif ($size < $kb) { return $size."Bytes"; }
	elseif ($size < $mb) { return round($size / $kb,$digits)."Kb"; }
	elseif ($size < $gb) { return round($size / $mb,$digits)."Mb"; }
	elseif ($size < $tb) { return round($size / $gb,$digits)."Gb"; }
	else { return round($size / $tb, $digits)."Tb"; }
}

function parsebb($str) {
	if (!defined("BB_LIB_LOADED")) {
		define("BB_LIB_LOADED",true);
		require_once SCRIPTS."nbbc/nbbc.php";
	}
	$search = array("&lt;", "&gt;", "&#39;", '&quot;');
	$replace = array("<", ">", "'", '"');
	$str = str_replace($search,$replace,$str);
	$bb = new BBCode;
	return $bb->parse($str);
}

function urltext($link) {
	$link = strtolower($link);
	//diacritice:
	$link = killRoChars($link);
	//caractere speciale1 :
	$search = array("ä", "Ä", "ü", "Ü", "ö", "Ö", "ß", ".", " ","--",
			">","<","'","\"",":",",","[","]","{","}","?","&lt;","&gt;");
	$replace = array("ae", "ae", "ue", "ue", "oe", "oe", "ss", "", "-","",
			"","","","","","","","","","","","","");
	$link = str_replace($search,$replace,$link);
	//caractere speciale2 :
	$search2=array('&quot;', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '{', '}', '|', ':', '"', '<', '>', '?', '[', ']', ';', "'", '.', '_', '/', '*', '+', '~', '`', '=', ' ', '---', '--', '--', ',,,', ',,', ',,');
	$replace2=array('', '', '', '', '', '', '-n-', ',', '', '', '', ',', '', '', '', '', '', '', '', '', '', '', ',', ',', ',', ',', '', '', ',', '', ',', ',', ',', ',', ',', ',', ',', ',', ',');
	$link = str_replace($search2, $replace2, $link); 
	return $link;
}

function fixRoChars($text) {
	$wrong = array("&amp;#537;","&amp;#539;","&amp;#536;","&amp;#538;");
	$wrong2 = array("&#537;","&#539;","&#536;","&#538;");
	$ok = array("&#351;","&#355;","&#350;","&#354;");
	$text = str_replace($wrong,$ok,$text);
	$text = str_replace($wrong2,$ok,$text);
	return $text;
}
function killRoChars($text) {
	$wrong = array("ă","Ă","â","Â","î","Î","ș","Ș","ț","Ț","ş","Ş","ţ","Ţ");
	$ok = array("a","A","a","A","i","I","s","S","t","T","s","S","t","T");
	$text = str_replace($wrong,$ok,$text);
	return $text;
}
function keywordize($text) {
	$search=array('&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']',';',"'",'.','_','/','*','+','~','`','=',' ','---','--','--',',,,',',,',',,');
	$replace=array('','','','','','','',',','','','',',','','','','','','','','','','',',',',',',',',','','',',','',',',',',',',',',',',',',',',',',',');
	$text = strtolower(str_replace($search,$replace,fixRoChars($text)));
	return $text;
}
function forumlink($tip,$id) {
	if ($tip == "f" && isnum($id)) {
		$result=dbquery("SELECT forum_name FROM ".DB_FORUMS." WHERE forum_id='$id'");
		$data = dbarray($result);
		return "forum/".urltext($data['forum_name']).".f$id";
	} elseif ($tip == "th" && isnum($id)) {
		$result = dbquery("SELECT f.forum_name,f.forum_id,th.thread_subject FROM ".DB_THREADS." th
				LEFT JOIN ".DB_FORUMS." f ON th.forum_id=f.forum_id
				WHERE th.thread_id='$id'");
		$data = dbarray($result);
		return "forum/".urltext($data['forum_name']).".f".$data['forum_id']."/".urltext($data['thread_subject']).".d$id";
	} else {
		return false;
	}
}

function indexItem($id,$type,$title,$text,$keywords,$local,$visibility,$datestamp,$url) {
	$exista = dbcount("(search_id)",DB_SEARCH,"search_type='$type' AND search_item='$id'");
	$title = killRoChars($title);
	$text = killRoChars(strip_tags($text));
	$keywords = keywordize($text);
	if ($exista) {
		$result = dbquery("UPDATE ".DB_SEARCH." SET search_title='$title', search_text='$text', search_keywords='$keywords', search_local='$local', search_visibility='$visibility', search_url='$url'".($datestamp ? ", search_datestamp='$datestamp'" : "")." WHERE search_type='$type' AND search_item='$id'");
	} else {
		$result = dbquery("INSERT INTO ".DB_SEARCH."
		(search_item, search_type, search_title, search_text, search_keywords, search_local, search_visibility, search_datestamp, search_url)
		VALUES
		('$id', '$type', '$title', '$text', '$keywords', '$local', '$visibility', '$datestamp', '$url')");
	}
	return ($result ? true : false);
}
function deleteIndex($type,$id) {
	$result = dbquery("DELETE FROM ".DB_SEARCH." WHERE search_type='$type' AND search_item='$id'");
	return ($result ? true : false);
}

function checkMyRight($right) {
	if (!iMEMBER) return false;
	global $userdata;
	$rights = explode(".", $userdata['user_rights']);
	if (is_array($rights)) {
		if (in_array($right,$rights)) {
			return true;
		} else {
			return false;
		}
	} else if ($userdata['user_rights'] == $right) {
		return true;
	} else {
		return false;
	}
}

function checkMyAccess($type,$id) {
	if (!iMEMBER) return false;
	if (checkMyRight($type)) { return true; }
	global $userdata;
	if ($type == "A") {
		$access = dbcount("(article_id)",DB_ARTICLES,"article_id='$id' AND article_name='".$userdata['user_id']."'");
		if ($access) { return true; } else { return false; }
	} elseif ($type == "N") {
		$access = dbcount("(news_id)",DB_NEWS,"news_id='$id' AND news_name='".$userdata['user_id']."'");
		if ($access) { return true; } else { return false; }
	} elseif ($type == "S") {
		$access = dbcount("(spot_id)",DB_SPOT_ALBUMS,"spot_id='$id' AND spot_user='".$userdata['user_id']."'");
		if ($access) { return true; } else { return false; };
	} elseif ($type == "L") {
		$access = dbcount("(photo_id)",DB_SPOT_PHOTOS,"photo_id='$id' AND photo_user='".$userdata['user_id']."'");
		if ($access) { return true; } else { return false; };
	} elseif ($type == "V") {
		$access = dbcount("(video_id)",DB_VIDEOS,"video_id='$id' AND video_owner='".$userdata['user_id']."'");
		if ($access) { return true; } else { return false; };
	} else {
		return false;
	}
}

function cleanup($type,$item) {
	if (checkMyAccess($type,$item)) {
		$result = dbquery("DELETE FROM ".DB_COMMENTS." WHERE comment_type='$type' AND comment_item_id='$item'");
		$result = dbquery("DELETE FROM ".DB_RATINGS." WHERE rating_type='$type' AND rating_item_id='$item'");
		$result = dbquery("DELETE FROM ".DB_FAVORITE." WHERE item_id='$id' AND fav_type='$type'");
		deleteIndex($type,$item);
		return true;
	} else {
		return false;
	}
}

function getImageByType($type) {
	if ($type == "N") {
		return "stiri.png";
	} elseif ($type == "A") {
		return "articole.png";
	} elseif ($type == "C") {
		return "photoalbum.png";
	} elseif ($type == "P" || $type == "M") {
		return "poze.png";
	} elseif ($type == "L" || $type == "S") {
		return "spots.png"; // to do
	} elseif ($type == "B") {
		return "blog.png";
	} elseif ($type == "O" || $type == "J") {
		return "cityview.png";
	} elseif ($type == "F") {
		return "forum.png";
	} elseif ($type == "U") {
		return "friend.png";
	} elseif ($type == "V") {
		return "video.png";
	} else { return false; }
}

function getRelatedList($search,$limit=5,$type=false,$id=false) {
	$result = dbquery("SELECT search_title,search_url,search_type FROM ".DB_SEARCH." WHERE MATCH (search_text,search_keywords,search_title) AGAINST ('$search')".($type && $id ? " AND (search_type!='$type' OR search_item!='$id')" : "")." LIMIT 0,$limit");
	if (dbrows($result)>2) {
		return $result;
	} else {
		$result = dbquery("SELECT search_title,search_url,search_type FROM ".DB_SEARCH." WHERE MATCH (search_text,search_keywords,search_title) AGAINST ('$search' WITH QUERY EXPANSION)".($type && $id ? " AND (search_type!='$type' OR search_item!='$id')" : "")." LIMIT 0,$limit");
		if (dbrows($result)) {
			return $result;
		} else {
			return false;
		}
	}
}

function passwordStrengh($pass) {
	$safe_chars = 14; // numarul de caractere recomandat pt parola
	$t=0;
	if(preg_match('/[a-z]/', $pass)) {
		$t++;
	}
	if(preg_match('/[A-Z]/', $pass)) {
		$t++;
	}
	if(preg_match('/[0-9]/', $pass)) {
		$t++;
	}
	if(preg_match('/[^a-zA-Z0-9]/', $pass)) {
		$t++;
	}
	$len = strlen($pass);
	if ($len > $safe_chars) {
		$percent = 25;
	} else {
		$percent = round((25*$len)/$safe_chars);
	}
	return $percent * $t;
}

function showAvatar($avatar,$email,$yahoo) {
	if ($avatar=="gravatar") {
		return "<img src='http://gravatar.com/avatar/".md5(strtolower(trim($email)))."' alt='avatar'/>";
	} elseif ($avatar=="yahoo" && $yahoo) {
		return "<img src='http://img.msg.yahoo.com/avatar.php?yids=".$yahoo."' alt='avatar'/>";
	} elseif (file_exists(BASEDIR."images/avatars/".$avatar)) {
		return "<img src='http://img.weskate.ro/avatars/$avatar' alt='avatar'/>";
	} else { //nothing found, forcing gravatar (maybe it's default image)
		return "<img src='http://gravatar.com/avatar/".md5(strtolower(trim($email)))."' alt='avatar'/>";
	}
}

if (isset($_SESSION['logged_out'])) { define("logoutMSG",true); unset($_SESSION['logged_out']); } else { define("logoutMSG",false); }

?>
