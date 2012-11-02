<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => true, "is_admin" => true));
$MAIN->HeaderIncludes();
$MAIN->Init("page");

$entity = new CEntity(
    array(
        "table"=>"page",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


if($entity->identity)
{
    if($entity->GetHeader("page_ismainmenu") != "1")
    {
        $MAIN->SetTableFieldParamsParam("page", "page_menu_name", "hidden", "true");
    }
    switch ($entity->GetHeader("page_type"))
    {
        case "1": // "1" => array("Обычная страница"),
            break;
        case "2": // "2" => array("Модульная страница"),
            $MAIN->SetTableFieldParamsParam("page", "page_text", "hidden", "true");
            break;
        case "100": // "100" => array("Специальная страница"),
            $MAIN->SetTableFieldParamsParam("page", "page_text", "hidden", "true");
            break;
        default:
            $MAIN->SetTableFieldParamsParam("page", "page_text", "hidden", "true");
            break;
    }


}
else
{
    $MAIN->SetTableFieldParamsParam("page", "page_ismainmenu", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page", "page_menu_name", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page", "page_issubmenu", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page", "page_isfootermenu1", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page", "page_isfootermenu2", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("page", "page_text", "hidden", "true");
}

// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page",
            "table_parent"=>"page",
            "key_parent"=>"page_parent"
        )
    );

    $list->MoveUp($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("page", "all") );
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"page",
            "table_parent"=>"page",
            "key_parent"=>"page_parent"
        )
    );

    $list->MoveDown($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("page", "all") );
    exit;
}


if($MAIN->CheckPost())
{
    $entity->SavePost();
    // redirect
    header("Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity  );
    exit;

}
if($MAIN->CheckGet("action","delete"))
{
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("page", "all") );
    exit;
}

if($entity->identity)
{

    //		if($entity->GetParentId())
    //		{
    //			$MAIN->SetTableFieldParamsParam("page", "page_ismainmenu", "hidden", "true");
    //		}

    if($entity->GetHeader("page_ismainmenu"))
    {
        $MAIN->SetTableFieldParamsParam("page", "page_issubmenu", "hidden", "true");
    }
    elseif($entity->GetHeader("page_issubmenu"))
    {
        $MAIN->SetTableFieldParamsParam("page", "page_ismainmenu", "hidden", "true");
    }
    if(!$entity->GetHeader("page_ismainmenu") && !$entity->GetHeader("page_issubmenu")
        && !$entity->GetHeader("page_isfootermenu1") && !$entity->GetHeader("page_isfootermenu2"))
    {
        $MAIN->SetTableFieldParamsParam("page", "page_menu_name", "hidden", "true");
    }

}


//$template = $MAIN->GetTemplateContent(__FILE__, "", "page", "page.php");
//$breadcrumb_parents = array(
//    "0" => array(
//        "table" => "page",
//        "page" => "all",
//        "vars" => ""
//    ),
//    "1" => array(
//        "table" => "page",
//        "page" => "edit",
//        "vars" => "id=1",
//    ),
//    "2" => array(
//        "table" => "page_module",
//        "page" => "edit",
//        "vars" => "id=1",
//    ),
//);

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

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "page_edit", $MAIN->GetAdminPageName("page", "edit"), $entity->ViewEditEx(__FILE__));

    if($entity->identity && $entity->GetHeader("page_type") == "2")
    {
        $GLOBALS["page_id"] = $_GET["id"];

        $page_module_list = new CEntityList(
            array(
                "table" => "page_module",
                "table_parent" => "page",
                "key_parent" => "page_id",
                "parent_id" => $GLOBALS["page_id"],
            )
        );

        $page_module_list_view = "";
        $page_module_list_view .= <<<EOT
            <a href="page_module_edit.php?page_id={$GLOBALS["page_id"]}">Добавить модуль</a>
EOT;

        $page_module_list_view .= $page_module_list->ViewEditListEx(
            __FILE__,
            array(
                "keys"=>array("page_module_name", "page_module_type", "page_module_isshow"),
                "actions"=>array("edit","up","down"),
            )
        );

        //error_log(print_r($page_module_list_view, true));


        $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "page_module_list", $MAIN->GetAdminPageName("page_module", "list"),  $page_module_list_view);
    }


    return $ret;
}
exit;

$MAIN->IncludeModule("header.inc.php", true);
//include("boxes/top.inc.php");
?>

<?
$MAIN->AdminShowMessages();
?>

<div class="box">
    <div class="box-header"><a onclick='xajax_show_hide_change("page_edit")'><span id="page_edit_status">-</span> <?=$MAIN->GetAdminPageName("page", "edit")?></a></div>
    <div id="page_edit" class="box-content"><?=$entity->ViewEdit()?></div>
</div>


<?

if($entity->identity && $entity->GetHeader("page_type") == "2")
{
    // модульная страница
    ?>
<div class="box">
    <div class="box-header"><a onclick='xajax_show_hide_change("page_module_list")'><span id="page_module_list_status">-</span> Список модулей</a></div>
    <div id="page_module_list" class="box-content"><?$MAIN->IncludeFile("page_module_list.php", true);?></div>
</div>
<?
}
?>
<script type="text/javascript">
    <!--
    xajax_show_hide_current("page_edit");
    xajax_show_hide_current("page_module_list");
    //-->
</script>
<?
$MAIN->IncludeModule("footer.inc.php", true);
?>
