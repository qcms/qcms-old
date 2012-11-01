<?php
//-----------------------------------------------------------------------------
// Модуль описывает инициализацию базы данных значениями по умолчанию
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// структура массива
//array( 
//	"table1" => array( 
//		"field1" => "value1", 
//		"field2" => "value2", 
//	)
//);

global $database_init;
$database_init = array(
	"config" => array(
		array(
			"config_id" => "1",
			"config_site_name" => "Тестовый сайт QCMS",
			"config_datetime" => "2010-01-01 00:00:00",
			"config_site_keywords" => "keywords",
			"config_site_description" => "description",
			"config_site_isclosed" => "0",
		),
	),
	"page" => array(
		array(
			"page_id" => "1",
			"page_ln" => "index",
			"page_parent" => "0",
			"page_name" => "Главная",
			"page_datetime" => "2010-01-01 00:00:00",
			"page_isshow" => "1",
			"page_type" => "100",
		
		),
	),	
);

//-----------------------------------------------------------------------------
// Строка запроса для создания специальных таблиц
global $database_init_query;
$database_init_query = <<<EOT
DROP TABLE IF EXISTS `images`;
CREATE TABLE  `images` (
  `images_id` int(11) NOT NULL auto_increment,
  `images_contenttype` varchar(100) default NULL,
  `images_filename` varchar(250) default NULL,
  `images_table` varchar(100) default NULL,
  `images_table_id` int(11) default NULL,
  PRIMARY KEY  (`images_id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `videos`;
CREATE TABLE  `videos` (
  `videos_id` int(10) unsigned NOT NULL auto_increment,
  `videos_contenttype` varchar(45) NOT NULL,
  `videos_table` varchar(45) NOT NULL,
  `videos_table_id` int(10) unsigned default NULL,
  `videos_dir` varchar(100) default NULL,
  `videos_filename` varchar(200) default NULL,
  `videos_datetime` datetime NOT NULL,
  `videos_lenght` int(10) unsigned default NULL,
  PRIMARY KEY  (`videos_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `sounds`;
CREATE TABLE  `sounds` (
  `sounds_id` int(10) unsigned NOT NULL auto_increment,
  `sounds_contenttype` varchar(45) NOT NULL,
  `sounds_table` varchar(45) NOT NULL,
  `sounds_table_id` int(10) unsigned default NULL,
  `sounds_dir` varchar(100) default NULL,
  `sounds_filename` varchar(200) default NULL,
  `sounds_datetime` datetime NOT NULL,
  `sounds_lenght` int(10) unsigned default NULL,
  PRIMARY KEY  (`sounds_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `files`;
CREATE TABLE  `files` (
  `files_id` int(11) NOT NULL auto_increment,
  `files_datetime` datetime default NULL,
  `files_contenttype` varchar(100) default NULL,
  `files_filename` varchar(250) default NULL,
  `files_table` varchar(100) default NULL,
  `files_table_id` int(11) default NULL,
  `files_table_file_field` varchar(200) default NULL,
  `files_file` varchar(200) default NULL,
  `files_type` varchar(200) default NULL,

  PRIMARY KEY  (`files_id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `session`;
CREATE TABLE  `session` (
  `session_id` int(10) unsigned NOT NULL auto_increment,
  `session_datetime` datetime default NULL,
  `session_ln` varchar(100),
  `session_last_query` text,
  `session_lang` varchar(100) default NULL,
  `session_isremember` int(10) unsigned default NULL,
  `user_id` int(10) unsigned default NULL,
  PRIMARY KEY  USING BTREE (`session_id`)
) ENGINE=MyISAM;


EOT;
?>