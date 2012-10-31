<?
/*
 * Обоработка ошибки 404
 */
$root = "../";
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

if(preg_match('/\/$/ims', $_SERVER["REQUEST_URI"]))
{
    $_SERVER["REQUEST_URI"] .= "index.php";
}

$filename = basename(preg_replace('/\?.*$/ims', '', $_SERVER["REQUEST_URI"]));
$filename = str_replace("\/", "/", $filename);
//error_log('404.php: $filename:'.$filename);

$dirname = dirname(preg_replace('/\?.*$/ims', '', $_SERVER["REQUEST_URI"]));
//error_log('404.php: $dirname: '.$dirname);
$dirname = str_replace("\/", "/", $dirname);
//error_log('404.php: $dirname: '.$dirname);

$include_filename = $root . $dirname."/".DIRNAME_SYSTEM."/".$filename;
$include_filename = str_replace("//", "/", $include_filename);
$include_filename = str_replace("\/", "/", $include_filename);

//error_log('404.php: $_SERVER["REQUEST_URI"]: '. $_SERVER["REQUEST_URI"]);
//error_log('404.php: $include_filename: '.$include_filename);

if(file_exists($include_filename))
{
    //error_log('404.php: file_exists($include_filename): $include_filename: '.$include_filename);
    include_once($include_filename);
    exit;
}


$MAIN = new CMain($root,array("need_config" => false, "need_session" => false, "need_session_db" => false, "session_last_query"=>false, "is_admin"=>true,));
$MAIN->HeaderIncludes();
$MAIN->Init();

$MAIN->IncludeModule("header.inc.php", true);

?>
Административный интерфейс сайта <?=VN_SERVER.VN_DIR?>:<br>

<h1>Страница не найдена</h1>
<h2>Error 404</h2>

<?
$MAIN->IncludeModule("footer.inc.php", true);
?>
