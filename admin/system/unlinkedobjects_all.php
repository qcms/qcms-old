<?
/*
 * Отображение списка непривязанных объектов
 * */
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
$MAIN->Init();

$admin_current_name_custom = $MAIN->GetAdminPageName("unlinkedobjects", "all");

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


    $params =  $MAIN->GetParams();

    foreach($params as $table_name => $table_params)
    {
        // проверка на суперадмина
        if(isset($table_params["superadmin"])
            && $table_params["superadmin"] == "true"
            && !$MAIN->adminuser->IsSuperadmin())
        {
            // объекты с доступом superadmin не показываем
            continue;
        }

        if(!isset($table_params["hierarchy"])
            || !isset($table_params["hierarchy"]["parent"])
            || !isset($table_params["hierarchy"]["parent_table"])
            || !isset($table_params["hierarchy"]["parent_field"])
            || $table_params["hierarchy"]["parent"] != "true"
            || !strlen($table_params["hierarchy"]["parent_table"])
            || !strlen($table_params["hierarchy"]["parent_field"])
        )
        {
            continue;
        }

        $list = new CEntityList(
            array(
                "table" => $table_name,
                "where" => "`{$table_params["hierarchy"]["parent_field"]}` IS NULL OR `{$table_params["hierarchy"]["parent_field"]}`='0'",
            )
        );

        $list_content = $list->ViewEditListEx(
            __FILE__,
            array(
                "keys"=>array("{$table_name}_name", "{$table_name}_isshow"),
                "actions"=>array("edit", "delete"),
            )
        );

        $ret .= $MAIN->AdminShowBoxTemplate(__FILE__, "", "{$table_name}_list", $MAIN->GetAdminPageName("{$table_name}", "list"), $list_content);

        unset($list);

    }

    unset($params);


    return $ret;
}

?>