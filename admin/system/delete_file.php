<?
//===========================================================================
// скрипт удаления файла из БД и удаления ссылки на этот файл
// обязательные файлы

$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");


$MAIN = new CMain($root,array("need_config" => false, "is_admin"=>true));
$MAIN->HeaderIncludes();
$MAIN->Init();

//$MAIN->IncludeModule("header.inc.php", true);

//===========================================================================
// обработка формы
if( !isset($_GET["id"]) || !$_GET["id"] ){
    echo "query error";
    exit;
}

// экземпляр файла
$file = new FileClass($_GET["id"]);
if( $file->file_id )
{
    // удалим файл
    $file->delete_file();

    // удалим ссылку на картинку
    if( isset($_GET["table"]) && $_GET["table"]
        && isset($_GET["field"]) && $_GET["field"] )
    {
        mysql_query("
UPDATE " . DATABASE_PREFIX . $_GET["table"] . "
SET " . $_GET["field"] . "='0'
WHERE " . $_GET["field"] . "='" . $_GET["id"] . "'
");
    }
}

// redirect
if($_GET["backurl"])
{
    header("Location: " . urldecode( $_GET["backurl"] ) );
    exit;
}

//$MAIN->IncludeModule("footer.inc.php", true);
?>