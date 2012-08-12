<?php
require_once "../mainfile.php";

if (isset($_GET['photo_id']) && isnum($_GET['photo_id'])) {
	$result = dbquery("SELECT photo_filename,photo_title FROM ".DB_PHOTOS." WHERE photo_id=".$_GET['photo_id']);
	if (dbrows($result)) {
		$data = dbarray($result);
		$title = "Vizualizare poză".($data['photo_title'] ? " : ".$data['photo_title'] : " (".$_GET['photo_id'].")");
		$content = "<a href='javascript:window.close();'><img src='http://img.weskate.ro/photoalbum/".$data['photo_filename']."' alt='poză mare' style='border:0px;' /></a>";
	} else {
		$title = "poză inexistentă";
		$content = "<script type='text/javascript'>window.close();</script>\n";
	}
} else {
	$content = "<script type='text/javascript'>window.close();</script>\n";
	$title = "poză inexistentă";
}


echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'>\n";
echo "<html>\n<head>\n";
echo "<title>$title</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n";
echo "</head>\n<body style='margin:0px;'>$content\n";
echo "</body>\n</html>\n";

?>
