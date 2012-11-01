<?
  //===========================================================================
  // скрипт удаления картинки из БД и удаления ссылки на эту картинку
  // обязательные файлы

  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");


  $MAIN = new CMain($root,array("need_config" => false, "is_admin" => true));
  $MAIN->HeaderIncludes();
  $MAIN->Init();

  $MAIN->IncludeModule("header.inc.php", true);
  
  //===========================================================================
  // обработка формы
  if( !isset($_GET["id"]) || !$_GET["id"] ){
    echo "query error";
    exit;
  }

  // экземпляр изображения
  $video = new VideoClass($_GET["id"]);
  if( $video->video_id )
  {
    // удалим video
    $video->delete_video();

    // удалим ссылку на video
    /*
    if( isset($_GET["table"]) && $_GET["table"]
     && isset($_GET["field"]) && $_GET["field"] )
    {
      mysql_query("
UPDATE " . DATABASE_PREFIX . $_GET["table"] . "
SET " . $_GET["field"] . "='0'
WHERE " . $_GET["field"] . "='" . $_GET["id"] . "'
");
    }
    */
  }

  // redirect
  if($_GET["backurl"])
  {
    header("Location: " . urldecode( $_GET["backurl"] ) );
    exit;
  }

  $MAIN->IncludeModule("footer.inc.php", true);
?>