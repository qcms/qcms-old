<?
/*
 * модуль параметров навигации
 * при вызове модуля изменяется переменная $_SESSION["file"]["navc"]
 * */
$root = "";

// необходимые файлы
require($root."configuration/configuration.inc.php");
require($root."includes/navigation_class.inc.php");

$object = "";

if(isset($_GET["object"]) && $_GET["object"])
{
    $object = urldecode($_GET["object"]);
}


session_start();
session_id($_GET["sid"]);

$session_last_query = "";
if(isset($_SESSION["session_last_query"]) && $_SESSION["session_last_query"])
{
    $session_last_query = $_SESSION["session_last_query"];
}

$session_key = CNavigationPager::GetSessionKey($object, $session_last_query);

//error_log($session_key);


$_SESSION[$session_key]["navc"] = $_GET["navc"];

$_SESSION[$session_key]["navc"] = $_GET["navc"];
if(isset($_GET["backurl"]) && $_GET["backurl"])
{
    header("Location: " . urldecode($_GET["backurl"]));
}
else
{
    header("Location: " . $_SESSION["session_last_query"]);
}

?>