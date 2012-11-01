<?
$root = "";

// обязательные файлы
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => false, "need_session" => false));
$MAIN->HeaderIncludes();
$MAIN->Init();

if($_GET["id"] && intval($_GET["id"]))
{
    $image = new ImageClass(intval($_GET["id"]));

    if($image->image_id)
    {
        // выведем картинку
        $image->view_image();
    }
    else
    {
        // ошибка
        ImageClass::ErrorImage();
    }
    // выходим
    exit;
}

else
{
    // Выведем пустое изображение
    ImageClass::EmptyImage();
    // выходим
    exit;
}




?>
