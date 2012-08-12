<?php
if (!defined("inWeSkateCheck")) { die("Acces respins."); }

//prefix:
define("DB_PREFIX","weskate_");

//general
define("DB_BBCODES", DB_PREFIX."bbcodes");
define("DB_BLACKLIST", DB_PREFIX."blacklist");
define("DB_CAPTCHA", DB_PREFIX."captcha");
define("DB_FLOOD_CONTROL", DB_PREFIX."flood_control");
define("DB_ATTACH", DB_PREFIX."forum_attachments");
define("DB_NEW_USERS", DB_PREFIX."new_users");
define("DB_SMILEYS", DB_PREFIX."smileys");
define("DB_SETTINGS", DB_PREFIX."settings");
define("DB_STATISTICS", DB_PREFIX."statistics");

//articole:
define("DB_ARTICLE_CATS", DB_PREFIX."article_cats");
define("DB_ARTICLES", DB_PREFIX."articles");

//blog:
define("DB_BLOG", DB_PREFIX."blog");
define("DB_BLOG_CATS", DB_PREFIX."blog_cats");

//stiri:
define("DB_NEWS", DB_PREFIX."news");
define("DB_NEWS_CATS", DB_PREFIX."news_cats");

//forum
define("DB_FORUMS", DB_PREFIX."forums");
define("DB_POSTS", DB_PREFIX."posts");
define("DB_THREADS", DB_PREFIX."threads");

//poze
define("DB_PHOTO_ALBUMS", DB_PREFIX."photo_albums");
define("DB_PHOTOS", DB_PREFIX."photos");

//prin tara
define("DB_SPOT_ALBUMS", DB_PREFIX."spot_albums");
define("DB_SPOTS", DB_PREFIX."spots");
define("DB_CITIES", DB_PREFIX."cities");
define("DB_SPOT_PHOTOS", DB_PREFIX."spot_photos");

//video
define("DB_VIDEOS", DB_PREFIX."videos");
define("DB_VIDEO_CATS", DB_PREFIX."video_cats");

//search
define("DB_SEARCH", DB_PREFIX."search");

//pools
define("DB_POLL_VOTES", DB_PREFIX."poll_votes");
define("DB_POLLS", DB_PREFIX."polls");

//community commons
define("DB_RATINGS", DB_PREFIX."ratings");
define("DB_COMMENTS", DB_PREFIX."comments");
define("DB_FAVORITE", DB_PREFIX."favo");
define("DB_FRIENDS", DB_PREFIX."friends");

//tricks
define("DB_TRICKS", DB_PREFIX."tricks");
define("DB_VALIDATOR", DB_PREFIX."validator");

//users
define("DB_USERS", DB_PREFIX."users");
define("DB_RELATIONS", DB_PREFIX."relations");
define("DB_USER_PANELS",DB_PREFIX."user_panels");
?>
