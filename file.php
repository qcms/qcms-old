<?
//===========================================================================
// скрипт удаления картинки из БД и удаления ссылки на эту картинку
// обязательные файлы

$root = "";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");


$MAIN = new CMain($root,array("need_config" => false, "session_last_query" => false));
$MAIN->HeaderIncludes();
$MAIN->Init();


$message = "";

//===========================================================================
// обработка
if( !isset($_GET["id"]) || !$_GET["id"] || !intval($_GET["id"]) )
{
    echo "query error";
    exit;
}
// экземпляр файла
$file = new FileClass(intval($_GET["id"]));



    if($file->file_id)
    {
        if( isset($_GET["att"]) && $_GET["att"] == "1" )
        {
            // присоединить файл
            echo $file->view_file(null, true);
        }
        else
        {
            // просто отдаем файл
            echo $file->view_file();
        }
    }

include($root."includes/application_bottom.inc.php");
?>