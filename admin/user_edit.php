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
$MAIN->Init("user");


$entity = new CEntity(
    array(
        "table"=>"user",
        "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
);


// обработка действий
if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"user",
        )
    );
    $list->MoveUp($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("user", "all"));
    exit;
}
if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
{
    $list = new CEntityList(
        array(
            "table"=>"user",
        )
    );
    $list->MoveDown($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("user", "all"));
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
    header("Location: " . $MAIN->GetAdminPageFile("user", "all") );
    exit;
}


$admin_current_name_custom = $MAIN->GetAdminPageName("user", "edit");

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
        "user_edit",
        $MAIN->GetAdminPageName("user", "edit"),
        $entity->ViewEditEx(__FILE__)
    );


    return $ret;
}
?>