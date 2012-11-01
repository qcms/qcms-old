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

$admin_current_name_custom = $MAIN->GetAdminPageName("user", "all");

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
            "table"=>"user",
        )
    );


    $list_content = $list->ViewEditListEx(
        __FILE__,
        array(
            "keys" => array("user_email", "user_surname", "user_name"),
            "actions" => array("edit", ),
        )
    );

    $add_link_url = $MAIN->GetAdminPageUrl("user", "edit");
    $add_link = <<<EOT
<a href="{$add_link_url}">Добавить пользователя</a>
<br />
EOT;

    $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "user_all", $MAIN->GetAdminPageName("user", "all"), $add_link.$list_content);



    return $ret;
}
?>