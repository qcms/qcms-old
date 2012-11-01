DROP TABLE IF EXISTS `adminuser`;

CREATE TABLE `adminuser` (
  `adminuser_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `adminuser_order` int(10) unsigned DEFAULT NULL,
  `adminusergroup_id` int(11) DEFAULT NULL,
  `adminuser_ln` varchar(200) NOT NULL,
  `adminuser_password` text,
  `adminuser_fullname` text,
  `adminuser_comment` longtext,
  `adminuser_datetime` datetime DEFAULT NULL,
  `adminuser_isshow` int(11) DEFAULT '0',
  `adminuser_hash` varchar(200) NOT NULL,
  PRIMARY KEY (`adminuser_id`),
  UNIQUE KEY `adminuser_ln` (`adminuser_ln`),
  UNIQUE KEY `adminuser_hash` (`adminuser_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




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

INSERT INTO `config` VALUES('1',NULL,'Тестовый сайт QCMS','keywords','description','','','0','','2010-01-01 00:00:00');




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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `images`;

CREATE TABLE `images` (
  `images_id` int(11) NOT NULL AUTO_INCREMENT,
  `images_contenttype` varchar(100) DEFAULT NULL,
  `images_filename` varchar(250) DEFAULT NULL,
  `images_table` varchar(100) DEFAULT NULL,
  `images_table_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`images_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `images` VALUES('1','image/jpeg','file_img_1.jpg','','0');

INSERT INTO `images` VALUES('2','image/jpeg','file_img_2.jpg','','0');

INSERT INTO `images` VALUES('3','image/jpeg','file_img_3.jpg','','0');

INSERT INTO `images` VALUES('4','image/jpeg','file_img_4.jpg','','0');




DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `module_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module_order` int(10) unsigned DEFAULT NULL,
  `module_ln` text,
  `module_name` text,
  `module_link` text,
  `module_text` longtext,
  `module_isphp` int(11) DEFAULT '0',
  `module_isshow` int(11) DEFAULT '0',
  `module_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `page` VALUES('1','1','Главная','0','100','','','','index','','0','1',NULL,'2010-01-01 00:00:00');

INSERT INTO `page` VALUES('2','2','Тестовая модульная страница','0','2','','','','','','0','1','2012-10-25 03:54:37','2012-10-25 03:54:59');




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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `page_module` VALUES('1','1','2','40','йййййййййй','',NULL,NULL,NULL,NULL,NULL,'1','2012-10-25 03:55:42');




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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `page_module_image` VALUES('1','1','1','йй','1','2','1','2012-10-25 03:56:38');

INSERT INTO `page_module_image` VALUES('2','2','1','ww','3','4','1','2012-10-25 03:57:49');




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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `session` VALUES('1','2012-10-25 03:57:53','cc0bcc8e03536ac298efaa43fab421d7','/admin/page_module_edit.php?id=1','RU','0','0');




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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




