<?php

/* REQUIRED VARS :
int $forum_id
int $items_per_page
int $page
string $forum_name
*/

$threads = dbcount("(thread_id)",DB_THREADS,"forum_id=".$forum_id);
$result = dbquery("SELECT t.*,a.user_name AS author_name,a.user_profileurl AS author_url,l.user_name AS last_name,l.user_profileurl AS last_url FROM ".DB_THREADS." t 
		LEFT JOIN ".DB_USERS." a ON t.thread_author=a.user_id
		LEFT JOIN ".DB_USERS." l ON t.thread_lastuser=l.user_id
		WHERE forum_id=".$forum_id."
		ORDER BY thread_lastpost DESC
		LIMIT ".firstitem($page,$items_per_page).",$items_per_page");
if (iMEMBER) {
	echo "<a href='javascript:newThread();' class='flright header-link-m forum smallround' style='display:inline-block;padding:3px 3px 3px 21px;background-image:url(http://img.weskate.ro/new.png);background-position:2px 50%;background-repeat:no-repeat;'>Discuție nouă</a>";
}
echo pagenav($page,$threads,$items_per_page,"javascript:forumspage(",2,",$forum_id);");

echo "<div style='clear:both;' class='spacer'></div>";
echo "<table cellpadding='4' cellspacing='1' width='100%' class='round spacer' style='border:2px solid #43a74F;'>";
echo "<tr class='f-table-head'><td style='width:1%;white-space:nowrap;'></td><td><div style='font-size:14px;font-weight:bold;'>".$forum_name."</div></td><td style='width:1%;white-space:nowrap;text-align:center;'>vizualizări</td><td style='width:1%;white-space:nowrap;text-align:center;'>autor</td><td style='width:1%;white-space:nowrap;text-align:center;'>răspunsuri</td><td style='width:1%;white-space:nowrap;text-align:center;'>ultima postare</td></tr>";
$class='f-dark';
while ($data=dbarray($result)) {
	echo "<tr class='forumtd lightonhover $class'>";
	if (iMEMBER) {
		if ($userdata['user_lastvisit'] < $data['thread_lastpost']) {
			echo "<td style='width:1%;white-space:nowrap;'><img src='http://img.weskate.ro/newposts.png' alt='sunt postări noi de la ultima vizită' /></td>";
		} else {
			echo "<td style='width:1%;white-space:nowrap;'><img src='http://img.weskate.ro/nonewposts.png' alt='fără postări noi de la ultima vizită' /></td>";
		}
	} else {
		echo "<td style='width:1%;white-space:nowrap;'><img src='http://img.weskate.ro/nonewposts.png' alt='fără postări noi de la ultima vizită' /></td>";
	}
	echo "<td><a href='/forum/".urltext($forum_name).".f".$data['forum_id']."/".urltext($data['thread_subject']).".d".$data['thread_id']."'>".$data['thread_subject']."</a></td>";
	echo "<td style='width:1%;white-space:nowrap;text-align:center;'>".$data['thread_views']."</td>";
	echo "<td style='width:1%;white-space:nowrap;text-align:center;'><a href='http://profil.weskate.ro/".$data['author_url']."' style='font-size:12px;'>".$data['author_name']."</a></td>";
	echo "<td style='width:1%;white-space:nowrap;text-align:center;'>".$data['thread_postcount']."</td>";
	echo "<td style='width:1%;white-space:nowrap;text-align:center;'><a href='http://profil.weskate.ro/".$data['last_url']."' style='font-size:12px;'>".$data['last_name']."</a><br />".showdate("ago",$data['thread_lastpost'])."</td>";
	echo "</tr>";
	$class = ($class=="f-light" ? "f-dark" : "f-light");
}
echo "<tr class='$class'><td colspan='6' style='padding-top:3px;'></td></tr>";
echo "</table>";

if (iMEMBER) {
	echo "<a href='javascript:newThread();' class='flright header-link-m forum smallround' style='display:inline-block;padding:3px 3px 3px 21px;background-image:url(http://img.weskate.ro/new.png);background-position:2px 50%;background-repeat:no-repeat;'>Discuție nouă</a>";
}
echo pagenav($page,$threads,$items_per_page,"javascript:forumspage(",2,",$forum_id);");

echo "<div style='clear:both;'></div>";
?>
