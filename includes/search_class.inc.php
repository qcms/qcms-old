<?
//=============================================================================
// Определения классов поиска
require_once("includes/navigation_class.inc.php");

//-----------------------------------------------------------------------------
// класс поиска
class SearchClass
{
  //===========================================================================
  // данные члены
  var $search_name; // название для поиска
  
  //---------------------------------------------------------------------------
  // массивы поиска

  // массив условий поиска
  // name => value
  var $search_conditions = array();  
  // массив результатов поиска
  // count => array( datetime => value, name => value, description => value, keyword => value, location => value, get => value, type => value)
  var $search_results = array(); 
  
  //===========================================================================
  // функции члены
  
  //---------------------------------------------------------------------------
  // конструктор класса поиска
  function SearchClass($search_name, $search_conditions)
  {
    $this->search_name = $search_name;
    $this->search_conditions = $search_conditions;
  }

  //---------------------------------------------------------------------------
  // функция возвращает строку адаптированную для оператора LIKE в SQL запросе
  function SearchReplace($string)
  {
    return str_replace("*","%",str_replace("'","''",$string));
  }
  


  //---------------------------------------------------------------------------
  // функция находит документы по критериям поиска
  function AddDocuments()
  {
    $query = "
SELECT document.document_id, document.page_ln, document.document_name, document.document_description, document.document_type, document.document_accesstype
FROM document 
WHERE NULL IS NULL
AND (
NULL IS NOT NULL";
    
    // условия для поиска документов
    
    
    // поиск по названию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "names")
    {
      $query .= "
OR document.document_name LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по описанию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "descriptions")
    {
      $query .= "
OR document.document_description LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }

    $query .= "
)
";

//var_dump($query);
    // выполним запрос и заполним результатами
    $result = mysql_query($query);
    if($result)
    {
      while($row = mysql_fetch_assoc($result))
      {
      
        $found = array(
          "datetime" => get_value_lang($row["document_datetime"],$_SESSION["lang"]),
          "name" => get_value_lang($row["document_name"],$_SESSION["lang"]), 
          "description" => get_value_lang($row["document_description"],$_SESSION["lang"]), 
          "keyword" => "", 
          "location" => "page.php?id=" . $row["page_ln"], 
          "get" => "documentfile.php?id=" . $row["document_id"],
          "type" => "document"
        );
        if($found != NULL)
        {
          $this->search_results[count($this->search_results)] = $found;
        }
        unset($found);
      }
    
      mysql_free_result($result);
    }
    
  }

  
  
  //---------------------------------------------------------------------------
  // функция находит страницы по критериям поиска
  function AddPages()
  {
    $query = "
SELECT page.page_ln, page.page_name, page.page_description, page.page_keywords, page.page_text, page.page_datetime, page.page_file
FROM page
WHERE NULL IS NULL
AND (
NULL IS NOT NULL";
    
    // условия для поиска документов
    
    
    // поиск по названию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "names")
    {
      $query .= "
OR page.page_name LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по описанию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "descriptions")
    {
      $query .= "
OR page.page_description LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по ключевым словам
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "keywords")
    {
      $query .= "
OR page.page_keywords LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по тексту
    if(true)
    {
      $query .= "
OR page.page_text LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }

    $query .= "
)
";

//var_dump(nl2br($query));
    // выполним запрос и заполним результатами
    $result = mysql_query($query);
    if($result)
    {
      while($row = mysql_fetch_assoc($result))
      {
        $found = array(
          "datetime" => get_value_lang($row["page_datetime"],$_SESSION["lang"]),
          "name" => get_value_lang($row["page_name"],$_SESSION["lang"]), 
          "description" => get_value_lang($row["page_description"],$_SESSION["lang"]), 
          "keyword" => get_value_lang($row["page_keywords"],$_SESSION["lang"]),  
          "location" => "page.php?id=" . $row["page_ln"], 
          "get" => "page.php?id=" . $row["page_ln"],
          "type" => "page"
        );
        if($found != NULL)
        {
          $this->search_results[count($this->search_results)] = $found;
        }
        unset($found);
      }
    
      mysql_free_result($result);
    }
  }

  
  
  
  //---------------------------------------------------------------------------
  // функция находит контексты страницы по критериям поиска
  function AddPageContents()
  {
    $query = "
SELECT content.content_ln, content.page_ln, content.content_number, content.content_datetime, content.content_header, content.content_text, content.content_link_text
FROM content
WHERE NULL IS NULL
AND (
NULL IS NOT NULL";
    
    // условия для поиска контента
    // поиск по тексту контента
    if(true)
    {
      // заголовки контента
      $query .= "
OR content.content_header LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
      // текст контента
      $query .= "
OR content.content_text LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
      // ссылки контента
      $query .= "
OR content.content_link_text LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }

    $query .= "
)
";

//var_dump($query);
    // выполним запрос и заполним результатами
    $result = mysql_query($query);
    if($result)
    {
      while($row = mysql_fetch_assoc($result))
      {
        $found = array(
          "datetime" => get_value_lang($row["content_datetime"],$_SESSION["lang"]),
          "name" => get_value_lang($row["content_header"],$_SESSION["lang"]), 
          "description" => get_value_lang($row["content_text"],$_SESSION["lang"]), 
          "keyword" => "", 
          "location" => "page.php?id=" . $row["page_ln"], 
          "get" => "page.php?id=" . $row["page_ln"],
          "type" => "page"
        );
        if($found != NULL)
        {
          $this->search_results[count($this->search_results)] = $found;
        }
        unset($found);
      }
    
      mysql_free_result($result);
    }
  }

  
  
  
  //---------------------------------------------------------------------------
  // функция находит новости по критериям поиска
  function AddNews()
  {
    $query = "
SELECT news.news_id, news.news_header, news.news_description, news.news_text, news.news_datetime
FROM news
WHERE NULL IS NULL
AND (
NULL IS NOT NULL";
    
    // условия для поиска документов
    
    
    // поиск по названию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "names")
    {
      $query .= "
OR news.news_header LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по описанию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "descriptions")
    {
      $query .= "
OR news.news_description LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    // поиск по тексту
    if(true)
    {
      $query .= "
OR news.news_text LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }

    $query .= "
)
";

//var_dump($query);
    // выполним запрос и заполним результатами
    $result = mysql_query($query);
    if($result)
    {
      while($row = mysql_fetch_assoc($result))
      {
        $found = array(
          "datetime" => get_value_lang($row["news_datetime"],$_SESSION["lang"]),
          "name" => get_value_lang($row["news_header"],$_SESSION["lang"]), 
          "description" => get_value_lang($row["news_description"],$_SESSION["lang"]), 
          "keyword" => "",  
          "location" => "news.php?id=" . $row["news_id"], 
          "get" => "news.php?id=" . $row["news_id"],
          "type" => "page"
        );
        if($found != NULL)
        {
          $this->search_results[count($this->search_results)] = $found;
        }
        unset($found);
      }
    
      mysql_free_result($result);
    }
  }
  

  
  
  //---------------------------------------------------------------------------
  // функция находит новости по критериям поиска
  function AddAnnounce()
  {
    $query = "
SELECT announce.announce_id, announce.announce_header, announce.announce_text, announce.announce_datetime
FROM announce
WHERE NULL IS NULL
AND (
NULL IS NOT NULL";
    
    // условия для поиска документов
    
    /*
    // поиск по названию
    if($this->search_conditions["inkind"] == "" || $this->search_conditions["inkind"] == "names")
    {
      $query .= "
OR announce.announce_header LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }
    */
    // поиск по тексту
    if(true)
    {
      $query .= "
OR announce.announce_text LIKE '%" . $this->SearchReplace($this->search_conditions["search"]) . "%'
";
    }

    $query .= "
)
ORDER BY announce.announce_datetime DESC
LIMIT 0,5
";

//var_dump($query);
    // выполним запрос и заполним результатами
    $result = mysql_query($query);
    if($result)
    {
      while($row = mysql_fetch_assoc($result))
      {
        $found = array(
          "datetime" => get_value_lang($row["announce_datetime"],$_SESSION["lang"]),
          "name" => get_value_lang($row["announce_header"],$_SESSION["lang"]), 
          "description" => get_value_lang($row["announce_text"],$_SESSION["lang"]), 
          "keyword" => "",  
          "location" => "index.php", 
          "get" => "index.php",
          "type" => "page"
        );
        if($found != NULL)
        {
          $this->search_results[count($this->search_results)] = $found;
        }
        unset($found);
      }
    
      mysql_free_result($result);
    }
  }
  


  
  //---------------------------------------------------------------------------
  // функция выполняет поиск по заданным условиям $this->search_conditions
  function SimpleSearch()
  {
    // поиск по документам 
    if($this->search_conditions["intype"] == "docs" || $this->search_conditions["intype"] == "")
    {
      $this->AddDocuments();
    }
    // поиск по страницам
    if($this->search_conditions["intype"] == "pages" || $this->search_conditions["intype"] == "")
    {
      $this->AddPages();
    }
    // поиск везде
    if($this->search_conditions["intype"] == "")
    {
      $this->AddPageContents();
      $this->AddNews();
      $this->AddAnnounce();
    }

    
    // сортировка результатов    
    if($this->search_conditions["sort"] == "name")
    {
      $this->OrderResult("name", $this->search_conditions["order"]);
    }
    else
    {
      $this->OrderResult("date", $this->search_conditions["order"]);
    }
    
    //$this->CheckResult();
  }

/*  
  //---------------------------------------------------------------------------
  // функция проверки резульатов которая удаляет пустые результаты
  function CheckResult()
  {
    $result_array = array();
    for($i=0; $i<count($this->search_results); $i++)
    {
echo "<br>";
var_dump($this->search_results[$i]);
echo "<br>";
      if(!$this->search_results[$i]["name"])
      {
        continue;
      }
      $result_array[count($result_array)] = $this->search_results[$i];
    }

    $this->search_results = $result_array;
  }
*/

  
  //---------------------------------------------------------------------------
  // Функция сортировки результатов поиска
  // $order_by - имя атрибута по которому производится сортировка "datetime", "name", "description", "keyword"
  // $order_sort - способ сортировки "asc", "desc"
  function OrderResult($order_by, $order_sort)
  {
    // сортировка результата методом пузырька
    // проверим нужна ли сортировка
    if(count($this->search_results) <= 1)
    {
      // сортировка не нужна
      return;
    }
    

    do // основной цикл
    {
      $move_count = 0; // количество перестановок методом пузырька
      for($i=0; $i<count($this->search_results)-1; $i++) // цикл перестановок
      {
        if($order_by == "date") // сортировка по дате
        {
          if($order_sort == "asc" 
          && $this->search_results[$i]["datetime"] > $this->search_results[$i+1]["datetime"])
          {
            // перестановка
            $temp_result = $this->search_results[$i];
            $this->search_results[$i] = $this->search_results[$i+1];
            $this->search_results[$i+1] = $temp_result;
            $move_count++;
          }
          if($order_sort == "desc" 
          && $this->search_results[$i]["datetime"] < $this->search_results[$i+1]["datetime"])
          {
            // перестановка
            $temp_result = $this->search_results[$i];
            $this->search_results[$i] = $this->search_results[$i+1];
            $this->search_results[$i+1] = $temp_result;
            $move_count++;
          }
        }
        elseif($order_by == "name") // сортировка по имени
        {
          if($order_sort == "asc" 
          && $this->search_results[$i]["name"] > $this->search_results[$i+1]["name"])
          {
            // перестановка
            $temp_result = $this->search_results[$i];
            $this->search_results[$i] = $this->search_results[$i+1];
            $this->search_results[$i+1] = $temp_result;
            $move_count++;
          }
          if($order_sort == "desc" 
          && $this->search_results[$i]["name"] < $this->search_results[$i+1]["name"])
          {
            // перестановка
            $temp_result = $this->search_results[$i];
            $this->search_results[$i] = $this->search_results[$i+1];
            $this->search_results[$i+1] = $temp_result;
            $move_count++;
          }
        }
      }
      
    }while($move_count > 0);
    
  }
  
  //---------------------------------------------------------------------------
  // Функция показа результатов поиска
  function ViewSearch()
  {
    // отобразим результаты поиска
    
    // название поиска
    echo "<p><b>" . $this->search_name . "</b>
<br>
Найдено " . count($this->search_results) . " совпадений
</p>
";
    // количество найденного
  
    $navigation = new NavigationClass( NULL, $this->search_conditions["count"], count($this->search_results) );

    $navigation->view_navigation();
    $first = true;
    
    $temp_results = $navigation->array_navigation($this->search_results);
    
    for($i=0; $i<count($temp_results); $i++)
    {
      $current_result = $temp_results[$i];
      
      // шаблон для результата поиска
      $template = '
<table width="565" border="0" cellspacing="0" cellpadding="0">
%otsechka%
<tr align="left" valign="top">
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
  <td colspan="3" height="1" bgcolor="#666666"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
</tr>
%otsechka%
<tr align="left" valign="top">
  <td colspan="5" height="10"><img width="1" height="10" src="'.VN_PIXEL.'" border="0" alt=""></td>
</tr>
<tr align="left" valign="top">
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
  <td width="45"><a href="'. VN_DIR .'%get%"><img width="45" height="45" src="%icon%" alt="" border="0"></a></td>
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
  <td width="505"><strong>%name%</strong><br>
<span class="publish"><<lang:Опубликовано|Publication>>: %datetime%</span><br>
%description%</td>
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
</tr>
<tr align="right" valign="top">
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
  <td colspan="3"><a href="'. VN_DIR .'%get%"><<lang:Открыть|Open>></a>&nbsp;<a href="'. VN_DIR .'%get%"><img src="images/link1.gif" width="17" height="7" border="0"></a>&nbsp;</td>
  <td width="5"><img width="1" height="1" src="'.VN_PIXEL.'" border="0" alt=""></td>
</tr>
<tr align="left" valign="top">
  <td colspan="5" height="10"><img width="1" height="10" src="'.VN_PIXEL.'" border="0" alt=""></td>
</tr>
</table>
'; 
      // полоска между документами
      if($first == true)
      {
        $template = preg_replace("/%otsechka%(.*)%otsechka%/mis", "", $template);
      }
      $template = preg_replace("/%otsechka%/mis", "", $template);


      // языковые варианты
      // конструкция вида <<lang:К списку новостей|To the list of news>>
      if(preg_match_all("/<<lang:([^>]*)>>/", $template, $matches))
      {
        for($j=0; $j< count($matches[0]); $j++)
        {
          $template = preg_replace("/". preg_quote($matches[0][$j]) ."/", get_value_lang(preg_replace("/\|/",LANGUAGE_SPLITTER,$matches[1][$j]), $_SESSION["lang"]), $template);
        }
      }
      
      // название
      $template = str_replace("%name%", preg_replace("/(" . $this->search_conditions["search"] . ")/mis", "<span style='background-color : #E0E0E0;'>\$1</span>", $current_result["name"]), $template);
      // дата и время
      //$template = str_replace("%datetime%", date("d.m.Y",$current_result["datetime"]), $template);
      $template = str_replace("%datetime%", $current_result["datetime"], $template);

      // описание
      $template = str_replace("%description%", preg_replace("/(" . $this->search_conditions["search"] . ")/mis", "<span style='background-color : #E0E0E0;'>\$1</span>", $current_result["description"]), $template);
      
      // ссылки
      $template = str_replace("%get%", $current_result["get"], $template);
      
      // иконка
      switch($current_result["type"])
      {
        case "application/msword":
          $template = str_replace("%icon%", VN_IMAGES . "icon_doc.gif", $template);
          break;
        
        case "text/richtext":
          $template = str_replace("%icon%", VN_IMAGES . "icon_rtf.gif", $template);
          break;

        case "application/pdf":
          $template = str_replace("%icon%", VN_IMAGES . "icon_pdf.gif", $template);
          break;

        default:
          $template = str_replace("%icon%", VN_PIXEL, $template);
      }
      /*
      if($document->headers["document_type"] == "application/msword"
      {
      }
      elseif($document->headers["document_type"] == "text/richtext"))
      {
      }
      */
      
//echo "<pre>";
//var_dump($document->template);
//echo "</pre>";
      echo $template;
      $first = false;
    }

    $navigation->view_navigation();

//var_dump($this);
  }
  
}

?>