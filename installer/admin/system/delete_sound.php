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

  // экземпляр объекта
  $sound = new SoundClass($_GET["id"]);
  if( $sound->sound_id )
  {
    // удалим sound
    $sound->delete_sound();
  }

  // redirect
  if($_GET["backurl"])
  {
    header("Location: " . urldecode( $_GET["backurl"] ) );
    exit;
  }

  $MAIN->IncludeModule("footer.inc.php", true);
?>