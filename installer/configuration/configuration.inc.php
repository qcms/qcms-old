<?
/*
 * ----------------------------------------------------------------------------
 * Файл базовых настроек
 * ----------------------------------------------------------------------------
 * */


/*
 * ----------------------------------------------------------------------------
 * DIRNAME_* настройки каталогов
 * ----------------------------------------------------------------------------
 */

/**
 * каталог админки
 */
if(!defined("DIRNAME_ADMIN"))
    define("DIRNAME_ADMIN", "admin");
/**
 * каталог системных файлов
 */
if(!defined("DIRNAME_SYSTEM"))
    define("DIRNAME_SYSTEM", "system");
/**
 * каталог языковых файлов
 */
if(!defined("DIRNAME_LANG"))
    define("DIRNAME_LANG", "lang");
/**
 * каталог конфигурации
 */
if(!defined("DIRNAME_CONFIGURATION"))
    define("DIRNAME_CONFIGURATION", "configuration");
/**
 * каталог для хранения файлов базы данных
 */
if(!defined("DIRNAME_DATABASE"))
    define("DIRNAME_DATABASE", "database");
/**
 * каталог системных файлов конфигурации
 */
if(!defined("DIRNAME_CONFIGURATION_SYSTEM"))
    define("DIRNAME_CONFIGURATION_SYSTEM", "system");
/**
 * каталог системных файлов конфигурации
 */
if(!defined("DIRNAME_SITES"))
    define("DIRNAME_SITES", "sites");
/**
 * каталог сущностей
 */
if(!defined("DIRNAME_ENTITIES"))
    define("DIRNAME_ENTITIES", "entities");
/**
 * файл сущности
 */
if(!defined("FILENAME_ENTITY"))
    define("FILENAME_ENTITY", "entity.inc.php");
/**
 * каталог инклудов
 */
if(!defined("DIRNAME_INCLUDES"))
    define("DIRNAME_INCLUDES", "includes");
/**
 * каталог подключаемых блоков
 */
if(!defined("DIRNAME_BOXES"))
    define("DIRNAME_BOXES", "boxes");
/**
 * каталог css
 */
if(!defined("DIRNAME_CSS"))
    define("DIRNAME_CSS", "s");
/**
 * каталог шаблонов
 */
if(!defined("DIRNAME_TEMPLATES"))
    define("DIRNAME_TEMPLATES", "templates");
/**
 * каталог шаблонов админки
 */
if(!defined("DIRNAME_TEMPLATES_ADMIN"))
    define("DIRNAME_TEMPLATES_ADMIN", "admin");
/**
 * каталог шаблонов редактирования сущностей админки
 */
if(!defined("DIRNAME_TEMPLATES_ADMIN_VIEWEDIT"))
    define("DIRNAME_TEMPLATES_ADMIN_VIEWEDIT", ".viewedit");
/**
 * каталог изображений
 */
if(!defined("DIRNAME_IMAGES"))
    define("DIRNAME_IMAGES", "images");
/**
 * каталог видео
 */
if(!defined("DIRNAME_VIDEOS"))
    define("DIRNAME_VIDEOS", "videos");


/*
 * ----------------------------------------------------------------------------
 * настройки админки
 * ----------------------------------------------------------------------------
 */

/**
 * идентификатор сессии админки
 */
if(!defined("ADMIN_SESSION_ID"))
    define("ADMIN_SESSION_ID", "QCMS_ADMIN_SESSION_ID");
/**
 * секретный код админки
 */
if(!defined("ADMIN_SECRET"))
    define("ADMIN_SECRET", "12345");

/*
 * ----------------------------------------------------------------------------
 * настройки списков
 * ----------------------------------------------------------------------------
 */

/**
 * разделитель списков в БД
 */
if(!defined("LIST_SEPARATOR"))
    define("LIST_SEPARATOR", "\n");
/**
 * разделитель элементов списка
 */
if(!defined("LIST_ITEM_SEPARATOR"))
    define("LIST_ITEM_SEPARATOR", "|");
/**
 * разделитель языков в тексте
 */
if(!defined("LANGUAGE_SPLITTER"))
    define("LANGUAGE_SPLITTER", "---|LANG|---");


?>