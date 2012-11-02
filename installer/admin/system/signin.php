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

$MAIN->IncludeModule("signin_action.inc.php", true);

$admin_current_name_custom = $MAIN->GetLangMessage("SIGNIN_CAPTION");

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


    $backurl = "";
    if(isset($_REQUEST["backurl"]))
        $backurl = urlencode($_REQUEST["backurl"]);

    $ret .= <<<EOT
<div>
    <h3>{lang_message:SIGNIN_CAPTION}</h3>
    <form enctype="multipart/form-data" method="post">
        <div class="f-row">
            <label for="admin_login">{lang_message:SIGNIN_LOGIN}:</label>
            <div class="f-input">
                <input class="g-3" type="text" name="admin_login" id="admin_login" value="" />
            </div>
        </div>
        <div class="f-row">
            <label for="admin_password">{lang_message:SIGNIN_PASSWORD}:</label>
            <div class="f-input">
                <input class="g-3" type="password" name="admin_password" id="admin_password" value="" />
            </div>
        </div>
        <div class="f-actions">
            {if:backurl}
                <input type="hidden" name="backurl" value="{variable:backurl}" />
            {endif:backurl}
            <input class="f-bu" type="submit" value="{lang_message:SIGNIN_SIGNIN_BUTTON}" />
            <input class="f-bu" type="reset" value="{lang_message:SIGNIN_RESET_BUTTON}" />
        </div>
    </form>

</div>
EOT;


    return $ret;
}

exit;


$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain(
    $root,
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


$MAIN->IncludeModule("signin_action.inc.php", true);


global $admin_current_name_custom;
$admin_current_name_custom = "Signin";

$MAIN->IncludeModule("header.inc.php", true);

$MAIN->IncludeModule("signin.inc.php", true);

$MAIN->IncludeModule("footer.inc.php", true);
?>