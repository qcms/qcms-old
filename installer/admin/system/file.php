<?
  //===========================================================================
  // скрипт удаления картинки из БД и удаления ссылки на эту картинку
  // обязательные файлы

  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");


  $MAIN = new CMain($root,array("need_config" => false, "need_session" => true, "session_last_query" => false, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init();
  

  $message = "";
  
  //===========================================================================
  // обработка
  if( !isset($_GET["id"]) || !$_GET["id"] ){
    echo "query error";
    exit;
  }
  // экземпляр файла
  $file = new FileClass($_GET["id"]);
  
  if(isset($_GET["action"]))
  {
  	if($_GET["action"] == "delete")
  	{
  		// удаление
		  if( $file->file_id )
		  {
		    // удалим картинку
		    $file->delete_file();
		    //$message = "Файл удален!";
		  }
		  //var_dump($MAIN->GetSessionLastQuery());exit;
			header("Location: " . $MAIN->GetSessionLastQuery());
		  exit;
  	}
  	else if($_GET["action"] == "file")
  	{
  		// скачивание
  		echo $file->view_file(null, true);
  	}
  	
  }
  else
  {
  	// просто отдаем файл
  	echo $file->view_file();
  }
?>