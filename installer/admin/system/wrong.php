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
$admin_current_name_custom = $MAIN->GetLangMessage("WRONG_AUTH_REQUIRED");

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

    global $VN_ADMIN_SIGNIN, $backurl;
    $VN_ADMIN_SIGNIN = VN_ADMIN_SIGNIN;
    $backurl = "";
    if(isset($_REQUEST["backurl"]))
        $backurl = "?backurl=".urlencode($_REQUEST["backurl"]);

    $ret .= <<<EOT
<h3>{lang_message:WRONG_AUTH_REQUIRED}</h3>
<a href="{variable:VN_ADMIN_SIGNIN}{variable:backurl}">{lang_message:WRONG_AUTH_SIGNIN}</a>
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


global $admin_current_name_custom;
$admin_current_name_custom = "Authorization is required";

$MAIN->IncludeModule("header.inc.php", true);

?>

<h3>Authorization is required!</h3>

<?
$MAIN->IncludeModule("footer.inc.php", true);
?>