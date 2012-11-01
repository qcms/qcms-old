<?
$root = "../";
// обязательные файлы
include($root . "configuration/configuration.inc.php");
include($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => false,
        "need_session_db" => false,
        "session_last_query"=>false,
//        "is_admin"=>true
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init();

//var_dump($_SERVER['REQUEST_URI']);

$dir = "sounds";
$number = "0";
$prefix = SOUND_FILE_PREFIX;

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

$basename_mp3 = basename($_SERVER['REQUEST_URI']);


if(preg_match("/^(\\d+)\\.mp3$/ims",$basename_mp3,$matches))
{
    $number = $matches[1];

    //$basename = basename($_SERVER['REQUEST_URI']);
    $filename = $root.$dir."/".$prefix.$number.".mp3";
    $basename = basename($filename);
    $start = 0;

    if(file_exists($filename))
    {
        $size = filesize($filename);
        $ext = "mp3";
        header ("HTTP/1.1 200 OK", true);
        header ("Status: 200 OK");
        header ("Accept-Ranges: bytes");
        header ("Content-Length: " . $size);
        header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
        header ("Content-Type: audio/x-".$ext);
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
elseif(preg_match("/^([^\\-]+)\\-([^\\-]+)\\-(\\d+)\\.mp3$/ims",$basename_mp3,$matches))
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

        if($MAIN->GetTableFieldParam($table, $field, "type") == "sound"
            && $entity->identity)
        {
            //error_log(print_r($action, true));
            $sound = $entity->GetSound($field);
            if($sound->sound_id)
            {
                $sound->view_sound();
            }
        }
    }
}


?>