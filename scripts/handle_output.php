<?php
/*-------------------------------------------------------+
| ORIGINAL COPYRIGHT
| ------------------
|
| Original filename: output_handling_include.php
| Original author: Max Toball (Matonor)
| 
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+========================================================+
| Modified to fit WeSkate's needs by Velici Vlad.
| Current filename: handle_output.php
+-------------------------------------------------------*/
if (!defined("inWeSkateCheck")) { die("Access respins"); }

$wsk_page_replacements = "";
$wsk_page_title = $setari['sitename'];
$wsk_page_meta = array("description" => $setari['description'], "keywords" => $setari['keywords']);
$wsk_page_head_tags = "";

function set_title($title=""){
	global $wsk_page_title;
	$wsk_page_title = $title;
}

function add_to_title($addition=""){
	global $wsk_page_title;
	$wsk_page_title .= $addition;
}

function set_meta($name, $content=""){
	global $wsk_page_meta;
	$wsk_page_meta[$name] = $content;
}

function add_to_meta($name, $addition=""){
	global $wsk_page_meta;
	if(isset($wsk_page_meta[$name])){
		$wsk_page_meta[$name] .= $addition;
	}
}

function add_to_head($tag=""){
	global $wsk_page_head_tags;
	if(!stristr($wsk_page_head_tags, $tag)){
		$wsk_page_head_tags .= $tag."\n";
	}
}

function replace_in_output($target, $replace, $modifiers=""){
	global $wsk_page_replacements;
	$wsk_page_replacements .= "\$output = preg_replace('^$target^$modifiers', '$replace', \$output);";
}

function handle_output($output){
	global $wsk_page_head_tags, $wsk_page_title, $wsk_page_meta, $wsk_page_replacements, $setari;

	if(!empty($wsk_page_head_tags)){
		$output = preg_replace("#</head>#", $wsk_page_head_tags."</head>", $output, 1);
	}
	if($wsk_page_title != $setari['sitename']){
		$output = preg_replace("#<title>.*</title>#i", "<title>".$wsk_page_title."</title>", $output, 1);
	}
	if(!empty($wsk_page_meta)){
		foreach($wsk_page_meta as $name => $content){
			$output = preg_replace("#<meta (http-equiv|name)='$name' content='.*' />#i", "<meta \\1='".$name."' content='".$content."' />", $output, 1);
		}
	}
	if(!empty($wsk_page_replacements)){
		eval($wsk_page_replacements);
	}
	return $output;
}

?>
