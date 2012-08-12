<?php
require_once "../mainfile.php";
header("Content-type: text/css");
if (isset($_GET['user_id']) && isnum($_GET['user_id'])) {


$result = dbquery ("SELECT user_colors FROM ".DB_USERS." WHERE user_id=".$_GET['user_id']);
$udata = dbarray($result);
$culori = unserialize($udata['user_colors']);

echo "

.namecolor {\n
	color:#".(isset($culori['color_name']) ? $culori['color_name'] : "000000").";\n
	font-size:35px;\n
	text-decoration:none;
}\n

.MeniuRotunjit {
	background-color:#ccc;
	text-align:center;
	padding:5px 10px 5px 10px;
	display:inline-block;
	white-space:nowrap;
}
.MeniuRotunjit a {
	color:#".(isset($culori['color_menu_link']) ? $culori['color_menu_link'] : "000033").";
	padding:5px;
	padding-right:6px;
	font-size:14px;
	border-left:1px solid #888;
	margin-right: -1px;

}

.MeniuRotunjit a:hover {
	color:#eef;
	border-bottom : 2px solid #555;

	text-decoration : none;
	background-color:#999;
	background-image: url(http://img.weskate.ro/look/panou-mid.png);
	background-repeat: repeat-x;
}

.MeniuRotunjit span {
	color:#eef;
	border-bottom : 2px solid #555;
	text-decoration : none;
	background-color:#999;
	padding:5px;
	padding-right:6px;
	font-size:14px;
	border-left:1px solid #888;
	margin-right: -1px;

}
a.optnav {
	display:block;
	padding:4px;
}
a.optnav:hover {
	color:#000;
	text-decoration:none;
	background : url(http://img.weskate.ro/look/panou-mid.png) repeat-x transparent;
}
";
//PANOURI STANGA PROFIL :
echo "
.profil-panel {
	border:".(isset($culori['width_panel_border']) ? $culori['width_panel_border'] : "2")."px solid #".(isset($culori['color_panel_border']) ? $culori['color_panel_border'] : "2").";
}
.profil-paneltop {
	background-color:#".(isset($culori['background_panel_title']) ? $culori['background_panel_title'] : "555").";
	background-image:url(http://img.weskate.ro/look/degradeu.png);
	background-repeat:repeat-x;
	color:#".(isset($culori['color_panel_title']) ? $culori['color_panel_title'] : "FFFFFF").";
	padding-top:2px;
	padding-bottom:2px;
	padding-left:8px;
	padding-right:4px;
	font-size:25px;
	border-bottom:".(isset($culori['width_panel_border']) ? $culori['width_panel_border']-1 : "1")."px solid #".(isset($culori['color_panel_border']) ? $culori['color_panel_border'] : "CCCCCC").";
}
.profil-paneltop-color {
	color:#".(isset($culori['color_panel_title']) ? $culori['color_panel_title'] : "FFFFFF").";
}
.profil-panelbody {
	padding:4px;
	background-color:#".(isset($culori['background_panel_body']) ? $culori['background_panel_body'] : "F3F3F3").";
}


";
//FRIENDS :
echo "
.friendtd {
	border:2px solid #ddd;
	text-align:center;
}
.friendtd:hover {
	border:2px solid #999;
	background-color:#eee;
}
";
//BLOG :
echo "
.blog-inside-dark {
	background-color:#e1e1e1;
	margin-bottom:10px;
}
.blog-inside-light {
	background-color:#e9e9e9;
	margin-bottom:10px;
}
.title-blog a {
	font-size: 30px;
	font-family: 'Arial Black', Gadget, sans-serif;
	color:#CC3681;
	display:block;
	font-weight:bold;
	text-align: justify;
	line-height: 30px;
	text-decoration:none;
}
.title-blog span {
	font-size: 30px;
	font-family: 'Arial Black', Gadget, sans-serif;
	color:#CC3681;
	font-weight:bold;
	display:block;
	text-align: justify;
	line-height: 30px;
	text-decoration:none;
}
.title-blog a:hover {
	text-decoration:none;
	color:#FF69B4;
}

a.blogger-link {

	font-size:15px;
	padding-left:3px;
	color:#557;	
}
a.blogger-link:hover {
	text-decoration:underline;
	color:#779;
}
span.blogger-link {
	font-size:12px;
	padding-bottom:3px;
	font-weight:bold;
	display:inline;
	vertical-align:middle;
	color:#555;
	text-decoration:none;	
}
";

}

mysql_close();
?>
