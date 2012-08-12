<?php
/*
| get_video() function originally by Fraev, in video.php
| from video BBCode. (http://fplace.atwebpages.com)
*/

if (!defined("inWeSkateCheck")) { header("Location : index.php"); }

function showVideo($embed,$autoplay=0,$width=425,$height=344) {
	return str_replace(array("[widthPX]","[heightPX]","[autoplay]"),array($width,$height,$autoplay),$embed);
}

function get_video_info($link) {
	if ($video = get_video($link)) {
		$info = explode("||||",$video);
		return $info;
	} else {
		return false;
	}
}

function get_video($link){
$values = array (
//http://www.youtube.com/watch?v=OygxkgewEhU
array('/youtube\.com.*v=([^&]*)/i', '<object width="[widthPX]" height="[heightPX]"><param name="movie" value="http://www.youtube-nocookie.com/v/{ID_VIDEO}&amp;hl=en_US&amp;fs=1&amp;rel=0&amp;hd=1&amp;showinfo=0&amp;autoplay=[autoplay]&amp;iv_load_policy=3&amp;color1=0x5d1719&amp;color2=0xcd311b"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube-nocookie.com/v/{ID_VIDEO}&amp;hl=en_US&amp;fs=1&amp;rel=0&amp;hd=1&amp;showinfo=0&amp;autoplay=[autoplay]&amp;iv_load_policy=3&amp;color1=0x5d1719&amp;color2=0xcd311b" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="[widthPX]" height="[heightPX]"></embed></object>||||http://img.youtube.com/vi/{ID_VIDEO}/default.jpg'));

foreach ($values as $value){
if (preg_match($value[0], $link, $matches)){
$id_video=$matches[1];
return preg_replace_callback('/{.*?}/', create_function('$matches', 'switch (true){
case preg_match("/\{ID_VIDEO\}/", $matches[0]):
return "'.$id_video.'";
break;
case preg_match("/\{LINK\}/", $matches[0]):
return "'.$link.'";
break;
case preg_match("/\{DOWNLOAD(.*?)%(.*?)%(.*?)\}/", $matches[0], $matches2):
if (empty($matches2[1])) $matches2[1]="'.$link.'";
preg_match($matches2[2], file_get_contents(str_replace(" ","+",$matches2[1])), $matches3);
if (empty($matches2[3])){
return $matches3[1];
}else{
$t=$matches3[1];
foreach(explode("|", $matches2[3]) as $e){
eval(\'$t=\'.$e.\'($t);\');
}
return $t;
}
break;
}
return $matches[0];'), $value[1]);
}
}
return false;
}
?>
