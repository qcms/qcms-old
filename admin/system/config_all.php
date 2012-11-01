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
$MAIN->Init("config");

$entity = new CEntity(
    array(
        "table"=>"config",
        "id"=>1
    )
);

if($MAIN->CheckPost())
{
    $entity->SavePost();
    // redirect
    $MAIN->Redirect($MAIN->QueryStringWithoutParams());
}

$admin_current_name_custom = $MAIN->GetAdminPageName("config", "all");

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

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "",
        "config_all",
        $MAIN->GetAdminPageName("config", "all"),
        $entity->ViewEditEx(__FILE__)
    );



    return $ret;
}

?>