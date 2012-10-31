<?
//=============================================================================
// Общие функции и переменные
// Разработка: Fedor Belyaev
// Компания: Information Ways
// Сайт: www.infoways.ru

//-----------------------------------------------------------------------------
// Глобальные переменные
$connection = NULL; // подключение к БД


function get_debug_print_backtrace($traces_to_ignore = 1)
{
    $traces = debug_backtrace();
    $ret = array();
    foreach($traces as $i => $call){
        if ($i < $traces_to_ignore ) {
            continue;
        }

        $object = '';
        if (isset($call['class'])) {
            $object = $call['class'].$call['type'];
            if (is_array($call['args'])) {
                foreach ($call['args'] as &$arg) {
                    get_arg($arg);
                }
            }
        }

        $ret[] = '#'.str_pad($i - $traces_to_ignore, 3, ' ')
            .$object.$call['function'].'('.implode(', ', $call['args'])
            .') called at ['.$call['file'].':'.$call['line'].']';
    }

    return implode("\n",$ret);
}

function get_arg(&$arg)
{
    if (is_object($arg)) {
        $arr = (array)$arg;
        $args = array();
        foreach($arr as $key => $value) {
            if (strpos($key, chr(0)) !== false) {
                $key = '';    // Private variable found
            }
            $args[] =  '['.$key.'] => '.get_arg($value);
        }

        $arg = get_class($arg) . ' Object ('.implode(',', $args).')';
    }
}



//-----------------------------------------------------------------------------
// Функция рекурсивного удаления директории
function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
 } 


//-----------------------------------------------------------------------------
// Функция возвращает месяц из даты
function datetime_get_part($datetime,$part)
{
	$ret = $datetime;
	$matches = null;
	if(preg_match("/^(\\d{4})\\-(\\d{2})\\-(\\d{2})\\s(\\d{2})\\:(\\d{2})\\:(\\d{2})$/",$datetime,$matches))
	{
		switch ($part)
		{
			case "year":
				$ret = $matches[1];
				break;
			case "month":
				$ret = $matches[2];
				break;
			case "day":
				$ret = $matches[3];
				break;
			case "hour":
				$ret = $matches[4];
				break;
			case "minute":
				$ret = $matches[5];
				break;
			case "second":
				$ret = $matches[6];
				break;
		}
		
	}
	return $ret;
	
}

//-----------------------------------------------------------------------------
// Функция проверки значения на отсутсвие тэгов
function check_value_no_tags($value)
{
	if($value != strip_tags($value))
	{
		return false;
	}
	return true;
}

//-----------------------------------------------------------------------------
// Функция проверки значения на совместимость с mysql 
// на отсутсвие спецсимволов mysql
function check_value_mysql($value)
{
	if(mysql_real_escape_string($value) != $value)
	{
		//var_dump($value);var_dump(mysql_real_escape_string($value));exit;
		return false;
	}		
	return true;
}


//-----------------------------------------------------------------------------
// Функция вычисляет отличие в датах в днях
// дата передается в формате "Y-m-d"
function date_diff_day($beginDate, $endDate)
{
  $date_parts1=explode("-", $beginDate);
  //var_dump($endDate);
  $date_parts2=explode("-", $endDate);

  // вариант для нормального хостинга
  //$start_date=cal_to_jd(CAL_GREGORIAN, $date_parts1[1], $date_parts1[2], $date_parts1[0]);
  //$end_date=cal_to_jd(CAL_GREGORIAN, $date_parts2[1], $date_parts2[2], $date_parts2[0]);
  //return $end_date - $start_date;


  // специально для valuehost
  return daysbetween($date_parts1[1], $date_parts1[2], $date_parts1[0],
  $date_parts2[1], $date_parts2[2], $date_parts2[0]);

}


//-----------------------------------------------------------------------------
// Функция вычисляет отличие в датах в секундах
// дата передается в формате "Y-m-d H:i:s"
// 2011-01-03 12:12:12 - 2011-01-01 13:13:13 = 1:23:23:59 = 86400+82800+1380+59
// 2011-01-03 10:10:12 - 2011-01-01 10:10:13 = 1:00:00:59 = 86400+82800+1380+59
function datetime_diff($beginDateTime, $endDateTime, $ret_string=false)
{
	$ret = 0;
	
	$beginDateTimeArr = preg_split("/\s/", $beginDateTime);
	$endDateTimeArr = preg_split("/\s/", $endDateTime);
	
	$beginDate = $beginDateTimeArr[0];
	$endDate = $endDateTimeArr[0];

	$days = date_diff_day($beginDate, $endDate);
	
	$beginTime = $beginDateTimeArr[1];
	$endTime = $endDateTimeArr[1];
	
	$beginTimeArr = preg_split("/\:/", $beginTime);
	$endTimeArr = preg_split("/\:/", $endTime);
	
	$hours = $endTimeArr[0] - $beginTimeArr[0];
	$minutes = $endTimeArr[1] - $beginTimeArr[1];
	$seconds = $endTimeArr[2] - $beginTimeArr[2];
	
	if($seconds<0)
	{
		$seconds = 60 + $seconds;
		$minutes--;
	}
	if($minutes<0)
	{
		$minutes = 60 + $minutes;
		$hours--;
	}
	if($hours<0)
	{
		$hours = 24 + $hours;
		$days--;
	}
	

	if($ret_string)
	{
		$ret = 	($days>0?($days.":"):"").($hours<10?"0":"").$hours.":".($minutes<10?"0":"").$minutes.":".($seconds<10?"0":"").$seconds;
		return $ret;
	}
	
	$ret = $seconds + $minutes*60 + $hours*60*60 + $days*24*60*60;
	return $ret;
}



//-----------------------------------------------------------------------------
// специально для valuehost
function daysbetween($m1, $d1, $y1, $m2, $d2, $y2)
{
    $result = 0;

    if( $m1>2 )
    {
        $m1 = $m1+1;
    }
    else
    {
        $m1 = $m1+13;
        $y1 = $y1-1;
    }
    $n1 = 36525*$y1/100+306*$m1/10+$d1;
    if( $m2>2 )
    {
        $m2 = $m2+1;
    }
    else
    {
        $m2 = $m2+13;
        $y2 = $y2-1;
    }
    $n2 = 36525*$y2/100+306*$m2/10+$d2;
    $result = $n2-$n1;
    return floor($result);
}



//-----------------------------------------------------------------------------
// Функция выводит количество дней $days в виде строки "30 дней", "34 дня", "1 день"
function days_count_string($days)
{
  $ret = $days . " ";

  // найдем цифру на конце
  $days_digit_end = substr($days, -1, 1);

  if($days_digit_end != "")
  {
    switch((int)$days_digit_end)
    {
      case 1:
        $ret .= "день";
        break;
      case 2:
      case 3:
      case 4:
        $ret .= "дня";
        break;
      default:
        $ret .= "дней";
        break;
    }

    return $ret;
  }
}


//-----------------------------------------------------------------------------
// Функция показа ошибки при вызове die
// в зависимости от текущих параметров отображения
function die_mysql_error_show($query=null)
{

  $message = "Ошибка в SQL запросе!";

  $ret = "";
  if(ERROR_SHOW <= 0)
    return $ret;

  $ret .= $message . "
";
  if(ERROR_SHOW <= 1)
    return;

//  $arr = error_get_last();
//  $ret .= "type = " . $arr["type"] . "
//";
//  $ret .= "message = " . $arr["message"] . "
//";
//  $ret .= "file = " . $arr["file"] . "
//";
//  $ret .= "line = " . $arr["line"] . "
//";
//
  if(ERROR_SHOW <= 2)
    return $ret;

  $ret .= mysql_errno() . ": " . mysql_error(). "
";

  if(ERROR_SHOW <= 3)
    return $ret;

  if($query)
  {
    $ret .= $query . "
";
  }

  if(ERROR_SHOW <= 4)
    return $ret;

  if(ERROR_SHOW == 9)
  {
    //error_log($ret);

    $ret .= "
      " . print_r(debug_backtrace(), true);
  }
//  debug_print_backtrace();

  return $ret;

}


//-----------------------------------------------------------------------------
// Функция отображения модуля по его мнемокоду
function show_module($module_ln)
{
    global $root, $page;

    $module = new EntityClass(
        "module",
        '<<text:module_text>>',
        "d.m.Y",
        array(),
        array("module_datetime"),
        array("module_ln", "module_link", "module_name","module_isshow"),
        array("module_text"),
        ($module_ln),
        "ln",
        array("module_ln", "module_link", "module_isshow"),
        array("module_text")
    );

    if($module->entity_id && $module->headers["module_isshow"] == "1")
    {
        if(isset($module->headers["module_link"])
        && $module->headers["module_link"]
        && file_exists((isset($root)?$root:"") . "boxes/" . $module->headers["module_link"])
        )
        {
            include((isset($root)?$root:"") . "boxes/" . $module->headers["module_link"]);
        }
        else
        {
            $module->view_entity();
        }
    }
    return;
}


//-----------------------------------------------------------------------------
function R_win2utf ($str)
{
    return preg_replace_callback ('/([\xC0-\xFF\xA8\xB8])/', 'R_win2utf_char', $str);
}

//-----------------------------------------------------------------------------
function R_win2utf_char ($c)
{
    list (,$c) = $c;
    if ($c == "\xA8") return "\xD0\x81";
    if ($c == "\xB8") return "\xD1\x91";

    if ($c >= "\xC0" && $c <= "\xEF")
    return "\xD0" . chr(ord ($c) - 48);

    if ($c >= "\xF0")
    return "\xD1" . chr(ord ($c) - 112);

    return $c;
}


//-----------------------------------------------------------------------------
function cyr_code ($in_text){
$output="";
$other[1025]="Ё";
$other[1105]="ё";
$other[1028]="Є";
$other[1108]="є";
$other[1030]="I";
$other[1110]="i";
$other[1031]="Ї";
$other[1111]="ї";

for ($i=0; $i<strlen($in_text); $i++){
 if (ord($in_text{$i})>191){
  $output.="&#".(ord($in_text{$i})+848).";";
 } else {
  if (array_search($in_text{$i}, $other)===false){
   $output.=$in_text{$i};
  } else {
   $output.="&#".array_search($in_text{$i}, $other).";";
  }
 }
}
return $output;
}


//-----------------------------------------------------------------------------
function unicode_to_utf8( $str ) {

    $utf8 = '';

    foreach( $str as $unicode ) {

        if ( $unicode < 128 ) {

            $utf8.= chr( $unicode );

        } elseif ( $unicode < 2048 ) {

            $utf8.= chr( 192 +  ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );

        } else {

            $utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
            $utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
            $utf8.= chr( 128 + ( $unicode % 64 ) );

        } // if

    } // foreach

    return $utf8;

} // unicode_to_utf8


//-----------------------------------------------------------------------------
function utf8_to_unicode( $str )
{

   $unicode = array();
   $values = array();
   $lookingFor = 1;

   for ($i = 0; $i < strlen( $str ); $i++ ) {
       $thisValue = ord( $str[ $i ] );
   if ( $thisValue < ord('A') ) {
       // exclude 0-9
       if ($thisValue >= ord('0') && $thisValue <= ord('9')) {
            // number
            $unicode[] = chr($thisValue);
       }
       else {
            $unicode[] = '%'.dechex($thisValue);
       }
   } else {
         if ( $thisValue < 128)
       $unicode[] = $str[ $i ];
         else {
               if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
               $values[] = $thisValue;
               if ( count( $values ) == $lookingFor ) {
                   $number = ( $lookingFor == 3 ) ?
                       ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                       ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
           $number = dechex($number);
           $unicode[] = (strlen($number)==3)?"%u0".$number:"%u".$number;
                   $values = array();
                   $lookingFor = 1;
         } // if
       } // if
   }
   } // for
   return implode("",$unicode);
}


//-----------------------------------------------------------------------------
// функция возвращает ссылку на страницу по классу entity_class["page"]
function get_page_link($page)
{
	//var_dump($page);
	if($page->entity_id)
	{
		if(is_a($page,"CEntityEx"))
		{
			//echo "AAAAAAAAAAAAA";
			if(isset($page->headers["page_link"])
				&& $page->headers["page_link"] != null
				&& $page->headers["page_link"] != "")
			{
				return VN_SERVER . VN_DIR . $page->headers["page_link"];
			}
		
			if(isset($page->headers["page_ln"])
				&& $page->headers["page_ln"] != null
				&& $page->headers["page_ln"] != "")
			{
				if($page->headers["page_ln"] == "index")
				{
					return VN_INDEX;
				}
				return VN_PAGE . "?id=" . $page->headers["page_ln"];
			}
		
			return VN_PAGE . "?id=" . $page->identity;
							
		}
		else if(is_a($page,"CEntity"))
		{
			//echo "BBBBBBB";
			return get_page_link($page->entity_ex);
			/*
			if(isset($page->entity->headers["page_link"])
				&& $page->entity->headers["page_link"] != null
				&& $page->entity->headers["page_link"] != "")
			{
				return VN_SERVER . VN_DIR . $page->entity->headers["page_link"];
			}
		
			if(isset($page->entity->headers["page_ln"])
				&& $page->entity->headers["page_ln"] != null
				&& $page->entity->headers["page_ln"] != "")
			{
				return VN_PAGE . "?id=" . $page->entity->headers["page_ln"];
			}
			*/
		}
		else 
		{
			return "";
		}
	}
	else
	{
		//echo "AAAAAAAAAAAAAAAA";
		return "";
	}

	return VN_PAGE . "?id=" . $page->identity;
}

//-----------------------------------------------------------------------------
// функция проверки суперадмина
function is_superadmin($user, $password)
{
  //global $_SERVER;
  if(ADMIN_USER == $user && ADMIN_PASSWORD == $password)
  {
    return true;
  }
  return false;
}

//-----------------------------------------------------------------------------
// Функции работы с языками
//-----------------------------------------------------------------------------
// Функция возвращает код текущего языка
function lang_get()
{
  if(session_id())
  {
    return $_SESSION["lang"];
  }
  return "";
}

//-----------------------------------------------------------------------------
function lang_set($lang)
{
  if(session_id())
  {
    // сессия существует
    $_SESSION["lang"] = $lang;
  }
}

//---------------------------------------------------------------------------
// функция возвращает порядковый номер для языка
function get_lang_num($lang_code)
{
  global $languages;
  if(!$lang_code || !is_array($languages))
  {
    return 0;
  }
  foreach($languages as $language)
  {
    if($language["code"] == $lang_code)
    {
      return $language["num"];
    }
  }

  return 0;
}

//---------------------------------------------------------------------------
// функция возвращает текстовый контент для данного языка
function get_value_lang($value,$lang_code)
{
  global $languages;

  // количество языков
  $languages_count = count($languages);
  if(!$languages_count || $languages_count <= 0)
  {
    return $value;
  }

  $lang_num = get_lang_num($lang_code);
  if(!preg_match("/" . preg_quote(LANGUAGE_SPLITTER) ."/", $value))
  {
    // нет разделителя вернем $value целиком
    return $value;
  }
  
  $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
  if($lang_num < count($values) && $values[$lang_num])
  {
    return $values[$lang_num];
  }
  else
  {
    return $values[0];
  }
    
}

//---------------------------------------------------------------------------  
// функция устанавливает текстовый контент для данного языка
// возвращает результат
function set_value_lang(&$value,$new_value,$lang_code)
{
  global $languages;


  //var_dump($value);

  // количество языков
  $languages_count = count($languages);
  if(!$languages_count || $languages_count <= 0)
  {
    return false;
  }

  $lang_num = get_lang_num($lang_code);
  $value_new = "";
  $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
  $values_count = count($values);
  for($i=0;$i<$languages_count;$i++)
  {
    if($i > 0)
    {
      $value_new .= LANGUAGE_SPLITTER;
    }
    if($i == $lang_num)
    {
      $value_new .= $new_value;
      continue;
    }
    if($i <= $values_count)
    {
      // если есть старое значение то следует установить его
      $value_new .= $values[$i];
    }
  }
  
  $value = $value_new; // вернем через параметры результат


  return true;
}


//-----------------------------------------------------------------------------
// Функция возвращает мнемокод страницы по имени ее файла
function get_page_ln($page_file)
{
  $query = "
SELECT *
FROM page
WHERE 
page_file = '$page_file'
";
  $result = mysql_query($query);
  if($result && $row = mysql_fetch_assoc($result))
  {
    mysql_free_result($result);
    return $row["page_ln"];
  }
  
  return "";
}

////-----------------------------------------------------------------------------
//// Функция возвращает количество уровней между страницей $page_ln 
//// и страницей $parent_page_ln, если страница не является потомком то 0
//// для непосредственного потомка 1
//function get_parent_level($page_parent, $page_ln)
//{
//  $level = 0;
//  $is_found = false;
//  $page = new EntityClass( 
//                "page", 
//                'none', 
//                "d.m.Y H:i:s",
//                array(), 
//                array(),
//                array("page_ln", "page_parent"),
//                array(),
//                $page_ln,
//                "ln",
//                array("page_ln")
//              );
//
//  while($page->entity_id && $page->headers["page_parent"])
//  {
//    $level++;
//    if($page->headers["page_parent"] == $page_parent)
//    {
//      // нашли
//      return $level;
//    }
//
//    // на уровенть вверх
//    $page = new EntityClass( 
//                "page", 
//                'none', 
//                "d.m.Y H:i:s",
//                array(), 
//                array(),
//                array("page_ln", "page_parent"),
//                array(),
//                $page->headers["page_parent"],
//                "ln",
//                array("page_ln")
//              );
//  }
//
//  /*
//  if($is_found)
//  {
//    return $level;
//  }
//  */
//  return 0;
//}


//-----------------------------------------------------------------------------
// Функция возвращает id родителя верхнего уровня для страницы
function get_page_top_parent_id($page)
{
  if(!$page->identity)
  {
    return NULL;
  }
  while($page->identity && $page->headers["page_parent"])
  {
    // на уровенть вверх
    $page = new EntityClass(
      "page",
      'none',
      "d.m.Y H:i:s",
      array(),
      array(),
      array("page_parent"),
      array(),
      $page->headers["page_parent"],
      "id",
      array("page_parent")
    );
  }
  return $page->identity;
}

////-----------------------------------------------------------------------------
//// Функция возвращает мнемокод родителя страницы с мнемокодом $page_ln
//function get_parent_page_ln($page_ln)
//{
//  $page = new EntityClass( 
//            "page", 
//            'none', 
//            "d.m.Y H:i:s",
//            array(), 
//            array(),
//            array("page_ln", "page_file", "page_parent"),
//            array(),
//            $page_ln,
//            "ln",
//            array("page_ln")
//          );
//  if(!$page->entity_id || !$page->headers["page_parent"])
//  {
//    return "";
//  }
//  return $page->headers["page_parent"];
//}


////-----------------------------------------------------------------------------
//// Функция возвращает мнемокод страницы третего уровня 
//function get_parent3_page_ln($page_file,$page_ln=null)
//{
//  if($page_ln)
//  {
////var_dump($page_ln);
//
//    $parent2 = get_parent2_page_ln($page_file,$page_ln);
//
//    
//    $page_parent = get_parent_page_ln($page_ln);
////var_dump($page_parent);
//    $current_page_ln = $page_ln;
//    while($page_parent && $page_parent != $parent2)
//    {
//      $current_page_ln = $page_parent;
//      $page_parent = get_parent_page_ln($page_parent);
//    }
//    return  $current_page_ln;
//  }
//  
//  $page_file1 = get_parent2_page_file($page_file);
//  return get_page_ln($page_file1);
//  /*
//  $query = "
//SELECT *
//FROM page
//WHERE 
//page_file = '$page_file1'
//";
//  $result = mysql_query($query);
//  if($row = mysql_fetch_assoc($result))
//  {
//    return $row["page_ln"];
//  }
//
//  return "";
//  */
//}
//
//
//
//
////-----------------------------------------------------------------------------
//// Функция возвращает мнемокод страницы второго уровня 
//function get_parent2_page_ln($page_file,$page_ln=null)
//{
//  if($page_file && $page_file != "page.php")
//  {
//    $page_ln = get_page_ln($page_file);
//  }
//  if($page_ln)
//  {
////var_dump($page_ln);
//    
//    $page_parent = get_parent_page_ln($page_ln);
////var_dump($page_parent);
//    $current_page_ln = $page_ln;
//    while($page_parent && $page_parent != "main_page")
//    {
//      $current_page_ln = $page_parent;
//      $page_parent = get_parent_page_ln($page_parent);
//    }
//    return  $current_page_ln;
//  }
//  
//  $page_file1 = get_parent2_page_file($page_file);
//  return get_page_ln($page_file1);
//  /*
//  $query = "
//SELECT *
//FROM page
//WHERE 
//page_file = '$page_file1'
//";
//  $result = mysql_query($query);
//  if($row = mysql_fetch_assoc($result))
//  {
//    return $row["page_ln"];
//  }
//  
//  return "";
//  */
//}


////-----------------------------------------------------------------------------
//// Функция возвращает имя файла страницы второго уровня 
//function get_parent2_page_file($page_file)
//{
//  if($page_file == "index.php")
//  {
//    return $page_file;
//  }
//  
//  $current_page_file = $page_file;
//  while($current_page_file != "" && get_parent_page_file($current_page_file) != "index.php")
//  {
//    $current_page_file = get_parent_page_file($current_page_file);
//  }
//  return $current_page_file;
//}
//
////-----------------------------------------------------------------------------
//// Функция возвращает файл страницы предыдущего уровня 
//function get_parent_page_file($page_file)
//{
//  $query = "
//SELECT *
//FROM page
//WHERE 
//page_file = '$page_file'
//";
//  $result = mysql_query($query);
//  if($row = mysql_fetch_assoc($result))
//  {
//    if(!is_null($row["page_parent"]) && $row["page_parent"] != "")
//    {
//      $page = new EntityClass( 
//                "page", 
//                'none', 
//                "d.m.Y H:i:s",
//                array(), 
//                array(),
//                array("page_ln", "page_file"),
//                array(),
//                $row["page_parent"],
//                "ln",
//                array("page_ln")
//              );
//      return $page->headers["page_file"];
//    }
//  }
//  return "";
//}

//-----------------------------------------------------------------------------
// Функция подключения к БД
function db_connect()
{
  global $connection;
  $connection = mysql_connect(MYSQLSERVER, DATABASE_USER, DATABASE_PW)
        or die("Could not connect");  
  mysql_select_db(DATABASE_NAME);
}

function db_close()
{
  global $connection;
  if(isset($connection) && is_resource($connection))
 		mysql_close($connection);
}

function tep_exit()
{
  require("includes/application_bottom.inc.php");
  exit;
}

////-----------------------------------------------------------------------------
//// function returns image_id for $free_image_ln from free_image table
//function get_image_id($free_image_ln)
//{
//  global $connection;
//  $ret = NULL;
//  $result = mysql_query("
//SELECT free_image_picture
//FROM free_image
//WHERE free_image_ln = '" . $free_image_ln . "'
//LIMIT 0,1;");
//
//  if( $row = mysql_fetch_assoc($result) ){
//    $ret = $row["free_image_picture"];
//    mysql_free_result($result);
//  }
//  return $ret;
//}
 

////-----------------------------------------------------------------------------
//// function returns text for $free_text_name from free_text table
//function get_free_text($free_text_ln)
//{
//  global $connection;
//  $ret = "";
//  $result = mysql_query("
//SELECT free_text_value
//FROM free_text
//WHERE free_text_ln = '" . $free_text_ln . "'
//LIMIT 0,1;");
//
//  if( $row = mysql_fetch_assoc($result) ){
//    $ret = $row["free_text_value"];
//    mysql_free_result($result);
//  }
//  return $ret;
//}


////-----------------------------------------------------------------------------
//// function returns id in table on its Mnemocode
//function get_id($table, $ln)
//{
//  global $connection;
//  $ret = 0;
//  $result = mysql_query("
//SELECT " . $table . "_id
//FROM " . $table . "
//WHERE " . $table . "_ln = '" . $ln . "'");
//
//  if( $row = mysql_fetch_assoc($result) )
//  {
//    $ret = (integer)$row[$table . "_id"];
//    mysql_free_result($result);
//  }
//  return $ret;
//}

////-----------------------------------------------------------------------------
//// function returns list item value for its name and code
//function get_list_item_value($list_ln, $list_item_ln)
//{
//  global $connection;
//  $ret = "";
//  $result = mysql_query("
//SELECT list_items
//FROM list
//WHERE list_ln = '" . $list_ln . "'");
//
//  if( $row = mysql_fetch_assoc($result) ){
//    foreach(split(LIST_SEPARATOR, $row["list_items"]) as $item)
//    {
//      if(trim($item) == "")
//        continue;
//
//      list($item_ln, $item_value) = explode(LIST_ITEM_SEPARATOR, $item);
//      if($item_ln == $list_item_ln)
//      {
//        $ret = $item_value;
//        break;
//      }
//    }
//    mysql_free_result($result);
//  }
//  return trim($ret);
//}


//-----------------------------------------------------------------------------
// function gets years between $date1 and $date2
// $date1, $date2 - strings with dates in format "Y-m-d H:i:s"
// $date1 >= $date2
function get_years($date1, $date2)
{
  $ret = 0;

  if( preg_match("/^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", $date1, $matches1) 
  && preg_match("/^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", $date2, $matches2)   )
  {
    if( checkdate($matches1[2], $matches1[3], $matches1[1])
    && (0 <= (integer)$matches1[4] && (integer)$matches1[4] <= 23)
    && (0 <= (integer)$matches1[5] &&  (integer)$matches1[5] <= 59)
    && (0 <= (integer)$matches1[6] && (integer)$matches1[6] <= 59)
    && checkdate($matches2[2], $matches2[3], $matches2[1])
    && (0 <= (integer)$matches2[4] && (integer)$matches2[4] <= 23)
    && (0 <= (integer)$matches2[5] &&  (integer)$matches2[5] <= 59)
    && (0 <= (integer)$matches2[6] && (integer)$matches2[6] <= 59)  )
    {
      $ret = (integer)$matches1[1] - (integer)$matches2[1];
      if((integer)$matches1[2] < (integer)$matches2[2])
      {
        $ret--;
      }
      elseif( (integer)$matches1[2] == (integer)$matches2[2] 
      && (integer)$matches1[3] < (integer)$matches2[3])
      {
        $ret--;
      }
      elseif( (integer)$matches1[2] == (integer)$matches2[2] 
      && (integer)$matches1[3] == (integer)$matches2[3]
      && (integer)$matches1[4] < (integer)$matches2[4] )
      {
        $ret--;
      }
      elseif( (integer)$matches1[2] == (integer)$matches2[2] 
      && (integer)$matches1[3] == (integer)$matches2[3]
      && (integer)$matches1[4] == (integer)$matches2[4]
      && (integer)$matches1[5] < (integer)$matches2[5] )
      {
        $ret--;
      }
      elseif( (integer)$matches1[2] == (integer)$matches2[2] 
      && (integer)$matches1[3] == (integer)$matches2[3]
      && (integer)$matches1[4] == (integer)$matches2[4]
      && (integer)$matches1[5] == (integer)$matches2[5]
      && (integer)$matches1[6] < (integer)$matches2[6] )
      {
        $ret--;
      }
    }
  }
  
  return $ret;
}




//-----------------------------------------------------------------------------
// function returns errors count for any sources stored in session
function s_errors_count()
{
  $ret = 0;

  if(!isset($_SESSION["errors"]) 
  || !is_array($_SESSION["errors"]) 
  || count($_SESSION["errors"])==0 )
  {
    return $ret;
  }

  foreach($_SESSION["errors"] as $error_source)
  {
    if(is_array($error_source))
    {
      $ret += count($error_source);
    }
  }
  
  return $ret;
}


//-----------------------------------------------------------------------------
// function returns errors count for specified source stored in session
function s_source_errors_count($source)
{
  $ret = 0;

  if(!isset($_SESSION["errors"]) 
  || !is_array($_SESSION["errors"]) 
  || count($_SESSION["errors"])==0 )
  {
    return $ret;
  }
  
  if(isset($_SESSION["errors"][$source]) && is_array($_SESSION["errors"][$source]))
  {
    $ret += count($_SESSION["errors"][$source]);
  }
  //var_dump($ret); exit;
  return $ret;
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// function store variable in session for variable name
function s_add_var($source, $name, $value)
{
  if( !isset($_SESSION["var"][$source]) 
  || !is_array($_SESSION["var"][$source]) )
  {
    // create error for this source
    $_SESSION["var"][$source] = array();
  }
	
  // add var for this source
  $_SESSION["var"][$source][$name] = $value;
  //array_push($_SESSION["var"][$source], array($name=>$value));
}

function s_isset_var($source, $name)
{
	if(isset($_SESSION["var"][$source][$name]))
	{
		return true;
	}
	return false;
}

//-----------------------------------------------------------------------------
// function gets variable value for it name
// if variable is not set returns empty string
function s_get_var($source, $name)
{
	if(isset($_SESSION["var"][$source][$name]))
	{
		return $_SESSION["var"][$source][$name];
	}
	return "";
}


//-----------------------------------------------------------------------------
// function reset vars all for the source
function s_reset_vars($source)
{
	if(isset($_SESSION["var"][$source]))
	{
		unset($_SESSION["var"][$source]);
	}
}

//-----------------------------------------------------------------------------
// function reset vars all for the source
function s_reset_var($source,$name)
{
	if(isset($_SESSION["var"][$source][$name]))
	{
		unset($_SESSION["var"][$source][$name]);
	}
}

//-----------------------------------------------------------------------------
// function add information about error in session object
function s_add_error($source, $error)
{
  if( !isset($_SESSION["errors"][$source]) 
  || !is_array($_SESSION["errors"][$source]) )
  {
    // create error for this source
    $_SESSION["errors"][$source] = array();
  }
  // add error for this source
  array_push($_SESSION["errors"][$source], $error);
  //var_dump($error);exit;
}

//-----------------------------------------------------------------------------
// function returns var array for source
function s_get_vars_array($source)
{
	
  if( !isset($_SESSION["var"][$source]) 
  || !is_array($_SESSION["var"][$source]) )
  {
    // create error for this source
    return array();
  }
	
  return $_SESSION["var"][$source];
}


//-----------------------------------------------------------------------------
// function returns errors array for source
function s_get_errors_array($source)
{
	
  if( !isset($_SESSION["errors"][$source]) 
  || !is_array($_SESSION["errors"][$source]) )
  {
    // create error for this source
    return array();
  }
	
  return $_SESSION["errors"][$source];
}


//-----------------------------------------------------------------------------
// function reset information about errors for specified source 
// stored in session object
function s_reset_errors($source)
{
  $_SESSION["errors"][$source] = array();
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// function reset information about errors for any source without specified
// stored in session object
function s_reset_errors_without_source($source)
{ 
  if(isset($_SESSION["errors"][$source]))
  {
  	$temp = $_SESSION["errors"][$source];
  	s_reset_all_errors();
  	$_SESSION["errors"][$source] = $temp;
  }
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// function checks email for required simbols
function check_email($email)
{
  return verify_email($email);
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// Функция проверки email
function verify_email($email)
{

    if(!preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]+)$/',$email)){
        return false;
    } else {
        return true;
    }
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// function show edit date fields in html form tags
// like dropdown lists
function view_edit_date($ln, $date)
{
  $err_rep = ini_get ('error_reporting');
  ini_set ('error_reporting', E_ERROR);

  $a_date = getdate($date);

  ini_set ('error_reporting', $err_rep);
  if(!is_array($a_date))
  {
    $a_date["mday"] = 1;
    $a_date["mon"] = 1;
    $a_date["year"] = 1900;
  }
?>
<SELECT name="<? echo $ln; ?>_day" size="1" class="dropdownblue">
<?
  for($i=1; $i<=31; $i++)
  {
?>
  <OPTION value="<? printf("%02d", $i);?>"<?
    if($a_date["mday"] == $i)
    {
      echo " selected";
    }
  ?>><? printf("%02d", $i);?></OPTION>
<?
  }
?>
</SELECT>
/
<select name="<? echo $ln; ?>_month" size="1" class="dropdownblue">
<?
  for($i=1; $i<=12; $i++)
  {
?>
  <OPTION value="<? printf("%02d", $i);?>"<?
    if($a_date["mon"] == $i)
    {
      echo " selected";
    }
  ?>><? printf("%02d", $i);?></OPTION>
<?
  }
?>
</select>
/ 
<SELECT name="<? echo $ln; ?>_year1" size="1" class="dropdownblue">
  <OPTION value="19"<?
    if($a_date["year"] < 2000)
    {
      echo " selected";
    }
  ?>>19</OPTION>
  <OPTION value="20"<?
    if($a_date["year"] >= 2000)
    {
      echo " selected";
    }
  ?>>20</OPTION>
</SELECT>
<SELECT name="<? echo $ln; ?>_year2" size="1" class="dropdownblue">
<?
  if($a_date["year"]-1900 < 100)
  {
    $a_date["year"] = $a_date["year"]-1900;
  }
  else
  {
    $a_date["year"] = $a_date["year"]-2000;
  }
  for($i=0; $i<=99; $i++)
  {
?>
  <OPTION value="<? printf("%02d", $i);?>"<?
    if($i == $a_date["year"])
    {
      echo " selected";
    }
  ?>><? printf("%02d", $i);?></OPTION>
<?
  }
?>
</SELECT>
dd/mm/yyyy
<?
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
function get_datetime_from_string($str)
{
  $ret = NULL;
  if( preg_match("/^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", $str, $matches) ){
    $err_rep = ini_get ('error_reporting');
    ini_set ('error_reporting', E_ERROR);

    $ret = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1] );

    ini_set ('error_reporting', $err_rep);
  }
  return $ret;
}

//-----------------------------------------------------------------------------
// function returns datetime from $var
function get_date_from_vars($ln, $var)
{
  $ret = NULL;

  if(checkdate((integer)$var[$ln."_month"], (integer)$var[$ln."_day"], (integer)($var[$ln."_year1"].$var[$ln."_year2"])))
  {
    $ret = $var[$ln."_year1"].$var[$ln."_year2"]."-".$var[$ln."_month"]."-".$var[$ln."_day"]." 00:00:00";
  }
  
  return $ret;
}

//-----------------------------------------------------------------------------
function send_message($from_email, $from_name, $to_email, $subject, $message)
{

  /* To send HTML mail, you can set the Content-type header.
  $headers  = "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
  */

  // additional headers */
  $headers = "";

  //$headers .= "To: $to_email\r\n";
  $headers .= "From: $from_name <$from_email>\r\n";
  $headers .= "Content-Type: text/plain; charset=".DEFAULT_CHARSET."; format=flowed".
  "\r\n"."Content-Transfer-Encoding: 8bit";

  $message = str_replace("\r\n","\n",$message);

  // and now mail it
  $ret = false;
  $ret = @mail($to_email, $subject, $message, $headers);
  return $ret;
}
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// Функция отправки сообщения с вложенными файлами
//-----------------------------------------------------------------------------
function send_message_files($from_email, $from_name, $to_email, $subject, $message, $files=array())
{
  /* PREPARE MAIL HEADERS */
  $headers = "";

  //$headers .= "To: $to_email\r\n";

  $semi_rand = md5(time());
  $mime_boundary = "{$semi_rand}";

  $headers .= "From: $from_name <$from_email>\n";
  $headers .= "X-Mailer: PHP\n";
  $headers .= "Reply-To: $from_name <$from_email>\n";
  $headers .= "X-Priority: 3 (Normal)\n";

  $headers .= "MIME-Version: 1.0\n" .
                "Content-Type: multipart/mixed;\n" .
                " boundary=\"--{$mime_boundary}\"";


  $headers2 = "From: $from_name <$from_email>
Reply-To: {$from_name} <{$from_email}>
X-Mailer: PHP QCMS
X-Priority: 3 (Normal)
  ";

  $headers2 .= "MIME-Version: 1.0
Content-Type: multipart/mixed;
 boundary=\"{$mime_boundary}\"

";

  $message_full2 = "--{$mime_boundary}
Content-Type: text/plain; charset=windows-1251
Content-Transfer-Encoding: 8bit

{$message}
--{$mime_boundary}";
/*

$message_full2 .= '
Content-Type: application/octet-stream;
 name="{$filename}"
Content-transfer-encoding: base64
Content-Disposition: attachment;
 filename="{$filename}"

{$data}
--{$mime_boundary}';

$message_full2 .= '
Content-Type: application/octet-stream;
 name="{$filename}"
Content-transfer-encoding: base64
Content-Disposition: attachment;
 filename="{$filename}"

{$data}
--{$mime_boundary}--

';
*/


  $message_full = "--{$mime_boundary}\n" .
                   "Content-Type:text/plain; charset=windows-1251\n" .
                   "Content-Transfer-Encoding: 8bit\n\n" .
            @$message . "\n\n" ;

  /* PREPARE ATTACHMENT */
  //var_dump($files);
  foreach($files as $file)
  {
    $fileatt = $file["name"];
    $filename = $file["name"];
    $fileatt_type = "application/octet-stream";


    if(!isset($file["file_content"]))
    {
      $f = fopen($file['tmp_name'],"rb");
      $data = fread($f,$file['size']);
      fclose($f);
    }
    else
    {
      $data = $file["file_content"];
    }

    $data = chunk_split(base64_encode($data));

    $message_full .= "\n--{$mime_boundary}\n" .
        "Content-Type: application/octet-stream;\n" .
        " name=\"{$filename}\"\n" .
        "Content-Transfer-Encoding: base64\n" .
        "Content-Disposition: attachment;\n" .
        "filename=\"{$filename}\"\n\n" .
        $data . "\n" .
        "--{$mime_boundary}\n";
    $message_full2 .= "
Content-Type: application/octet-stream;
 name=\"{$filename}\"
Content-transfer-encoding: base64
Content-Disposition: attachment;
 filename=\"{$filename}\"

{$data}
--{$mime_boundary}";
    //break;
  }
  if(count($files))
  {
    $message_full .= "--\n";
  }
  else
  {
    $message_full .= "\n";
  }
$message_full2 .= '--

';

  /* SEND FILE */
  $ok = @mail($to_email, $subject, $message_full2, $headers2);
  //var_dump($headers);
  //var_dump($message_full);
  //exit;

  if($ok)
  {
    return true;
  }

  //return true;
  return false;

}

//-----------------------------------------------------------------------------
function send_message_html($from_email, $from_name, $to_email, $subject, $message, $css="", $cc="", $bcc="")
{
	$charset = DEFAULT_CHARSET;
	
	/* To send HTML mail, you can set the Content-type header.
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	*/

	$subject_encoded = send_message_encode_subject($subject,$charset);

	// additional headers */
	$headers = "";
	// To send HTML mail, the Content-type header must be set
	$headers  .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset='.DEFAULT_CHARSET. "\r\n";
	
	
	//$headers .= "To: $to_email\r\n";
	$headers .= "From: $from_name <$from_email>\r\n";
	if($cc)
	{
		$headers .= 'CC: '.$cc."\r\n";
	}
	if($bcc)
	{
		$headers .= 'BCC: '.$bcc."\r\n";
	}
	
	$message_html = '<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset='.$charset.'" />
  <title>'.$subject.'</title>';

	if($css)
	{
		$message_html .= '
<STYLE type="text/css">
'.$css.'
</STYLE>
';
	}
	$message_html .= '
</head>
<body>
'.$message.'
</body>
</html>
';

	//$headers .= "Content-Type: text/plain; charset=windows-1251; format=flowed".
	//"\r\n"."Content-Transfer-Encoding: 8bit";

	//$message = str_replace("\r\n","\n",$message);

	// and now mail it
	$ret = false;
	$ret = @mail($to_email, $subject_encoded, $message_html, $headers);
	return $ret;
}

function send_message_encode_subject($subject,$charset)
{
	$return="=?".$charset."?B?".base64_encode($subject)."?=";
	return $return;
  //$return = "=?".$charset."?Q?".str_replace("=\r\n", "", preg_replace("/\?/", "=3F", imap_8bit($subject)))."?=";
  //return $return;
}


//-----------------------------------------------------------------------------
// function reset information about errors stored in session object
function s_reset_all_errors()
{
  $_SESSION["errors"] = array();
}
//-----------------------------------------------------------------------------


?>