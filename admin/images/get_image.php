<?
  $root = "../../";
  // обязательные файлы
  include($root . "configuration/configuration.inc.php");
  include($root . "includes/functions.inc.php");

  //var_dump($_SERVER['REQUEST_URI']);exit;

  $basename = basename($_SERVER['REQUEST_URI']);
  
  //var_dump($basename);exit;
  //$filename = $root.DIRNAME_IMAGES."/".$basename;
  
  $filename = str_replace(VN_ADMIN, "", $_SERVER['REQUEST_URI']);
  $filename = substr($filename, 1);
  $filename = $root.$filename;
  //var_dump($filename);exit;
  
  $start = 0;

  if(file_exists($filename))
  {
    $size = filesize($filename);
    $ext = "jpg";
    if(preg_match("/\\.(png|gif|jpg|jpeg)$/",$basename,$matches))
    {
      $ext = $matches[1];
      //var_dump($matches);
      //exit;
    }
    //var_dump(filetype($root.DIRNAME_IMAGES."/".$basename));
    //var_dump(filesize($root.DIRNAME_IMAGES."/".$basename));
    //var_dump(file($root.DIRNAME_IMAGES."/".$basename));

    header ("HTTP/1.1 200 OK", true);
    header ("Status: 200 OK");
    header ("Accept-Ranges: bytes");
    header ("Content-Length: " . $size);
    header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
    header ("Content-Type: image/".$ext);
    header ('Content-Disposition: inline; filename="' . $basename  . '"');

    echo file_get_contents($filename);
    exit;

  }



?>