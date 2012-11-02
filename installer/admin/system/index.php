<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => true,
        "need_session_db" => false,
        "is_admin"=>true
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init();
$MAIN->LoadLangMessages(__FILE__);

$admin_current_name_custom = $MAIN->GetAdminPageName("index", "all");

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

    $VN_SERVER = VN_SERVER;

$ret .= <<<EOT
{lang_message:INDEX_HEADER} {$VN_SERVER}:<br />


<ul class="index">
EOT;

    global $params;
    if(!isset($params))
    {
        $params = $MAIN->GetParams();
    }

    // выведем список страниц
    foreach($params as $key => $value)
    {
        if(isset($value["admin"]["all"]["file"])
            && isset($value["admin"]["all"]["name"])
            && is_array($value["admin"]["all"]["name"])
        )
        {
            if(isset($value["superadmin"]) && $value["superadmin"] == "true"
                && !$MAIN->adminuser->IsSuperadmin())
            {
                continue;
            }


            $name = $MAIN->GetCurrentArrayLang($value["admin"]["all"]["name"]);
            $ret .= <<<EOT

            <li><a href="{$value["admin"]["all"]["file"]}">{$name}</a></li>
EOT;
        }
    }
    $ret .= <<<EOT
</ul>
EOT;

    return $ret;
}

//$MAIN->IncludeModule("header.inc.php", true);

// для изменения главной страницы нужно скопировать файл '/admin/boxes/system/index.inc.php'
// в  '/admin/boxes/index.inc.php' и изменить его
//$MAIN->IncludeModule("index.inc.php", true);

//$MAIN->IncludeModule("footer.inc.php", true);
?>
