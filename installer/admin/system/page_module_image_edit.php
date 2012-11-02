<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => true, "is_admin" => true));
$MAIN->HeaderIncludes();
$MAIN->Init("page_module_image");

$entity = new CEntity(
    array(
        "table"=>"page_module_image",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


if($entity->identity)
{
    $GLOBALS["page_module_id"] = $entity->GetHeader("page_module_id");
    if($entity->GetParentId() && $entity->GetParent()->GetHeader("page_id"))
    {
        $GLOBALS["page_id"] = $entity->GetParent()->GetHeader("page_id");
    }

}
else
{
    if(isset($_GET["page_module_id"]) && intval($_GET["page_module_id"]))
    {
        $GLOBALS["page_module_id"] = intval($_GET["page_module_id"]);
    }
    if(isset($GLOBALS["page_module_id"]) && $GLOBALS["page_module_id"])
    {
        $entity_page_module = new CEntity(
            array(
                "table"=>"page_module",
                "id"=>$GLOBALS["page_module_id"]
            )
        );
        if($entity_page_module->identity && $entity_page_module->GetHeader("page_id"))
        {
            $GLOBALS["page_id"] = $entity_page_module->GetHeader("page_id");
        }
    }
//    if(isset($_GET["page_id"]) && intval($_GET["page_id"]))
//    {
//        $GLOBALS["page_id"] = intval($_GET["page_id"]);
//    }
}

// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module_image",
        )
    );

    $list->MoveUp($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_module_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module_image",
        )
    );

    $list->MoveDown($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_module_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );
    exit;
}


if($MAIN->CheckPost())
{
    $entity->SavePost();
    $GLOBALS["id"] = $entity->identity;
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module_image", "edit", false, true) );
    exit;

}
if($MAIN->CheckGet("action","delete"))
{
    $GLOBALS["id"] = $entity->GetHeader("page_id");
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );

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
    "2" => array(
        "table" => "page_module",
        "page" => "edit",
        "vars" => "id=".$GLOBALS["page_module_id"]
    ),
);

$admin_current_name_custom = $MAIN->GetAdminPageName("page_module_image", "edit");

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


exit;


$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => true, "is_admin" => true));
$MAIN->HeaderIncludes();
$MAIN->Init("page_module_image");

$entity = new CEntity(
    array(
        "table"=>"page_module_image",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


if($entity->identity)
{
    $GLOBALS["page_module_id"] = $entity->GetHeader("page_module_id");
    if($entity->GetParentId() && $entity->GetParent()->GetHeader("page_id"))
    {
        $GLOBALS["page_id"] = $entity->GetParent()->GetHeader("page_id");
    }

}
else
{
    if(isset($_GET["page_module_id"]) && intval($_GET["page_module_id"]))
    {
        $GLOBALS["page_module_id"] = intval($_GET["page_module_id"]);
    }
    if(isset($_GET["page_id"]) && intval($_GET["page_id"]))
    {
        $GLOBALS["page_id"] = intval($_GET["page_id"]);
    }
}

// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module_image",
        )
    );

    $list->MoveUp($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_module_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page_module_image",
        )
    );

    $list->MoveDown($_GET["id"]);
    $GLOBALS["id"] = $entity->GetHeader("page_module_id");
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );
    exit;
}


if($MAIN->CheckPost())
{
    $entity->SavePost();
    $GLOBALS["id"] = $entity->identity;
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module_image", "edit", false, true) );
    exit;

}
if($MAIN->CheckGet("action","delete"))
{
    $GLOBALS["id"] = $entity->GetHeader("page_id");
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("page_module", "edit", false, true) );

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
    "2" => array(
        "table" => "page_module",
        "page" => "edit",
        "vars" => "id=".$GLOBALS["page_module_id"]
    ),
);

$MAIN->IncludeModule("header.inc.php", true);

$MAIN->AdminShowMessages();

$MAIN->AdminShowBox("page_module_image_edit", $MAIN->GetAdminPageName("page_module_image", "edit"), $entity->ViewEdit());

$MAIN->IncludeModule("footer.inc.php", true);
?>