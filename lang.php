<?
//-----------------------------------------------------------------------------
// модуль смены языка
// при вызове модуля изменяется переменная $_SESSION["lang"] на значение
// кода языка из двух букв по умолчанию RU, есть еще EN
//-----------------------------------------------------------------------------

    $root = "";
    
    // необходимые файлы
	  include_once($root . "configuration/configuration.inc.php");
	  include_once($root . "includes/main_class.inc.php");
	
	
	  $MAIN = new CMain($root,array("need_config" => false, "session_last_query" => false, "need_session_db"=> false));
	  $MAIN->HeaderIncludes();
	  $MAIN->Init();

    unset($_SESSION["lang"]);
    // изменим значение в сессии
    $_SESSION["lang"] = $_GET["id"];
   
    if(isset($_GET["backurl"]) && $_GET["backurl"])
    {
	    header("Location: " . urldecode($_GET["backurl"]));
			exit;
    }
    else
    {
	    if(isset($_SESSION["session_last_query"]) && $_SESSION["session_last_query"])
	    {
	        header("Location: " . $_SESSION["session_last_query"]);
	        exit;
	    }
	    else
	    {
	        header("Location: " . VN_INDEX);
	        exit;
	    }
    }
?>