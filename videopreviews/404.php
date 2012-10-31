<?
$root = "../";
// обязательные файлы
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_session" => false));
$MAIN->HeaderIncludes();
$MAIN->Init();

$table = NULL;
$field = NULL;
$number = NULL;

$basename_file = basename($_SERVER['REQUEST_URI']);
if(preg_match("/([^\\-]+)\\-([^\\-]+)\\-(\\d+)\\.jpg$/ims",$basename_file,$matches))
{
    $table = $matches[1];
    $field = $matches[2];
    $number = $matches[3];

    if($table && $field && $number)
    {
        $entity = new CEntity(
            array(
                "table" => $table,
                "id" => $number,
                //"where" => "${table}_isshow = '1'",
            )
        );

        if($MAIN->GetTableFieldParam($table, $field, "type") == "image"
            && $entity->identity)
        {
//            error_log("/videopreviews/404.php");
//            error_log(print_r($action, true));

            $preview = $entity->GetImage($field);
            if($preview->image_id)
            {
                $preview->view_image();
                exit;
            }
        }
    }
}

$filename = $root."/videopreviews/table-field-0.jpg";
//error_log("QQQQQQQQQQQQQQQQQ");
//error_log($root."videopreviews/table-field-0.jpg");
//error_log(__FILE__);

header ("HTTP/1.0 200 OK", true);
header ( "Content-type: image/jpeg", true);
header ( 'Content-Disposition: inline; filename="' . basename($_SERVER["REQUEST_URI"])  . '"', true);
header ( 'Accept-Ranges: bytes', true);
header ( 'Content-Length: ' . filesize($filename), true);
readfile($filename);
exit;
?>