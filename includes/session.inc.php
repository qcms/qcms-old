<?
	global $MAIN, $session, $user, $user_id;

	//error_log("session.inc.php is working");
	//error_log("MAIN:in session: ".print_r($MAIN,true));
	
	//error_log("_COOKIE: ".print_r($_COOKIE,true));
	
	// starting a session
	// init session_id
	if(isset($_COOKIE[SESSION_COOKIE_ID]) && $_COOKIE[SESSION_COOKIE_ID])
	{
		session_id($_COOKIE[SESSION_COOKIE_ID]);
		//var_dump(session_id());
		//exit;
	}
	
	if(!session_id())
	{
		// starting a session
		session_start();
	}
	
	//error_log('$_SESSION: '.print_r($_SESSION,true));
	
//var_dump($_COOKIE[SESSION_COOKIE_ID]);

	if($MAIN->need_session_db)
	{
		$session = new CEntity(
			array(
				"table" => "session",
				"id" => session_id(),
				"index_suffix" => "ln"
			)
		);
	
		if($session->identity)
		{
			//error_log(print_r($session, true));
			
			// инициализируем параметры сохраненной сессии
			if(!isset($_SESSION["user_id"])
			|| !$_SESSION["user_id"]
			&& $session->GetHeader("user_id"))
			{
				//var_dump($session->headers["user_id"]);
				$_SESSION["user_id"] = $session->GetHeader("user_id");
			}
	
			$_SESSION["session_last_query"] = $session->GetHeader("session_last_query");
			$_SESSION["session_isremember"] = $session->GetHeader("session_isremember");
			$_SESSION["session_datetime"] = $session->GetDate("session_datetime");
	
			if(!isset($_SESSION["lang"]) ||  $_SESSION["lang"] == "")
			{
				$_SESSION["lang"] = $session->GetHeader("session_lang");
			}
		}
		else
		{
			// новая сессия
			$_SESSION["lang"] = LANGUAGE_DEFAULT;
			$_SESSION["session_datetime"] = date("Y-m-d H:i:s");
			$_SESSION["session_last_query"] = $_SERVER["REQUEST_URI"];
			$_SESSION["user_id"] = NULL;
			$_SESSION["session_isremember"] = 0;
		}
	}

	// datetime of last access
	$_SESSION["session_datetime"] = date("Y-m-d H:i:s");

	if(isset($_SESSION["var"]) && isset($_SESSION["var"]['REQUEST_URI']) )
	{
		$temp = $_SESSION["var"][$_SERVER['REQUEST_URI']];
		unset($_SESSION["var"]);
		$_SESSION["var"][$_SERVER['REQUEST_URI']] = $temp;
	}



	if(!isset($_SESSION["lang"]) ||  $_SESSION["lang"] == "")
	{
		// язык по умолчанию
		$_SESSION["lang"] = LANGUAGE_DEFAULT;
	}

	if($MAIN->session_last_query/*!isset($not_session_last_query)
	|| (isset($not_session_last_query) && $not_session_last_query==false)*/ )
	{
		session_last_query();
	}


	if($MAIN->need_session_db)
	{
		session_save($session);

		if(!$MAIN->is_admin)
		{
			$user_id = $session->GetHeader("user_id");
			$user = new CEntity(
				array(
					"table" => "user",
			    "id" => $user_id,
				)
			);
		}
	    
	  session_delete_old_entities();
  
	}


/**
 * Функция сохраняет текущее состояние текущей сессии в БД
 */
function session_current_save()
{
	global $session;
	session_save($session);
}
  
  
/**
 * Функция сохраняет текущее состояние сессии в БД
 * @param CEntity $session
 */
function session_save($session)
{
	if(is_a($session,"CEntity"))
	{
    $session->Save(
    	array(
    		"session_ln" => session_id(),
        "session_lang" => $_SESSION["lang"],
        "session_last_query" => $_SESSION["session_last_query"],
        "session_isremember" => $_SESSION["session_isremember"],
        "user_id" => $_SESSION["user_id"]?$_SESSION["user_id"]:"0",
    		"session_datetime" => date("Y-m-d H:i:s")
    	)
    );
	}
	
}
  
  
//-----------------------------------------------------------------------------  
// Функция устанавливает в сессии место текущего положения  
function session_last_query()
{
	$_SESSION["session_last_query"] = $_SERVER["REQUEST_URI"];  
}

//-----------------------------------------------------------------------------
// Функция удаления старых сессий   
function session_delete_old_entities()
{
  // удалим старые сессии
  $query_session = "DELETE FROM ".DATABASE_PREFIX."session WHERE DATE_ADD(session.session_datetime, INTERVAL ".SESSION_DELETE_INTERVAL.") <= '". date("Y-m-d H:i:s") ."'";
  $db_session = new CDatabase();
  $db_session->Query($query_session);
}
?>