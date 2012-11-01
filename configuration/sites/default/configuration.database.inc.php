<?
//-----------------------------------------------------------------------------
// Настройки базы данных

// тип БД
define("DATABASE_TYPE", "mysql");
// сервер БД
define("DATABASE_SERVER", "");
// имя БД
define("DATABASE_NAME", "");
// user
define("DATABASE_USER", "");
// password
define("DATABASE_PW", "");
// префикс таблиц в БД
define("DATABASE_PREFIX", "");

define("DATABASE_CHARSET", "utf8 collate utf8_general_ci");
define("DATABASE_AFTER_CONNECT", "SET NAMES " . DATABASE_CHARSET);

?>