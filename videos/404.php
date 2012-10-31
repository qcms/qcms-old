<?
$root = "../";

// обязательные файлы
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_session" => false));
$MAIN->HeaderIncludes();
$MAIN->Init();


$dir = "videos";
$number = "0";
$prefix = VIDEO_FILE_PREFIX;

/*
if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"])
{
  // из реферера вытащим имя плеера и номер записи
  $referer_basename = basename($_SERVER["HTTP_REFERER"]);
  if(preg_match("/^music_player_(\\d+)\\.swf$/",$referer_basename,$matches))
  {
    $number = $matches[1];
  }
}
*/

$basename_flv = basename($_SERVER['REQUEST_URI']);
if(preg_match("/^(\\d+)\\.flv$/ims",$basename_flv,$matches))
{
    $number = $matches[1];

    //$basename = basename($_SERVER['REQUEST_URI']);
    $filename = $root.$dir."/".$prefix.$number.".flv";
    $basename = basename($filename);
    $start = 0;

    if(file_exists($filename))
    {
        $size = filesize($filename);
        $ext = "flv";
        header ("HTTP/1.1 200 OK", true);
        header ("Status: 200 OK");
        header ("Accept-Ranges: bytes");
        header ("Content-Length: " . $size);
        header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
        header ("Content-Type: video/x-".$ext);
        header ('Content-Disposition: inline; filename="' . $basename  . '"');

        $handle = fopen($filename, "rb");
        $contents = '';
        while (!feof($handle)) {
            echo fread($handle, 8192);
        }
        fclose($handle);


        //echo file_get_contents($filename);
        exit;

    }
}
elseif(preg_match("/^([^\\-]+)\\-([^\\-]+)\\-(\\d+)\\.flv$/ims",$basename_flv,$matches))
{
    //error_log(print_r($matches,true));
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

        if($MAIN->GetTableFieldParam($table, $field, "type") == "video"
            && $entity->identity)
        {
            //error_log(print_r($action, true));
            $video = $entity->GetVideo($field);
            if($video->video_id)
            {
                $video->view_video();
            }
        }
    }
}






?>