<?
$root = "../";
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

global $MAIN;

$MAIN = new CMain($root,
    array(
        "is_admin"=>true
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init("module");


$entity = new CEntity(
    array(
        "table"=>"module",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"module",
            "pagecount"=>10
        )
    );
    $list->MoveUp($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("module", "all"));
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"module",
            "pagecount"=>10
        )
    );
    $list->MoveDown($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("module", "all"));
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
    header("Location: " . $MAIN->GetAdminPageFile("module", "all") );
    exit;
}


$admin_current_name_custom = $MAIN->GetAdminPageName("module", "edit");

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

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__,
        "",
        "module_edit",
        $MAIN->GetAdminPageName("module", "edit"),
        $entity->ViewEditEx(__FILE__)
    );


    return $ret;
}
exit;


$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => false, "is_admin" => true));
$MAIN->HeaderIncludes();
$MAIN->Init("module");


$entity = new CEntity(
    array(
        "table"=>"module",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"module",
            "pagecount"=>10
        )
    );
    $list->MoveUp($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("module", "all"));
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"module",
            "pagecount"=>10
        )
    );
    $list->MoveDown($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("module", "all"));
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
    header("Location: " . $MAIN->GetAdminPageFile("module", "all") );
    exit;
}

$MAIN->IncludeModule("header.inc.php", true);
?>

<?
$MAIN->AdminShowMessages();
?>
Редактирование модуля сайта

<?=$entity->ViewEdit()?>

<?
$MAIN->IncludeModule("footer.inc.php", true);
?>
