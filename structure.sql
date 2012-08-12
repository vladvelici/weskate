-- Host: localhost    Database: weskate
-- ------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `weskate_article_cats`
--

DROP TABLE IF EXISTS `weskate_article_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_article_cats` (
  `article_cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `article_cat_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`article_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_articles`
--

DROP TABLE IF EXISTS `weskate_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_articles` (
  `article_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `article_cat` mediumint(8) unsigned DEFAULT '0',
  `article_subject` varchar(200) NOT NULL DEFAULT '',
  `article_snippet` text NOT NULL,
  `article_article` text NOT NULL,
  `article_descriere` varchar(200) DEFAULT '',
  `article_keywords` varchar(200) DEFAULT '',
  `article_draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `article_breaks` char(1) NOT NULL DEFAULT '',
  `article_name` smallint(5) unsigned NOT NULL DEFAULT '1',
  `article_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `article_reads` mediumint(8) unsigned DEFAULT '0',
  `article_allow_comments` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `article_allow_ratings` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `article_photoalbum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `article_thumb` varchar(200) NOT NULL DEFAULT '',
  `article_sources` text NOT NULL,
  PRIMARY KEY (`article_id`),
  KEY `article_datestamp` (`article_datestamp`),
  KEY `article_reads` (`article_reads`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_blacklist`
--

DROP TABLE IF EXISTS `weskate_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_blacklist` (
  `blacklist_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `blacklist_ip` varchar(20) NOT NULL DEFAULT '',
  `blacklist_email` varchar(100) NOT NULL DEFAULT '',
  `blacklist_reason` text NOT NULL,
  `blacklist_why` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blacklist_expire` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`blacklist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_blog`
--

DROP TABLE IF EXISTS `weskate_blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_blog` (
  `blog_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `blog_subject` varchar(200) NOT NULL DEFAULT '',
  `blog_blog` text NOT NULL,
  `blog_user` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `blog_edit_user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `blog_edit_datestmp` int(10) unsigned NOT NULL DEFAULT '0',
  `blog_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `blog_visibility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `blog_reads` int(10) unsigned NOT NULL DEFAULT '0',
  `blog_draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blog_page` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `blog_allow_comments` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `blog_allow_ratings` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`blog_id`),
  KEY `blog_datestamp` (`blog_datestamp`),
  KEY `blog_reads` (`blog_reads`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_cities`
--

DROP TABLE IF EXISTS `weskate_cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_cities` (
  `city_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `city_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `city_name` varchar(100) NOT NULL DEFAULT '',
  `city_description` text NOT NULL,
  `city_judet` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`city_id`),
  KEY `album_title` (`city_name`),
  FULLTEXT KEY `album_description` (`city_description`),
  FULLTEXT KEY `album_title_2` (`city_name`)
) ENGINE=MyISAM AUTO_INCREMENT=417 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_comments`
--

DROP TABLE IF EXISTS `weskate_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `comment_item_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `comment_type` char(2) NOT NULL DEFAULT '',
  `comment_name` varchar(50) NOT NULL DEFAULT '',
  `comment_message` text NOT NULL,
  `comment_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `comment_datestamp` (`comment_datestamp`),
  KEY `comment_type` (`comment_type`)
) ENGINE=MyISAM AUTO_INCREMENT=627 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_favo`
--

DROP TABLE IF EXISTS `weskate_favo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_favo` (
  `fav_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(5) unsigned NOT NULL,
  `fav_user` smallint(5) unsigned NOT NULL,
  `fav_type` varchar(10) NOT NULL,
  PRIMARY KEY (`fav_id`)
) ENGINE=MyISAM AUTO_INCREMENT=630 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_forums`
--

DROP TABLE IF EXISTS `weskate_forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_forums` (
  `forum_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `forum_cat` mediumint(8) unsigned DEFAULT '0',
  `forum_name` varchar(100) NOT NULL DEFAULT '',
  `forum_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `forum_description` text NOT NULL,
  `forum_moderators` text NOT NULL,
  `forum_access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `forum_post` smallint(3) unsigned DEFAULT '101',
  `forum_poll` smallint(3) unsigned NOT NULL DEFAULT '0',
  `forum_vote` smallint(3) unsigned NOT NULL DEFAULT '0',
  `forum_reply` smallint(3) unsigned NOT NULL DEFAULT '0',
  `forum_attach` smallint(3) unsigned NOT NULL DEFAULT '0',
  `forum_lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_postcount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `forum_threadcount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `forum_lastuser` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`forum_id`),
  KEY `forum_order` (`forum_order`),
  KEY `forum_lastpost` (`forum_lastpost`),
  KEY `forum_postcount` (`forum_postcount`),
  KEY `forum_threadcount` (`forum_threadcount`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_friends`
--

DROP TABLE IF EXISTS `weskate_friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_friends` (
  `rel_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `friend_one` smallint(5) unsigned NOT NULL,
  `friend_two` smallint(5) unsigned NOT NULL,
  `rel_status` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_new_users`
--

DROP TABLE IF EXISTS `weskate_new_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_new_users` (
  `user_code` varchar(64) NOT NULL,
  `user_email` varchar(254) NOT NULL,
  `user_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_password` varchar(128) NOT NULL,
  `user_hide_email` tinyint(1) unsigned NOT NULL,
  `user_name` varchar(200) NOT NULL,
  UNIQUE KEY `user_code` (`user_code`),
  KEY `user_datestamp` (`user_datestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_news`
--

DROP TABLE IF EXISTS `weskate_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_news` (
  `news_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `news_subject` varchar(200) NOT NULL DEFAULT '',
  `news_cat` mediumint(8) unsigned DEFAULT '0',
  `news_news` text NOT NULL,
  `news_descriere` varchar(200) DEFAULT '',
  `news_keywords` varchar(200) DEFAULT '',
  `news_extended` text NOT NULL,
  `news_breaks` char(1) NOT NULL DEFAULT '',
  `news_name` smallint(5) unsigned NOT NULL DEFAULT '1',
  `news_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `news_start` int(10) unsigned NOT NULL DEFAULT '0',
  `news_end` int(10) unsigned NOT NULL DEFAULT '0',
  `news_visibility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `news_reads` mediumint(8) unsigned DEFAULT '0',
  `news_draft` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `news_sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `news_allow_comments` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `news_allow_ratings` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `news_city` mediumint(7) NOT NULL DEFAULT '0',
  `news_photoalbum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `news_thumb` varchar(200) NOT NULL DEFAULT '',
  `news_sources` text NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `news_datestamp` (`news_datestamp`),
  KEY `news_reads` (`news_reads`),
  KEY `news_subject` (`news_subject`),
  KEY `news_keywords` (`news_keywords`),
  KEY `news_descriere` (`news_descriere`),
  FULLTEXT KEY `news_subject_2` (`news_subject`),
  FULLTEXT KEY `news_news` (`news_news`),
  FULLTEXT KEY `news_extended` (`news_extended`),
  FULLTEXT KEY `news_extended_2` (`news_extended`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_news_cats`
--

DROP TABLE IF EXISTS `weskate_news_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_news_cats` (
  `news_cat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `news_cat_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`news_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_photo_albums`
--

DROP TABLE IF EXISTS `weskate_photo_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_photo_albums` (
  `album_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `album_title` varchar(100) NOT NULL DEFAULT '',
  `album_description` text NOT NULL,
  `album_thumb` varchar(100) NOT NULL DEFAULT '',
  `album_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `album_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`album_id`),
  KEY `album_datestamp` (`album_datestamp`),
  FULLTEXT KEY `album_description` (`album_description`),
  FULLTEXT KEY `album_title` (`album_title`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_photos`
--

DROP TABLE IF EXISTS `weskate_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_photos` (
  `photo_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` mediumint(8) unsigned DEFAULT '0',
  `photo_title` varchar(100) NOT NULL DEFAULT '',
  `photo_description` text NOT NULL,
  `photo_filename` varchar(100) NOT NULL DEFAULT '',
  `photo_thumb1` varchar(100) NOT NULL DEFAULT '',
  `photo_thumb2` varchar(100) NOT NULL DEFAULT '',
  `photo_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `photo_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `photo_views` smallint(5) unsigned NOT NULL DEFAULT '0',
  `photo_allow_comments` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `photo_allow_ratings` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`photo_id`),
  KEY `photo_datestamp` (`photo_datestamp`),
  KEY `photo_title` (`photo_title`),
  KEY `photo_title_2` (`photo_title`),
  FULLTEXT KEY `photo_description` (`photo_description`),
  FULLTEXT KEY `photo_title_3` (`photo_title`)
) ENGINE=MyISAM AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_posts`
--

DROP TABLE IF EXISTS `weskate_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_posts` (
  `thread_id` mediumint(8) unsigned DEFAULT '0',
  `post_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `post_quote` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `post_message` text NOT NULL,
  `post_author` smallint(5) unsigned NOT NULL DEFAULT '0',
  `post_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `post_edituser` smallint(5) unsigned NOT NULL DEFAULT '0',
  `post_edittime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`),
  KEY `thread_id` (`thread_id`),
  KEY `post_datestamp` (`post_datestamp`),
  KEY `post_quote` (`post_quote`),
  FULLTEXT KEY `post_message` (`post_message`)
) ENGINE=MyISAM AUTO_INCREMENT=866 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_ratings`
--

DROP TABLE IF EXISTS `weskate_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_ratings` (
  `rating_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `rating_item_id` mediumint(8) unsigned DEFAULT '0',
  `rating_type` char(1) NOT NULL DEFAULT '',
  `rating_user` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rating_vote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rating_id`)
) ENGINE=MyISAM AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_search`
--

DROP TABLE IF EXISTS `weskate_search`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_search` (
  `search_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `search_item` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `search_type` varchar(1) NOT NULL DEFAULT '',
  `search_text` text NOT NULL,
  `search_title` varchar(200) NOT NULL DEFAULT '',
  `search_keywords` text NOT NULL,
  `search_local` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `search_visibility` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `search_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `search_url` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`search_id`),
  KEY `search_type` (`search_type`),
  KEY `search_datestamp` (`search_datestamp`),
  KEY `search_visibility` (`search_visibility`),
  KEY `search_local` (`search_local`),
  FULLTEXT KEY `search_text` (`search_text`,`search_keywords`,`search_title`)
) ENGINE=MyISAM AUTO_INCREMENT=363 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_settings`
--

DROP TABLE IF EXISTS `weskate_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_settings` (
  `setting_name` varchar(200) NOT NULL,
  `setting_value` varchar(200) NOT NULL,
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_spot_albums`
--

DROP TABLE IF EXISTS `weskate_spot_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_spot_albums` (
  `spot_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `spot_title` varchar(100) NOT NULL DEFAULT '',
  `spot_description` text NOT NULL,
  `spot_thumb` varchar(100) NOT NULL DEFAULT '',
  `spot_user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `spot_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `spot_city` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `spot_coords` varchar(30) NOT NULL DEFAULT '0',
  `spot_adress` varchar(200) NOT NULL DEFAULT '',
  `spot_views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`spot_id`),
  KEY `spot_datestamp` (`spot_datestamp`),
  FULLTEXT KEY `spot_description` (`spot_description`),
  FULLTEXT KEY `spot_title` (`spot_title`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_spot_photos`
--

DROP TABLE IF EXISTS `weskate_spot_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_spot_photos` (
  `photo_id` mediumint(7) unsigned NOT NULL AUTO_INCREMENT,
  `photo_spot` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `photo_title` varchar(200) NOT NULL DEFAULT '',
  `photo_file` varchar(50) NOT NULL DEFAULT '',
  `photo_user` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `photo_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_statistics`
--

DROP TABLE IF EXISTS `weskate_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_statistics` (
  `counter` smallint(5) unsigned NOT NULL DEFAULT '0',
  `date` smallint(5) unsigned NOT NULL,
  UNIQUE KEY `date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_threads`
--

DROP TABLE IF EXISTS `weskate_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_threads` (
  `forum_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `thread_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `thread_subject` varchar(100) NOT NULL DEFAULT '',
  `thread_author` smallint(5) unsigned NOT NULL DEFAULT '0',
  `thread_views` smallint(5) unsigned NOT NULL DEFAULT '0',
  `thread_lastpost` int(10) unsigned NOT NULL DEFAULT '0',
  `thread_postcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `thread_lastuser` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`thread_id`),
  KEY `thread_postcount` (`thread_postcount`),
  KEY `thread_lastpost` (`thread_lastpost`),
  KEY `thread_views` (`thread_views`)
) ENGINE=MyISAM AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_tricks`
--

DROP TABLE IF EXISTS `weskate_tricks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_tricks` (
  `trick_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `trick_name` varchar(100) NOT NULL DEFAULT '',
  `trick_url` varchar(50) NOT NULL,
  `trick_sinonim` varchar(255) NOT NULL DEFAULT '',
  `trick_howto` text NOT NULL,
  `trick_requires` varchar(50) NOT NULL DEFAULT '0',
  `trick_fbug` text NOT NULL,
  PRIMARY KEY (`trick_id`),
  UNIQUE KEY `trick_name` (`trick_name`),
  UNIQUE KEY `trick_url` (`trick_url`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_user_panels`
--

DROP TABLE IF EXISTS `weskate_user_panels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_user_panels` (
  `panel_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `panel_template` varchar(200) NOT NULL DEFAULT 'simple',
  `panel_title` varchar(200) NOT NULL DEFAULT '',
  `panel_content` text NOT NULL,
  `panel_user` mediumint(8) unsigned NOT NULL,
  `panel_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`panel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_users`
--

DROP TABLE IF EXISTS `weskate_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL DEFAULT '',
  `user_password` varchar(128) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_hide_email` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `user_avatar` varchar(100) NOT NULL DEFAULT 'gravatar',
  `user_joined` int(10) unsigned NOT NULL DEFAULT '0',
  `user_lastvisit` int(10) unsigned NOT NULL DEFAULT '0',
  `user_rights` text NOT NULL,
  `user_level` tinyint(3) unsigned NOT NULL DEFAULT '101',
  `user_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_aim` varchar(16) NOT NULL DEFAULT '',
  `user_icq` varchar(16) NOT NULL DEFAULT '',
  `user_msn` varchar(100) NOT NULL DEFAULT '',
  `user_skater` smallint(5) unsigned NOT NULL DEFAULT '1',
  `user_stance` smallint(5) unsigned NOT NULL DEFAULT '1',
  `user_web` varchar(200) NOT NULL DEFAULT '',
  `user_visibility` smallint(5) unsigned NOT NULL DEFAULT '1',
  `user_yahoo` varchar(100) NOT NULL DEFAULT '',
  `user_sig` text NOT NULL,
  `user_location` varchar(50) NOT NULL DEFAULT '',
  `user_profileurl` varchar(30) NOT NULL DEFAULT '',
  `user_culoarepagina` varchar(13) NOT NULL DEFAULT 'albastru',
  `user_blog` varchar(255) NOT NULL DEFAULT '0',
  `user_colors` text NOT NULL,
  `user_points` smallint(5) unsigned NOT NULL DEFAULT '1',
  `user_cookie` varchar(128) NOT NULL DEFAULT '',
  `user_cookie_exp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name_2` (`user_name`),
  UNIQUE KEY `user_profileurl` (`user_profileurl`),
  KEY `user_name` (`user_name`),
  KEY `user_joined` (`user_joined`),
  KEY `user_lastvisit` (`user_lastvisit`),
  KEY `user_cookie` (`user_cookie`),
  KEY `user_cookie_2` (`user_cookie`)
) ENGINE=MyISAM AUTO_INCREMENT=155 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_validator`
--

DROP TABLE IF EXISTS `weskate_validator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_validator` (
  `validator_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `validator_trick` mediumint(8) unsigned NOT NULL,
  `validator_file` varchar(255) NOT NULL,
  `validator_true` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `validator_false` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `validator_lastuser` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `validator_trust` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `validator_user` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`validator_id`),
  KEY `validator_trick` (`validator_trick`,`validator_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_video_cats`
--

DROP TABLE IF EXISTS `weskate_video_cats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_video_cats` (
  `video_cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `video_cat_name` varchar(200) NOT NULL DEFAULT '',
  `video_cat_sub` smallint(5) NOT NULL DEFAULT '0',
  `video_cat_desc` text NOT NULL,
  PRIMARY KEY (`video_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `weskate_videos`
--

DROP TABLE IF EXISTS `weskate_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weskate_videos` (
  `video_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `video_title` varchar(200) NOT NULL DEFAULT '',
  `video_url` text NOT NULL,
  `video_meta_keywords` text NOT NULL,
  `video_meta_description` varchar(200) NOT NULL DEFAULT '',
  `video_embed` text NOT NULL,
  `video_thumb` text NOT NULL,
  `video_cat` smallint(5) unsigned NOT NULL DEFAULT '0',
  `video_owner` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `video_datestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `video_views` int(8) unsigned NOT NULL DEFAULT '0',
  `video_allow_ratings` smallint(1) unsigned NOT NULL DEFAULT '1',
  `video_allow_comments` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`video_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-12 22:54:15
