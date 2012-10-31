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
$MAIN->Init("page");

$admin_current_name_custom = $MAIN->GetAdminPageName("page", "all");

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
            "table"=>"page",
            "table_parent"=>"page",
            "key_parent"=>"page_parent",
        )
    );


    $list_content = $list->ViewEditListEx(
        __FILE__,
        array(
            "keys" => array("page_name", "page_type", "page_isshow"),
            "actions" => array("edit", "up", "down"),
        )
    );

    $add_link_url = $MAIN->GetAdminPageUrl("page", "edit");
    $add_link = <<<EOT
<a href="{$add_link_url}">Добавить страницу</a>
<br />
EOT;

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "page_all", $MAIN->GetAdminPageName("page", "all"), $add_link.$list_content);



    return $ret;
}
?>