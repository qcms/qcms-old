DROP TABLE IF EXISTS `adminuser`;

CREATE TABLE `adminuser` (
  `adminuser_id` int(11) NOT NULL AUTO_INCREMENT,
  `adminuser_login` varchar(200) NOT NULL,
  `adminuser_password` varchar(200) DEFAULT NULL,
  `adminuser_pages` text,
  `adminuser_fullname` varchar(200) DEFAULT NULL,
  `adminuser_comment` text,
  `adminuser_isactive` int(10) unsigned DEFAULT NULL,
  `adminuser_datetime` datetime DEFAULT NULL,
  `adminuser_order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`adminuser_id`),
  UNIQUE KEY `Index_1` (`adminuser_login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `adminuser_access`;

CREATE TABLE `adminuser_access` (
  `adminuser_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminuser_access_order` int(10) unsigned DEFAULT NULL,
  `adminuser_id` int(11) DEFAULT NULL,
  `adminuser_access_entity` text,
  `adminuser_access_ishierarchisch` int(11) DEFAULT '0',
  `adminuser_access_isread` int(11) DEFAULT '0',
  `adminuser_access_iswrite` int(11) DEFAULT '0',
  `adminuser_access_isadd` int(11) DEFAULT '0',
  `adminuser_access_isdelete` int(11) DEFAULT '0',
  `adminuser_access_isshow` int(11) DEFAULT '0',
  PRIMARY KEY (`adminuser_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `adminuser_access_condition`;

CREATE TABLE `adminuser_access_condition` (
  `adminuser_access_condition_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminuser_access_condition_order` int(10) unsigned DEFAULT NULL,
  `adminuser_access_id` int(11) DEFAULT NULL,
  `adminuser_access_condition_field` text,
  `adminuser_access_condition_value` text,
  `adminuser_access_condition_ishierarchisch` int(11) DEFAULT '0',
  `adminuser_access_condition_isread` int(11) DEFAULT '0',
  `adminuser_access_condition_iswrite` int(11) DEFAULT '0',
  `adminuser_access_condition_isadd` int(11) DEFAULT '0',
  `adminuser_access_condition_isdelete` int(11) DEFAULT '0',
  `adminuser_access_condition_isshow` int(11) DEFAULT '0',
  PRIMARY KEY (`adminuser_access_condition_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `adminusergroup`;

CREATE TABLE `adminusergroup` (
  `adminusergroup_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminusergroup_order` int(10) unsigned DEFAULT NULL,
  `adminusergroup_ln` text,
  `adminusergroup_comment` longtext,
  `adminusergroup_datetime` datetime DEFAULT NULL,
  `adminusergroup_isshow` int(11) DEFAULT '0',
  PRIMARY KEY (`adminusergroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `adminusergroup_access`;

CREATE TABLE `adminusergroup_access` (
  `adminusergroup_access_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminusergroup_access_order` int(10) unsigned DEFAULT NULL,
  `adminusergroup_id` int(11) DEFAULT NULL,
  `adminuser_access_entity` text,
  `adminuser_access_access` text,
  PRIMARY KEY (`adminusergroup_access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_order` int(10) unsigned DEFAULT NULL,
  `config_site_name` text,
  `config_site_keywords` longtext,
  `config_site_description` longtext,
  `config_site_emailfrom` text,
  `config_site_emailto` text,
  `config_site_isclosed` int(11) DEFAULT '0',
  `config_site_isclosed_message` longtext,
  `config_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `config` VALUES('1',NULL,'111333---|LANG|---','keywords---|LANG|---','description---|LANG|---','fedor_belyaev@mail.ru','fedor_belyaev@mail.ru','0','---|LANG|---','2012-06-30 14:18:16');




DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `files_id` int(11) NOT NULL AUTO_INCREMENT,
  `files_datetime` datetime DEFAULT NULL,
  `files_contenttype` varchar(100) DEFAULT NULL,
  `files_filename` varchar(250) DEFAULT NULL,
  `files_table` varchar(100) DEFAULT NULL,
  `files_table_id` int(11) DEFAULT NULL,
  `files_table_file_field` varchar(200) DEFAULT NULL,
  `files_file` varchar(200) DEFAULT NULL,
  `files_type` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`files_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

INSERT INTO `files` VALUES('3','2012-07-16 23:28:04','image/jpeg','map_4.jpg','page_module','6','page_module_file','','');




DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `images_id` int(11) NOT NULL AUTO_INCREMENT,
  `images_contenttype` varchar(100) DEFAULT NULL,
  `images_filename` varchar(250) DEFAULT NULL,
  `images_table` varchar(100) DEFAULT NULL,
  `images_table_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`images_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

INSERT INTO `images` VALUES('3','image/gif','try_advanced_editor_new.gif','','0');

INSERT INTO `images` VALUES('4','image/jpeg','IMAG0077.jpg','','0');

INSERT INTO `images` VALUES('5','image/gif','bg_04.gif','','0');

INSERT INTO `images` VALUES('6','image/gif','bg_01.gif','','0');

INSERT INTO `images` VALUES('13','image/png','cursor-zoom-in.png','','0');

INSERT INTO `images` VALUES('14','image/jpeg','banner_lowprice.jpg','','0');

INSERT INTO `images` VALUES('15','image/jpeg','banner_lowprice.jpg','','0');




DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_order` int(10) unsigned DEFAULT NULL,
  `module_ln` text,
  `module_link` text,
  `module_name` text,
  `module_text` longtext,
  `module_isphp` int(11) DEFAULT '0',
  `module_isshow` int(11) DEFAULT '0',
  `module_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `module` VALUES('1','1','robots.txt','','файл robots.txt','User-Agent: *\r\nDisallow: /\r\n','0','1','2012-06-30 23:39:44');

INSERT INTO `module` VALUES('2','2','sitemap.xml','','файл sitemap.xml','<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n   <url>\r\n      <loc>http://example.com/</loc>\r\n      <lastmod>2012-06-30</lastmod>\r\n      <changefreq>monthly</changefreq>\r\n      <priority>0.8</priority>\r\n   </url>\r\n</urlset>','0','1','2012-06-30 23:50:47');




DROP TABLE IF EXISTS `page`;

CREATE TABLE `page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_order` int(10) unsigned DEFAULT NULL,
  `page_name` text,
  `page_parent` int(11) DEFAULT NULL,
  `page_type` int(11) DEFAULT NULL,
  `page_description` longtext,
  `page_keywords` longtext,
  `page_text` longtext,
  `page_ln` text,
  `page_link` text,
  `page_islocked` int(11) DEFAULT '0',
  `page_isshow` int(11) DEFAULT '0',
  `page_datetime_create` datetime DEFAULT NULL,
  `page_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

INSERT INTO `page` VALUES('1','1','Главная','0','100','','','','index','','0','1',NULL,'2010-01-01 00:00:00');

INSERT INTO `page` VALUES('2','2','Страница1---|LANG|---','0','1','Страница1---|LANG|---','Страница1---|LANG|---','<p>&nbsp;Тут немного текста. Тут немного текста. Тут немного текста.</p><p>Тут немного текста.Тут немного текста.</p><p><img style=\"width:229px;height:148px\" src=\"/images/file_img_6.gif\" width=\"229\" height=\"148\">Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.</p><p>Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста.Тут немного текста._</p>---|LANG|---','','','0','1','2012-05-26 00:33:44','2012-08-07 03:01:38');

INSERT INTO `page` VALUES('3','3','Страница2','0','1','','','','','','0','0','2012-05-26 00:34:12','2012-05-26 00:34:22');

INSERT INTO `page` VALUES('4','4','Страница3','0','1','','','','','','0','0','2012-05-26 00:34:27','2012-05-26 00:34:35');

INSERT INTO `page` VALUES('5','1','Страница1 1','2','1','','','','','','0','1','2012-05-26 00:34:42','2012-05-26 00:34:57');

INSERT INTO `page` VALUES('6','2','Страница1 2','2','1','','',' ','','','1','1','2012-05-26 00:35:02','2012-05-26 00:35:35');

INSERT INTO `page` VALUES('7','3','Страница1 3','2','1','','','','','','0','0','2012-05-26 00:35:14','2012-05-26 00:35:23');

INSERT INTO `page` VALUES('8','1','Страница1 1 1 www---|LANG|---','5','2','---|LANG|---','---|LANG|---','','wwww','','1','1','2012-05-26 00:56:18','2012-06-22 22:09:29');

INSERT INTO `page` VALUES('9','2','Страница1 1 2','5','100','','','','','','0','1','2012-05-26 00:56:37','2012-05-26 00:56:48');

INSERT INTO `page` VALUES('10','1','Страница1 2 1','6','1','','','','','','0','1','2012-05-26 00:56:59','2012-05-26 00:57:10');

INSERT INTO `page` VALUES('11','1','Страница1 2 1 1','10','1','','','','','','0','1','2012-05-26 00:57:16','2012-05-26 00:57:27');

INSERT INTO `page` VALUES('12','5','TEST','0','0','','','','','','0','0','2012-07-01 21:13:44','2012-07-01 21:13:51');




DROP TABLE IF EXISTS `page_module`;

CREATE TABLE `page_module` (
  `page_module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_module_order` int(10) unsigned DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `page_module_type` int(11) DEFAULT NULL,
  `page_module_name` text,
  `page_module_text` longtext,
  `page_module_preview` int(11) DEFAULT NULL,
  `page_module_picture` int(11) DEFAULT NULL,
  `page_module_video` int(11) DEFAULT NULL,
  `page_module_sound` int(11) DEFAULT NULL,
  `page_module_file` int(11) DEFAULT NULL,
  `page_module_isshow` int(11) DEFAULT '0',
  `page_module_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`page_module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `page_module` VALUES('1','1','8','20','rus caption---|LANG|---eng caption','<p>rus value</p>---|LANG|---<p>&nbsp;eng value</p>','5','6',NULL,NULL,NULL,'1','2012-06-25 00:52:34');

INSERT INTO `page_module` VALUES('2','2','8','50','Название видео1---|LANG|---','---|LANG|---','13',NULL,'6',NULL,NULL,'1','2012-07-01 21:28:17');

INSERT INTO `page_module` VALUES('3','3','8','60','qqqqqqqqq---|LANG|---','',NULL,NULL,NULL,'2',NULL,'1','2012-06-30 01:17:36');

INSERT INTO `page_module` VALUES('4','4','8','40','Тестовая галерея','',NULL,NULL,NULL,NULL,NULL,'1','2012-06-30 03:32:40');

INSERT INTO `page_module` VALUES('5','5','8','40','ййй','',NULL,NULL,NULL,NULL,NULL,'1','2012-06-30 03:58:59');

INSERT INTO `page_module` VALUES('6','6','8','70','qqq---|LANG|---','qqqqqqqq---|LANG|---',NULL,NULL,NULL,NULL,'3','1','2012-07-16 23:28:04');




DROP TABLE IF EXISTS `page_module_image`;

CREATE TABLE `page_module_image` (
  `page_module_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_module_image_order` int(10) unsigned DEFAULT NULL,
  `page_module_id` int(11) DEFAULT NULL,
  `page_module_image_name` text,
  `page_module_image_preview` int(11) DEFAULT NULL,
  `page_module_image_picture` int(11) DEFAULT NULL,
  `page_module_image_isshow` int(11) DEFAULT '0',
  `page_module_image_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`page_module_image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `page_module_image` VALUES('1','1','4','qq','14','15','1','2012-07-11 17:19:49');




DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `session_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_datetime` datetime DEFAULT NULL,
  `session_ln` varchar(100) DEFAULT NULL,
  `session_last_query` text,
  `session_lang` varchar(100) DEFAULT NULL,
  `session_isremember` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`session_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

INSERT INTO `session` VALUES('47','2012-07-19 18:03:05','jej7lqgghpkomfimhsb7r4r0u4','/admin/page_edit.php?id=8','RU','0','0');

INSERT INTO `session` VALUES('44','2012-07-16 12:27:16','2kvgd9hgo5th52kputnvp1kar1','/admin/page_all.php','RU','0','0');

INSERT INTO `session` VALUES('46','2012-07-17 21:15:07','2ea2vdq1ki36gha50encti3ap0','/admin/module_all.php','RU','0','0');

INSERT INTO `session` VALUES('45','2012-07-17 01:40:01','pe4cdhkpoav6rk716h5jrndtk3','/admin/page_module_edit.php?id=6','RU','0','0');

INSERT INTO `session` VALUES('49','2012-07-21 17:28:29','23q7d1q397km8m3u274a627p36','/admin/module_edit.php?id=1','RU','0','0');

INSERT INTO `session` VALUES('51','2012-08-05 16:35:07','udi9m5isuoh6bq63u73c4lmf00','/admin/config_all.php','RU','0','0');

INSERT INTO `session` VALUES('52','2012-08-07 03:01:38','4mmq1l76g1e9b3vtohnusm4mr6','/admin/page_edit.php?id=2','RU','0','0');

INSERT INTO `session` VALUES('43','2012-07-11 17:32:13','oagoubq7fr4ttju0huoodapet0','/admin/page_module_edit.php?id=4','RU','0','0');

INSERT INTO `session` VALUES('48','2012-07-19 23:48:03','34klgqp0m7veso8kk4t1iuur30','/admin/module_all.php','RU','0','0');

INSERT INTO `session` VALUES('50','2012-07-25 15:22:13','54mkt690fq8h6fr27p4ce94003','/admin/module_edit.php?id=1','RU','0','0');




DROP TABLE IF EXISTS `sounds`;

CREATE TABLE `sounds` (
  `sounds_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sounds_contenttype` varchar(45) NOT NULL,
  `sounds_table` varchar(45) NOT NULL,
  `sounds_table_id` int(10) unsigned DEFAULT NULL,
  `sounds_dir` varchar(100) DEFAULT NULL,
  `sounds_filename` varchar(200) DEFAULT NULL,
  `sounds_datetime` datetime NOT NULL,
  `sounds_lenght` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`sounds_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `sounds` VALUES('2','audio/mpeg','','0','','0.mp3','2012-06-30 01:17:36',NULL);




DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_order` int(10) unsigned DEFAULT NULL,
  `user_email` varchar(200) DEFAULT NULL,
  `user_surname` text,
  `user_name` text,
  `user_password` text,
  `user_sex` int(11) DEFAULT NULL,
  `user_type` int(11) DEFAULT NULL,
  `user_datetime_create` datetime DEFAULT NULL,
  `user_admin_comment` longtext,
  `user_isactive` int(11) DEFAULT '0',
  `user_isshow` int(11) DEFAULT '0',
  `user_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `idx_user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `videos`;

CREATE TABLE `videos` (
  `videos_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `videos_contenttype` varchar(45) NOT NULL,
  `videos_table` varchar(45) NOT NULL,
  `videos_table_id` int(10) unsigned DEFAULT NULL,
  `videos_dir` varchar(100) DEFAULT NULL,
  `videos_filename` varchar(200) DEFAULT NULL,
  `videos_datetime` datetime NOT NULL,
  `videos_lenght` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`videos_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `videos` VALUES('6','video/x-flv','','0','','video_1.flv','2012-06-29 01:15:04',NULL);




