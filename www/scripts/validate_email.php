<?php
if (!defined("inWeSkateCheck")) { die("Access respins"); }
/*
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
http://www.linuxjournal.com/article/9585
*/
function validEmail($email) {
	$atIndex = strrpos($email, "@");
	if (is_bool($atIndex) && !$atIndex) {
		return false;
	} else {
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64) {
			return false;
		} else if ($domainLen < 1 || $domainLen > 255) {
			return false;
		} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
			return false;
		} else if (preg_match('/\\.\\./', $local)) { 
			return false;
		} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) { 
			return false;
		} else if (preg_match('/\\.\\./', $domain)) {
			return false;
		} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))) {
			if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))) {
				return false;
			}
		}
		if (!(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
			return false;
		}
	}
	return true;
}
?>
