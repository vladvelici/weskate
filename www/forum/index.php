<?php
require_once "../mainfile.php";
$CuloarePagina = "verde";
require_once SCRIPTS."header.php";

set_title("WeSkate Forum");
add_to_head("<link rel='stylesheet' href='http://weskate.ro/forum/forum.css' type='text/css' media='screen' />");

echo "<span class='capmain'>WeSkate Forum</span>";

echo "<div style='border-top: 1px dotted #555;border-bottom:1px dotted #555;padding:10px 5px 10px 5px;'>";
echo "<div style='margin-left:10px;'><img src='http://img.weskate.ro/bullet_green.png' alt='green bullet' align='left' /><a href='/forum/'>Forum Home</a></div>";
echo "</div>";


echo "<div class='spacer'></div>";
$result = dbquery("SELECT forum_name,forum_id FROM ".DB_FORUMS." WHERE forum_cat=0 ORDER BY forum_order");
while ($data=dbarray($result)) {
	$result2 = dbquery("SELECT f.forum_name, f.forum_description, f.forum_threadcount, f.forum_id, f.forum_lastpost, f.forum_postcount, f.forum_lastuser, u.user_name, u.user_profileurl FROM ".DB_FORUMS." f
	LEFT JOIN ".DB_USERS." u ON u.user_id=f.forum_lastuser
	WHERE forum_cat=".$data['forum_id']);
	if (dbrows($result2)) {
		echo "<table cellpadding='4' cellspacing='1' width='100%' class='round spacer' style='border:2px solid #43a74F;'>";
		echo "<tr class='f-table-head'><td></td><td><div style='font-size:14px;font-weight:bold;'>".$data['forum_name']."</div></td><td style='text-align:center;'>discuții</td><td style='text-align:center;'>postări</td><td style='text-align:center;'>ultima postare</td></tr>";
		$class='f-dark';
		while ($data2=dbarray($result2)) {
			echo "<tr class='forumtd lightonhover $class'>";
			if (iMEMBER) {
				if ($userdata['user_lastvisit'] < $data2['forum_lastpost']) {
					echo "<td><img src='http://img.weskate.ro/newposts.png' alt='sunt postări noi de la ultima vizită' /></td>";
				} else {
					echo "<td><img src='http://img.weskate.ro/nonewposts.png' alt='fără postări noi de la ultima vizită' /></td>";
				}
			} else {
				echo "<td><img src='http://img.weskate.ro/nonewposts.png' alt='fără postări noi de la ultima vizită' /></td>";
			}
			echo "<td><a href='/forum/".urltext($data2['forum_name']).".f".$data2['forum_id']."'>".$data2['forum_name']."</a>";
			echo "<br />".$data2['forum_description']."</td>";
			echo "<td style='text-align:center;'>".$data2['forum_threadcount']."</td>";
			echo "<td style='text-align:center;'>".$data2['forum_postcount']."</td>";
			echo "<td style='text-align:center;'><a href='http://profil.weskate.ro/".$data2['user_profileurl']."' style='font-size:12px;'>".$data2['user_name']."</a><br />".showdate("ago",$data2['forum_lastpost'])."</td>";
			echo "</tr>";
			$class = ($class=="f-light" ? "f-dark" : "f-light");
		}
		echo "<tr class='$class'><td colspan='3' style='padding-top:3px;'></td></tr>";
		echo "</table>";
	}
}	

require_once SCRIPTS."footer.php";
?>
