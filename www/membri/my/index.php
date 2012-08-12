<?php
require_once "../../mainfile.php";
require_once SCRIPTS."header.php";
if (!iMEMBER) { redirect("../conectare.php?redirto=".urlencode(PAGE_REQUEST)); }
$key = "?key=".$_SESSION['user_key'];
add_to_head("<link rel='stylesheet' href='http://weskate.ro/membri/my/my.css' type='text/css' media='screen' />");
echo "<table cellpadding='4' cellspacing='0' width='100%' style='border-bottom:2px solid #999;' class='spacer'><tr>
<td><span class='capmain_color' style='font-weight:bold;font-size:40px;'>My</span><span class='capmain_color' style='font-size:30px;padding-bottom:7px;'>WeSkate</span></td>
<td align='right' class='my-navigationtd'>
<span class='my-albastru'>tot</span> <strong>-</strong> 
<a href='stiri.php$key' class='my-oranj'>&#351;tiri</a> <strong>-</strong> 
<a href='articole.php$key' class='my-galben'>articole</a> <strong>-</strong> 
<a href='spoturi.php$key' class='my-mov'>locuri de skate</a> <strong>-</strong> 
<a href='video.php$key' class='my-rosu'>video</a></td>
</tr></table>";

$ciorneStiri = dbcount("(news_id)",DB_NEWS,"news_name='".$userdata['user_id']."' AND news_draft='1'");
$ciorneArticole = dbcount("(article_id)",DB_ARTICLES,"article_name='".$userdata['user_id']."' AND article_draft='1'");

$ciorne = $ciorneStiri || $ciorneArticole ? true : false;

$stiri = dbcount("(news_id)",DB_NEWS,"news_draft=0 AND news_name=".$userdata['user_id']);
$stiri_all = dbcount("(news_id)",DB_NEWS,"news_draft=0");

$articole = dbcount("(article_id)",DB_ARTICLES,"article_draft=0 AND article_name=".$userdata['user_id']);
$articole_all = dbcount("(article_id)",DB_ARTICLES,"article_draft=0");

$albume_foto = dbcount("(album_id)",DB_PHOTO_ALBUMS,"album_user=".$userdata['user_id']);
$albume_foto_all = dbcount("(album_id)",DB_PHOTO_ALBUMS);

$foto = dbcount("(photo_id)",DB_PHOTOS,"photo_user=".$userdata['user_id']);
$foto_all = dbcount("(photo_id)",DB_PHOTOS);

$spot = dbcount("(spot_id)",DB_SPOT_ALBUMS,"spot_user=".$userdata['user_id']);
$spot_all = dbcount("(spot_id)",DB_SPOT_ALBUMS);

$spot_photos = dbcount("(photo_id)",DB_SPOT_PHOTOS,"photo_user=".$userdata['user_id']);
$spot_photos_all = dbcount("(photo_id)",DB_SPOT_PHOTOS);

$posts = dbcount("(post_id)",DB_POSTS,"post_author=".$userdata['user_id']);
$posts_all = dbcount("(post_id)",DB_POSTS);

$comments = dbcount("(comment_id)",DB_COMMENTS,"comment_name=".$userdata['user_id']);
$comments_all = dbcount("(comment_id)",DB_COMMENTS);

$all = $stiri_all + $articole_all + $albume_foto_all + $foto_all + $spot_all + $spot_photos_all + $posts_all + $comments_all;
$my = $stiri + $articole + $albume_foto + $foto + $spot + $spot_photos + $posts + $comments;
$percent = ($all ? round($my*100/$all,2) : 0);

if ($ciorne) { add_to_head("<script type='text/javascript' src='my.js'></script>"); }

echo "<table cellpadding='0' cellspacing='0' width='100%'><tr valign='top'>";
echo "<td class='my-imagetd smallround'><br />";

echo "<span>WeSkate este al t&#259;u. Tu &icirc;l scrii.</span>";
echo "<span style='margin-top:70px;'>$percent% din WeSkate îți aparține</span>";
echo "</td>";
echo "<td class='my-navtv' align='right' style='padding-top:5px;'>
<a href='stiri.php$key' class='my-navlink stiri'>Scrie o &#351;tire<span> &lsaquo;</span></a>
<a href='articole.php$key' class='my-navlink articole'>Scrie un articol<span> &lsaquo;</span></a>
<a href='spoturi.php$key' class='my-navlink spoturi'>Adaug&#259; un loc de skate<span> &lsaquo;</span></a>
<a href='video.php$key' class='my-navlink video'>Trimite video<span> &lsaquo;</span></a>
</td></tr>";
echo "<tr valign='top'><td>";

echo "<table cellpadding='4' width='100%' style='border:2px solid #555;margin-top:5px;' class='smallround'>";
echo "<tr class='tbl2'><td>Obiect</td><td>tu</td><td>total</td><td>%</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Știri</td><td>$stiri</td><td>$stiri_all</td><td>".($stiri_all ? round((100*$stiri)/$stiri_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Articole</td><td>$articole</td><td>$articole_all</td><td>".($articole_all ? round((100*$articole)/$articole_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Albume foto</td><td>$albume_foto</td><td>$albume_foto_all</td><td>".($albume_foto_all ? round((100*$albume_foto)/$albume_foto_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Fotografii</td><td>$foto</td><td>$foto_all</td><td>".($foto_all ? round((100*$foto)/$foto_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Locuri de skate</td><td>$spot</td><td>$spot_all</td><td>".($spot_all ? round((100*$spot)/$spot_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Poze locuri de skate</td><td>$spot_photos</td><td>$spot_photos_all</td><td>".($spot_photos_all ? round((100*$spot_photos)/$spot_photos_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Postări în forum</td><td>$posts</td><td>$posts_all</td><td>".($posts_all ? round((100*$posts)/$posts_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl1'><td>Comentarii</td><td>$comments</td><td>$comments_all</td><td>".($comments_all ? round((100*$comments)/$comments_all,2) : 0)."</td></tr>";
echo "<tr class='lightonhoverF tbl2'><td>Total</td><td>$my</td><td>$all</td><td>$percent</td></tr>";
echo "</table>";
echo "</td><td>";
//right side

if ($ciorne) {
	echo "<div class='my-remindertd'><span>Nu uita de ciorne!</span>";
	if ($ciorneArticole) {
		echo "<a href='javascript:void(0);' onclick='showedit(\"a\")'>".$ciorneArticole." articol".($ciorneArticole > 1 ? "e" : "")." neterminat".($ciorneArticole > 1 ? "e" : "")."</a><br />";
	}
	if ($ciorneStiri) {
		echo "<a href='javascript:void(0);' onclick='showedit(\"n\")'>".$ciorneStiri." &#351;tir".($ciorneStiri > 1 ? "i" : "e")." nepublicat".($ciorneStiri > 1 ? "e" : "&#259;")."</a>";
	}
	echo "<span id='ciorne-txt'>Editeaz&#259; ciorne:</span>
	</div>";
	echo "<div id='ciorne'>";
	echo "</div>";
}




echo "</td></tr>";
echo "</table>";

require_once SCRIPTS."footer.php";
?>
