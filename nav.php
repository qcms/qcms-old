<?
//-----------------------------------------------------------------------------
// модуль параметров навигации
// при вызове модуля изменяется переменная $_SESSION["file"]["navc"] 
//-----------------------------------------------------------------------------


  // необходимые файлы
  require("configuration/configuration.inc.php");
  //require("includes/functions.inc.php");
  //require("includes/application_top.inc.php");

  //require("includes/image_class.inc.php");
  //require("includes/navigation_class.inc.php");
  //require("includes/entity_class.inc.php");
  //require("includes/session.inc.php");

  // изменим значение в сессии
  //$result = mysql_query("SELECT * FROM ".DATABASE_PREFIX."session WHERE session_id = '" . $_GET["sid"] . "'");

  //if($result && $row = mysql_fetch_assoc($result))
  {
    //mysql_query("UPDATE session SET session_last_use = NOW(), session_lang = '" . $_GET["lang"] . "' WHERE session_id = '" . $row["session_id"] . "';");

    session_start();
    session_id($_GET["sid"]);

	  $_SESSION[$_SESSION["session_last_query"]]["navc"] = $_GET["navc"];
    if(isset($_GET["backurl"]) && $_GET["backurl"])
    {
	    header("Location: " . urldecode($_GET["backurl"]));
    }
    else 
    {
	    header("Location: " . $_SESSION["session_last_query"]);
    }
  }

?>