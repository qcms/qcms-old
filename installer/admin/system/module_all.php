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

$admin_current_name_custom = $MAIN->GetAdminPageName("module", "all");

$MAIN->ShowTemplate(__FILE__, "", "page", "page.php", true);

/**
 * Функция отображения страницы административного интерфейса
 * возвращает контекст страницы административного интерфейса
 * @return string
 */
function admin_show_content()
{
    global $MAIN;
    $ret = "";

    $list = new CEntityList(
        array(
            "table"=>"module",
            //"pagecount"=>10,
        )
    );

    $list_content = $list->ViewEditListEx(
        __FILE__,
        array(
            "keys" => array("module_name", "module_ln", "module_isshow"),
            "actions" => array("edit", "up", "down"),
        )
    );

    $add_link_url = $MAIN->GetAdminPageUrl("module", "edit");
    $add_link = <<<EOT
<a href="{$add_link_url}">Добавить модуль</a>
<br />
EOT;

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__,
        "",
        "module_all",
        $MAIN->GetAdminPageName("module", "all"),
        $add_link.$list_content
    );



    return $ret;
}

?>