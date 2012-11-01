<?
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

//var_dump($_SERVER['REQUEST_URI']);
$MAIN = new CMain($root,array("need_session" => false));
$MAIN->HeaderIncludes();
$MAIN->Init();

$dir = "playervideo/";

$basename = basename($_SERVER['REQUEST_URI']);
$filename = $root.$dir."playervideo.swf";
$start = 0;

if(file_exists($filename))
{
    $size = filesize($filename);
    //var_dump(filetype($root.DIRNAME_IMAGES."/".$basename));
    //var_dump(filesize($root.DIRNAME_IMAGES."/".$basename));
    //var_dump(file($root.DIRNAME_IMAGES."/".$basename));

    header ("HTTP/1.1 200 OK", true);
    header ("Status: 200 OK");
    header ("Accept-Ranges: bytes");
    header ("Content-Length: " . $size);
    header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
    header ("Content-Type: application/x-shockwave-flash");
    header ('Content-Disposition: inline; filename="' . $basename  . '"');

    echo file_get_contents($filename);
    exit;
}



?>