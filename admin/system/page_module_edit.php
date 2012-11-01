<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => true, "is_admin" => true));
$MAIN->HeaderIncludes();
$MAIN->Init("page_module");

$entity = new CEntity(
    array(
        "table"=>"page_module",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


if($entity->identity)
{
    $GLOBALS["page_id"] = $entity->GetHeader("page_id");

    switch($entity->GetHeader("page_module_type"))
    {

        case "10": //        "10" => array("Текст"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "20": //        "20" => array("Текст с изображением слева"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "30": //        "30" => array("Текст с изображением справа"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "40": //        "40" => array("Галерея изображений"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "50": //        "50" => array("Видео"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "60": //        "60" => array("Звук"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "70": //        "70" => array("Файл с описанием"),
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
//            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
            break;
        case "100": //        "100" => array("Разделитель"),
            break;
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
            $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");

            break;

    }
}
else
{
    if(isset($_GET["page_id"]) && intval($_GET["page_id"]))
    {
        $GLOBALS["page_id"] = intval($_GET["page_id"]);
    }

    $MAIN->SetTableFieldParamsParam("page_module", "page_module_name", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_preview", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_picture", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_text", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_video", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_sound", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page_module", "page_module_file", "hidden", "true");
}

// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module",
        )
    );

    $list->MoveUp($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page", "edit", false, true) );
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module",
        )
    );

    $list->MoveDown($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page", "edit", false, true) );
    exit;
}


if($MAIN->CheckPost())
{
    $entity->SavePost();
    $entity = new CEntity(
        array(
            "table"=>"page_module",
            "id"=>$entity->identity,
        )
    );
    if(($entity->identity && $entity->GetHeader("page_module_type") == "1" //   "1" => array("Текст"),
        || $entity->identity && $entity->GetHeader("page_module_type") == "2" //	"2" => array("Текст с изображением слева"),
        || $entity->identity && $entity->GetHeader("page_module_type") == "3" //	"3" => array("Текст с изображением справа"),
    )
        && $entity->GetHeader("page_module_name") == ""
    )
    {
        $entity->Save(array("page_module_name" => "none"));

    }
    if($entity->identity && $entity->GetHeader("page_module_type") == "5") // "5" => array("Разделитель"),
    {
        $entity->Save(array("page_module_name" => "---"));
        //echo "AAAAAAAAAAAAAAA";exit;

    }

    $GLOBALS["id"] = $entity->identity;
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );
    exit;

}
if($MAIN->CheckGet("action","delete"))
{
    $GLOBALS["id"] = $entity->GetHeader("page_id");
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page", "edit", false, true) );

    exit;
}


$breadcrumb_parents = array(
    "0" => array(
        "table" => "page",
        "page" => "all",
        "vars" => ""
    ),
    "1" => array(
        "table" => "page",
        "page" => "edit",
        "vars" => "id=".$GLOBALS["page_id"]
    ),
);


$admin_current_name_custom = $MAIN->GetAdminPageName("page", "edit");

$MAIN->ShowTemplate(__FILE__, "", "page", "page.php", true);


/**
 * Функция отображения страницы административного интерфейса
 * возвращает контекст страницы административного интерфейса
 * @return string
 */
function admin_show_content()
{
    global $MAIN, $entity;
    $ret = "";

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "page_module_edit", $MAIN->GetAdminPageName("page_module", "edit"), $entity->ViewEditEx(__FILE__));

    if($entity->identity && $entity->GetHeader("page_module_type") == "40") //        "40" => array("Галерея изображений"),
    {
        $GLOBALS["page_module_id"] = $_GET["id"];

        $page_module_list = new CEntityList(
            array(
                "table" => "page_module_image",
                "table_parent" => "page_module",
                "key_parent" => "page_module_id",
                "parent_id" => $GLOBALS["page_module_id"],
            )
        );

        $page_module_list_view = "";
        $page_module_list_view .= <<<EOT
            <a href="page_module_image_edit.php?page_module_id={$GLOBALS["page_module_id"]}">Добавить изображение</a>
EOT;

        $page_module_list_view .= $page_module_list->ViewEditListEx(
            __FILE__,
            array(
                "keys"=>array("page_module_image_name", "page_module_image_preview", "page_module_image_isshow"),
                "actions"=>array("edit","up","down"),
            )
        );

        //error_log(print_r($page_module_list_view, true));


        $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "page_module_image_list", $MAIN->GetAdminPageName("page_module_image", "list"),  $page_module_list_view);
    }


    return $ret;
}

?>