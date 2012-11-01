<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => true,
        "need_session_db" => false,
        "need_xajax" => false,
        "is_admin" => true,
        "is_admin_non_auth" => true,
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init();

$MAIN->LoadLangMessages(__FILE__);

$MAIN->IncludeModule("signout_action.inc.php", true);

$admin_current_name_custom = $MAIN->GetLangMessage("SIGNOUT_CAPTION");

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

    global $VN_SERVER;
    $VN_SERVER = VN_SERVER;
    $ret .= <<<EOT
<h3>
{lang_message:SIGNOUT_TEXT} {constant:VN_SERVER}!
</h3>
EOT;

    return $ret;
}


exit;


$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => true,
        "need_session_db" => false,
        "need_xajax" => false,
        "is_admin" => true,
        "is_admin_non_auth" => true
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init();


$MAIN->IncludeModule("signout_action.inc.php", true);


global $admin_current_name_custom;
$admin_current_name_custom = "SignOut";

$MAIN->IncludeModule("header.inc.php", true);

$MAIN->IncludeModule("signout.inc.php", true);

$MAIN->IncludeModule("footer.inc.php", true);
?>