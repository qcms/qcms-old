<?
//-----------------------------------------------------------------------------
// Настройки базы данных

// тип БД
define("DATABASE_TYPE", "mysql");
// сервер БД
define("DATABASE_SERVER", "127.0.0.1");
// имя БД
define("DATABASE_NAME", "qcms");
// user
define("DATABASE_USER", "qcms");
// password
define("DATABASE_PW", "qcms");
// префикс таблиц в БД
define("DATABASE_PREFIX", "");

define("DATABASE_CHARSET", "utf8 collate utf8_general_ci");
define("DATABASE_AFTER_CONNECT", "SET NAMES " . DATABASE_CHARSET);

?>