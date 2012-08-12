<?php
require_once "../mainfile.php";
require_once SCRIPTS."header.php";

if (!iADMIN || !isset($_GET['key']) || $_GET['key'] != $_SESSION['user_key']) redirect(BASEDIR."index.php");

opentable("Panou de administrare WeSkate 5.1");
$key = "?key=".$_SESSION['user_key'];
if (iSUPERADMIN) {
	echo "<div style='font-size:14px;font-weight:bold;'>SUPER ADMIN</div>";
	echo "<a href='site_settings.php$key'>Setări principale</a><br />";
	echo "<hr />";
}
echo "<a href='statistics.php$key'>Statistici despre vizitele zilnice</a><br />";
require_once SCRIPTS."footer.php";
?>
