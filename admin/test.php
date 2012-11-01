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
$MAIN->Init();

$admin_current_name_custom = "admin_current_name_custom";

$breadcrumb_parents = array(
    "0" => array(
        "table" => "page",
        "page" => "all",
        "vars" => ""
    ),
    "1" => array(
        "table" => "config",
        "page" => "all",
        "vars" => ""
    ),
);

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

    global $test;
    $test = print_r($MAIN,true);

    $ret .= <<<EOT
Тестовый контент.
<pre>
{variable:test}
</pre>
{constant:VN_SERVER}
{constant:DIRNAME_ADMIN}
EOT;
    //$ret = $MAIN->ShowTemplateContent($ret);


    return $ret;
}
?>