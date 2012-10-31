<?
/**
 * Клас CNavigationPager предназначен для формирования
 * и вывода постраничной навигации
 */
class CNavigationPager
{
    /**
     * @var string запрос для которого берем записи (по которому навигация)
     */
    var $query;
    /**
     * текущая позиция для навигации
     * @var int
     */
    var $current;
    /**
     * @var int количество записей отдаваемых на страницу
     */
    var $pagecount;
    /**
     * @var int номер первой записи для последней страницы навигации
     */
    var $last;
    /**
     * @var string название объекта навигации,
     * применяется когда на одной странице нужно несколько навигаций
     */
    var $object;
    /**
     * @var int общее количество строк в результате запроса
     */
    var $num_rows;

    /**
     * @var string отображаемый контент постраничной навигации
     */
    var $view = "";
    /**
     * @var string ключ сессии в которой храним информацию о текущей позиции навигации
     */
    var $session_key;

    /**
     * @var string ссылка на файл который будет устанавливать значение сессии
     */
    var $navigation_pager_link;
    /**
     * префикс ключа сессии
     */
    const session_key_prefix = "NAVIGATION_PAGER";

    /**
     *  файл который будет устанавливать значение сессии
     */
    const navigation_pager_file = "navigation_pager.php";


    /**
     * Конструктор
     * @param string $query запрос на основе которого формируется навигация
     * @param int $pagecount количество записей на странице
     * @param string $object название объекта навигации
     * @param null|int $num_rows общее количество строк в результате запроса
     */
    function CNavigationPager($query, $pagecount, $object="", $num_rows=null)
    {
        $this->pagecount = $pagecount;
        $this->object = $object;

        //$this->session_key = CNavigationPager::session_key_prefix."_".$this->object."_".$_SERVER["REQUEST_URI"];
        $this->session_key = CNavigationPager::GetSessionKey($this->object, $_SERVER["REQUEST_URI"]);

        global $_SESSION;

        if( !isset($_SESSION[$this->session_key]["navc"])
            || (isset($_SESSION[$this->session_key])&& $_SESSION[$this->session_key]["navc"] == "") )
        {
            $this->current  = 0;
        }

        if(!$query && $num_rows)
        {
            // просто навигация без запроса к БД
            $this->num_rows = $num_rows;
        }
        else
        {
            $this->query = $query;

            $db = new CDatabase();

            $result = $db->Query($this->query);

            if( $result )
            {
                $this->num_rows = $db->RowCount();
            }
        }



        if( isset($_SESSION[$this->session_key]["navc"]) && $_SESSION[$this->session_key]["navc"] >= 0
            && ($_SESSION[$this->session_key]["navc"] % $this->pagecount) == 0 )
        {
            if( (integer)$_SESSION[$this->session_key]["navc"] < $this->num_rows )
            {
                $this->current = (integer)$_SESSION[$this->session_key]["navc"];
            }
        }
        for( $i=$this->num_rows-1; $i>=0; $i--)
        {
            if( ($i % $this->pagecount) == 0 )
            {
                $this->last = $i;
                break;
            }
        }

        $this->navigation_pager_link = VN_DIR.CNavigationPager::navigation_pager_file
            ."?sid=".urlencode(session_id())
            ."&object=".urlencode($this->object)
            ."&backurl=".urlencode($_SERVER["REQUEST_URI"]);
    }

    /**
     * @param $object
     * @param $uri
     * @return mixed|string
     */
    function GetSessionKey($object, $uri)
    {
        $ret = CNavigationPager::session_key_prefix."_".$object."_".$uri;
//        $ret = str_replace("/", "_", $ret);
//        $ret = str_replace("\\", "_", $ret);
//        $ret = str_replace(" ", "_", $ret);
        $ret = preg_replace('/[^\w\d]+/ims', "_", $ret);
        return $ret;
    }

    /**
     * @return string
     */
    function GetFirstLink()
    {
        return $this->navigation_pager_link . "&navc=0";
    }

    /**
     * @return string
     */
    function GetLastLink()
    {
        return $this->navigation_pager_link . "&navc=" . $this->last;
    }

    /**
     * @return string
     */
    function GetNextLink()
    {
        return $this->navigation_pager_link . "&navc=" . ($this->current + $this->pagecount);
    }

    /**
     * @return string
     */
    function GetPrevLink()
    {
        return $this->navigation_pager_link . "&navc=" . ($this->current - $this->pagecount);
    }


    /**
     * Функция возвращает номер первой записи на текущей странице
     * @return int
     */
    function GetCurrentPageFirst()
    {
        return $this->current+1;
    }

    /**
     * Функция возвращает номер последней записи на текущей странице
     * @return int|null
     */
    function GetCurrentPageLast()
    {
        //var_dump($this);
        if($this->current + 1 + $this->pagecount > $this->num_rows)
        {
            return $this->num_rows;
        }
        return $this->current + $this->pagecount;
    }

    /**
     * Функция возвращает Limit для SQL запроса по параметрам класса
     * @return string
     */
    function GetQuery()
    {
        return " LIMIT " . intval($this->current) . ", " . intval($this->pagecount);
    }


    /**
     * @param $file
     * @param string $template
     * @param string $component
     * @param bool $output
     * @return string
     */
    function View($file, $template="default", $component="page", $output=false)
    {
        global $MAIN;
        //error_log('CNavigationPager: $this: '.print_r($this, true));

        $ret = "";

        global $navigation_pager_prev,
               $navigation_pager_items,
               $navigation_pager_next;

        //error_log($file);
        $MAIN->LoadLangMessages(__FILE__); // загрузим сообщения по умолчанию (для класса)
        $MAIN->LoadLangMessages($file); // загрузим сообщения для шаблона (если есть)


        $navigation_pager_prev = $this->GetPrevLink();
        $navigation_pager_next = $this->GetNextLink();

        $navigation_pager_items = "";

        //$template_content = $MAIN->GetTemplateContent($file, $template, $component, "navigation_pager_item.php");

        // количество страниц
        $page_count = (integer)(ceil(($this->num_rows/$this->pagecount)));
        // текущая страница
        $page_current_index = (integer)(round($this->current/$this->pagecount, 0));

        if($page_current_index == 0)
        {
            $navigation_pager_prev = "";
        }

//        var_dump($page_count);
//        var_dump($page_current);
        if($page_count == $page_current_index+1)
        {
            $navigation_pager_next = "";
        }

        $skip1 = false;
        $skip2 = false;

        for($i=0;$i<$page_count;$i++)
        {
            $navigation_pager_items .= $this->ViewItemEx($file, $template, $component, $i, $page_current_index, $page_count, $skip1, $skip2);
        }


        $ret .= $MAIN->ShowTemplate($file, $template, $component, "navigation_pager.php");

        if($output)
        {
            echo $ret;
        }

        return $ret;
    }

    /**
     * @param $file
     * @param $template
     * @param $component
     * @param $i
     * @param $page_current_index
     * @param $page_count
     * @param $skip1
     * @param $skip2
     * @return string
     */
    function ViewItemEx($file, $template, $component, $i, $page_current_index, $page_count, &$skip1, &$skip2)
    {
        global $MAIN;
        $ret = "";

        global $navigation_pager_item_active,
               $navigation_pager_item_name,
               $navigation_pager_item_href;

        $navigation_pager_item_active = false;

        $show = "";
        if($i<3 || $i>$page_count-4)
        {
            $show = "item";
        }
        elseif($i>$page_current_index-3 && $i<$page_current_index+3)
        {
            $show = "item";
        }
        elseif($page_current_index-2 > 0
            && $i < $page_current_index-2
        )
        {
            $show = "skip1";
        }
        elseif($page_current_index+2 < $page_count
            && $i > $page_current_index+2
        )
        {
            $show = "skip2";
        }


        switch($show)
        {
            case "item":
//                error_log('CNavigationPager: 1');

                $navigation_pager_item_name = $i+1;
                if($i == $page_current_index)
                {
                    $navigation_pager_item_active = true;
                }
                $navigation_pager_item_href = $this->navigation_pager_link."&navc=".($this->pagecount*$i);

                $ret .= $MAIN->ShowTemplate($file, $template, $component, "navigation_pager_item.php");
                break;

            case "skip1":
//                error_log('CNavigationPager: 2');
                if(!$skip1)
                {
//                    error_log("skip1");
                    // пропуск 1
                    $skip1 = true;
                    $ret .= $MAIN->ShowTemplate($file, $template, $component, "navigation_pager_item_skip.php");
                }
                break;
            case "skip2":
//                error_log('CNavigationPager: 3');
                if(!$skip2)
                {
//                    error_log("skip2");
                    // пропуск 2
                    $skip2 = true;
                    $ret .= $MAIN->ShowTemplate($file, $template, $component, "navigation_pager_item_skip.php");
                }

        }

        return $ret;
    }
}

/*
class CNavigationDeprecated
{
    var $query; // запрос для которого берем записи (по которому навигация)
    var $current; // текущая позиция для навигации
    var $pagecount; // количество записей отдаваемых по умолчанию
    var $last; // номер первой записи для последней страницы навигации

    var $num_rows; // общее количество строк в результате запроса

    var $view = false;
    var $template = false;
    var $template_file = false;


    //---------------------------------------------------------------------------
    // конструктор класса
    // вычисляет значения navc и navr по GET параметрам $_GET[navc] и $_GET[navr] и устанавливает соответствующие значения для current и page_count
    function CNavigation($query, $page_count, $num_rows=NULL )
    {
        global $_SESSION;
        global $_SERVER,$MAIN;
        //global $_GET;
        //var_dump($_SESSION[$_SERVER["PHP_SELF"]]["navc"]);
        $this->pagecount = $page_count;

        //$tmpkey = $_SERVER["PHP_SELF"]. ($_SERVER["QUERY_STRING"]?"?".$_SERVER["QUERY_STRING"]:"");
        $tmpkey = $_SERVER["REQUEST_URI"];

        if( !isset($_SESSION[$tmpkey]["navc"])
            || (isset($_SESSION[$tmpkey])&& $_SESSION[$tmpkey]["navc"] == "") )
        {
            $this->current  = 0;
        }

        if(!$query && $num_rows)
        {
            // просто навигация без запроса к БД
            $this->num_rows = $num_rows;
        }
        else
        {
            $this->query = $query;

            $db = new CDatabase();

            $result = $db->Query($this->query);

            //$result = mysql_query($this->query) or die(die_mysql_error_show($query));
            if( $result )
            {
                $this->num_rows = $db->RowCount();//mysql_num_rows($result);
                //var_dump($this);
            }
        }


        if( isset($_SESSION[$tmpkey]["navc"]) && $_SESSION[$tmpkey]["navc"] >= 0
            && ($_SESSION[$tmpkey]["navc"] % $page_count) == 0 )
        {
            if( (integer)$_SESSION[$tmpkey]["navc"] < $this->num_rows )
            {
                $this->current = (integer)$_SESSION[$tmpkey]["navc"];
            }
        }
        for( $i=$this->num_rows-1; $i>=0; $i--)
        {
            if( ($i % $this->pagecount) == 0 )
            {
                $this->last = $i;
                break;
            }
        }
    }


    //---------------------------------------------------------------------------
    // Функция возвращает номер первой записи на текущей странице
    function GetCurrentPageFirst()
    {
        return $this->current+1;
    }

    //---------------------------------------------------------------------------
    // Функция возвращает номер последней записи на текущей странице
    function GetCurrentPageLast()
    {
        //var_dump($this);
        if($this->current + 1 + $this->page_count > $this->num_rows)
        {
            return $this->num_rows;
        }
        return $this->current + $this->page_count;
    }



    //---------------------------------------------------------------------------
    // функция вывода навигации на страницу
    function View($view = TRUE, $xajax = FALSE)
    {
        //global $_GET;
        global $_SESSION,$MAIN;
        $query_string = VN_DIR . "nav.php?sid=" .session_id();
        $query_string .= "&backurl=".urlencode($_SERVER["REQUEST_URI"]);

        $first = "";
        $last = "";
        $next = "";
        $prev = "";


        if( $query_string )
        {
            $first = $query_string . "&navc=0";
            $last = $query_string . "&navc=" . $this->last;
            $next = $query_string . "&navc=" . ($this->current + $this->page_count);
            $prev = $query_string . "&navc=" . ($this->current - $this->page_count);
        }



        if( ($this->page_count) < $this->num_rows )
        {
            $nav_value = "";

            //$nav_value .= $this->template_first;


            // если количество страниц больше 5 то нужно пропустить некоторые
            $pcount = (integer)ceil(($this->num_rows/$this->page_count));
            $pcurrent = (integer)round($this->current/$this->page_count, 0);

            if($pcurrent == 0)
            {
                $nav_value .= ($xajax?$this->template_prev_active_xajax:$this->template_prev_active);
            }
            else
            {
                $nav_value .= ($xajax?$this->template_prev_xajax:$this->template_prev);
            }


            for($i=0; $i<$pcount; $i++)
            {
                // текущая страница
                if($i==$pcurrent)
                {
                    $nav_value .= str_replace("[item_value]", $i+1, ($xajax?$this->template_item_active_xajax:$this->template_item_active));
                    continue;
                }

                // первая
                if($i == 0)
                {
                    $nav_value .= ($xajax?$this->template_first_xajax:$this->template_first);

                    if($pcurrent > $i+2)
                    {
                        $nav_value .= ($xajax?$this->template_interval_xajax:$this->template_interval);
                        //echo "&nbsp;...";
                    }
                    continue;
                }
                // последняя
                if($i == $pcount-1)
                {
                    if($pcurrent < $i-2)
                    {
                        $nav_value .= ($xajax?$this->template_interval_xajax:$this->template_interval);
                        //echo "&nbsp;...";
                    }
                    $nav_value .= ($xajax?$this->template_last_xajax:$this->template_last);
                    continue;
                }

                // пропускаем не нужные
                if($i<=$pcurrent-2)
                {
                    continue;
                }
                if($i>=$pcurrent+2)
                {
                    continue;
                }

                $nav_value .= str_replace("[item_link]", $query_string . "&navc=" . $this->page_count*$i, str_replace("[item_value]", $i+1, ($xajax?$this->template_item_xajax:$this->template_item)));
                $nav_value = str_replace("[nav_i]", $this->page_count*$i, $nav_value);

            }


            //$nav_value .= $this->template_last;
            if($pcurrent == $pcount-1)
            {
                $nav_value .= ($xajax?$this->template_next_active_xajax:$this->template_next_active);
            }
            else
            {
                $nav_value .= ($xajax?$this->template_next_xajax:$this->template_next);
            }


            $nav_value = str_replace("[first_value]", 1, $nav_value);
            $nav_value = str_replace("[last_value]", $this->last/$this->page_count+1, $nav_value);

            $nav_value = str_replace("[next_link]", $next, $nav_value);
            $nav_value = str_replace("[prev_link]", $prev, $nav_value);
            $nav_value = str_replace("[first_link]", $first, $nav_value);
            $nav_value = str_replace("[last_link]", $last, $nav_value);

            $nav_value = str_replace("[name_prev]", $MAIN->GetCurrentArrayLang($this->lang_values["prev"]), $nav_value);
            $nav_value = str_replace("[name_next]", $MAIN->GetCurrentArrayLang($this->lang_values["next"]), $nav_value);


            $this->view = str_replace("[nav_value]", $nav_value, ($xajax?$this->template_nav_xajax:$this->template_nav));

            $this->view = str_replace("[nav_first]", 0, $this->view);
            $this->view = str_replace("[nav_prev]", ($this->current - $this->page_count), $this->view);
            $this->view = str_replace("[nav_next]", ($this->current + $this->page_count), $this->view);
            $this->view = str_replace("[nav_last]", $this->last, $this->view);


            $this->view = $this->replace_root($this->view);

            if($view)
            {
                echo $this->view;
            }
        }
    }

    //---------------------------------------------------------------------------
    // Функция возвращает Limit для SQL запроса по параметрам класса
    function GetQuery()
    {
        return " LIMIT " . (integer)$this->current . ", " . (integer)$this->page_count;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает часть массива из исходного $source_array
    function GetArray($source_array)
    {
        $result_array = array();
        if(!$source_array)
        {
            return $result_array;
        }

        for($i=0;$i<count($source_array); $i++)
        {
            if($i < (integer)$this->current)
            {
                continue;
            }
            if($i > (integer)$this->current + (integer)$this->page_count)
            {
                break;
            }
            $result_array[count($result_array)] = $source_array[$i];
        }
        return $result_array;
    }


}
*/

class NavigationClass
{
    var $query; // запрос для которого берем записи (по которому навигация)
    var $navc; // текущая позиция для навигации
    var $navr; // количество записей отдаваемых по умолчанию

    var $last; // номер первой записи для последней страницы навигации

    var $num_rows; // общее количество строк в результате запроса

    var $view = '';

    // языковые значения для навигации
    var $lang_values = array(
        "first"=> array("&lt;&lt;&nbsp;"),
        "prev" => array("&nbsp;&lt;&nbsp;"),
        "next" => array("&nbsp;&gt;&nbsp;"),
        "last"=> array("&nbsp;&gt;&gt;"),
    );

//		<div id="nav">
//			<a href="#"><img src="./i/arr_l.gif" /></a>
//			<a href="#">1</a>
//			<span>(2)</span>
//			<a href="#">...</a>
//			<a href="#">6</a>
//			<a href="#">7</a>
//			<a href="#"><img src="./i/arr.gif" /></a></div>
//		</div>


    var $template_nav = '
<div id="nav" class="nav">[nav_value]</div>
';
    var $template_nav_xajax = '
<div id="nav" class="nav">[nav_value]</div>
';
    var $template_prev = '
  <a style="cursor:pointer;"  href="[prev_link]">[name_prev]</a>
  ';
    var $template_prev_xajax = '
  <a style="cursor:pointer;"  onclick="xajax_nav_go([nav_prev]);">[name_prev]</a>
  ';

    var $template_prev_active = '
  <a style="cursor:default;">[name_prev]</a>
  ';
    var $template_prev_active_xajax = '
  <a style="cursor:default;">[name_prev]</a>
  ';

    var $template_next = '
  <a style="cursor:pointer;" href="[next_link]">[name_next]</a>
  ';
    var $template_next_xajax = '
  <a style="cursor:pointer;" onclick="xajax_nav_go([nav_next]);">[name_next]</a>
  ';


    var $template_next_active = '
  <a style="cursor:default;">[name_next]</a>
  ';
    var $template_next_active_xajax = '
  <a style="cursor:default;">[name_next]</a>
  ';

    var $template_first = '
  <a style="cursor:pointer;"  class="digit" href="[first_link]">[first_value]</a>
  ';
    var $template_first_xajax = '
  <a style="cursor:pointer;" class="digit" onclick="xajax_nav_go([nav_first]);">[first_value]</a>
  ';
    var $template_first_active = '
  <span class="digit selected">([first_value])</span>
  ';
    var $template_first_active_xajax = '
  <span class="digit selected">([first_value])</span>
  ';

    var $template_item = '
  <a style="cursor:pointer;" class="digit" href="[item_link]">[item_value]</a>
  ';
    var $template_item_xajax = '
  <a style="cursor:pointer;" class="digit" onclick="xajax_nav_go([nav_i]);">[item_value]</a>
  ';
    var $template_item_active = '
  <span class="digit selected">([item_value])</span>
  ';
    var $template_item_active_xajax = '
  <span class="digit selected">([item_value])</span>
  ';

    var $template_last = '
  <a style="cursor:pointer;"  class="digit" href="[last_link]">[last_value]</a>
  ';
    var $template_last_xajax = '
  <a style="cursor:pointer;" class="digit" onclick="xajax_nav_go([nav_last]);">[last_value]</a>
  ';
    var $template_last_active = '
  <span class="digit selected">([last_value])</span>
  ';
    var $template_last_active_xajax = '
  <span class="digit selected">([last_value])</span>
  ';


    var $template_interval = '
  <span class="interval">...</span>
  ';
    var $template_interval_xajax = '
  <span class="interval">...</span>
  ';
    var $template_interval_active = '
  <span class="interval">...</span>
  ';
    var $template_interval_active_xajax = '
  <span class="interval">...</span>
  ';


    function replace_root($str)
    {
        global $root;
        return str_replace("[root]", $root, $str);
    }



    //---------------------------------------------------------------------------
    // конструктор класса
    // вычисляет значения navc и navr по GET параметрам $_GET[navc] и $_GET[navr] и устанавливает соответствующие значения для navc и navr
    function NavigationClass( $query, $navr, $num_rows=NULL )
    {
        global $_SESSION;
        global $_SERVER,$MAIN;
        //global $_GET;
        //var_dump($_SESSION[$_SERVER["PHP_SELF"]]["navc"]);
        $this->navr = $navr;

        //error_log('NavigationClass: $_SESSION: '.print_r($_SESSION, true));

        //$tmpkey = $_SERVER["PHP_SELF"]. ($_SERVER["QUERY_STRING"]?"?".$_SERVER["QUERY_STRING"]:"");
        $tmpkey = $_SERVER["REQUEST_URI"];

        if( !isset($_SESSION[$tmpkey]["navc"])
            || (isset($_SESSION[$tmpkey])&& $_SESSION[$tmpkey]["navc"] == "") )
        {
            $this->navc  = 0;
        }
        /*
            if( !isset($_GET["navr"])
             || $_GET["navr"] == "" ){
              $this->navr  = 10;
            }
        */

        if(!$query && $num_rows)
        {
            // просто навигация без запроса к БД
            $this->num_rows = $num_rows;
        }
        else
        {
            $this->query = $query;

            $db = new CDatabase();

            $result = $db->Query($this->query);

            //$result = mysql_query($this->query) or die(die_mysql_error_show($query));
            if( $result )
            {
                $this->num_rows = $db->RowCount();//mysql_num_rows($result);
                //var_dump($this);
            }
        }


        if( isset($_SESSION[$tmpkey]["navc"]) && $_SESSION[$tmpkey]["navc"] >= 0
            && ($_SESSION[$tmpkey]["navc"] % $navr) == 0 )
        {
            if( (integer)$_SESSION[$tmpkey]["navc"] < $this->num_rows )
            {
                $this->navc = (integer)$_SESSION[$tmpkey]["navc"];
            }
        }
        for( $i=$this->num_rows-1; $i>=0; $i--)
        {
            if( ($i % $this->navr) == 0 )
            {
                $this->last = $i;
                break;
            }
        }

        //error_log('NavigationClass: $this: '.print_r($this, true));

    }

    //---------------------------------------------------------------------------
    // Функция возвращает номер первой записи на текущей странице
    function get_current_page_first()
    {
        return $this->navc+1;
    }

    //---------------------------------------------------------------------------
    // Функция возвращает номер последней записи на текущей странице
    function get_current_page_last()
    {
        //var_dump($this);
        if($this->navc + 1 + $this->navr > $this->num_rows)
        {
            return $this->num_rows;
        }
        return $this->navc + $this->navr;
    }



    //---------------------------------------------------------------------------
    // функция вывода навигации на страницу
    function view_navigation($view = TRUE, $xajax = FALSE)
    {
        //global $_GET;
        global $_SESSION,$MAIN;
        $query_string = VN_DIR . "nav.php?sid=" .session_id();
        $query_string .= "&backurl=".urlencode($_SERVER["REQUEST_URI"]);

        $first = "";
        $last = "";
        $next = "";
        $prev = "";


        if( $query_string )
        {
            $first = $query_string . "&navc=0";
            $last = $query_string . "&navc=" . $this->last;
            $next = $query_string . "&navc=" . ($this->navc + $this->navr);
            $prev = $query_string . "&navc=" . ($this->navc - $this->navr);
        }



        if( ($this->navr) < $this->num_rows )
        {
            $nav_value = "";

            //$nav_value .= $this->template_first;


            // если количество страниц больше 5 то нужно пропустить некоторые
            $pcount = (integer)ceil(($this->num_rows/$this->navr));
            $pcurrent = (integer)round($this->navc/$this->navr, 0);

            if($pcurrent == 0)
            {
                $nav_value .= ($xajax?$this->template_prev_active_xajax:$this->template_prev_active);
            }
            else
            {
                $nav_value .= ($xajax?$this->template_prev_xajax:$this->template_prev);
            }


            for($i=0; $i<$pcount; $i++)
            {
                // текущая страница
                if($i==$pcurrent)
                {
                    $nav_value .= str_replace("[item_value]", $i+1, ($xajax?$this->template_item_active_xajax:$this->template_item_active));
                    continue;
                }

                // первая
                if($i == 0)
                {
                    $nav_value .= ($xajax?$this->template_first_xajax:$this->template_first);

                    if($pcurrent > $i+2)
                    {
                        $nav_value .= ($xajax?$this->template_interval_xajax:$this->template_interval);
                        //echo "&nbsp;...";
                    }
                    continue;
                }
                // последняя
                if($i == $pcount-1)
                {
                    if($pcurrent < $i-2)
                    {
                        $nav_value .= ($xajax?$this->template_interval_xajax:$this->template_interval);
                        //echo "&nbsp;...";
                    }
                    $nav_value .= ($xajax?$this->template_last_xajax:$this->template_last);
                    continue;
                }

                // пропускаем не нужные
                if($i<=$pcurrent-2)
                {
                    continue;
                }
                if($i>=$pcurrent+2)
                {
                    continue;
                }

                $nav_value .= str_replace("[item_link]", $query_string . "&navc=" . $this->navr*$i, str_replace("[item_value]", $i+1, ($xajax?$this->template_item_xajax:$this->template_item)));
                $nav_value = str_replace("[nav_i]", $this->navr*$i, $nav_value);

            }


            //$nav_value .= $this->template_last;
            if($pcurrent == $pcount-1)
            {
                $nav_value .= ($xajax?$this->template_next_active_xajax:$this->template_next_active);
            }
            else
            {
                $nav_value .= ($xajax?$this->template_next_xajax:$this->template_next);
            }


            $nav_value = str_replace("[first_value]", 1, $nav_value);
            $nav_value = str_replace("[last_value]", $this->last/$this->navr+1, $nav_value);

            $nav_value = str_replace("[next_link]", $next, $nav_value);
            $nav_value = str_replace("[prev_link]", $prev, $nav_value);
            $nav_value = str_replace("[first_link]", $first, $nav_value);
            $nav_value = str_replace("[last_link]", $last, $nav_value);

            $nav_value = str_replace("[name_prev]", $MAIN->GetCurrentArrayLang($this->lang_values["prev"]), $nav_value);
            $nav_value = str_replace("[name_next]", $MAIN->GetCurrentArrayLang($this->lang_values["next"]), $nav_value);


            $this->view = str_replace("[nav_value]", $nav_value, ($xajax?$this->template_nav_xajax:$this->template_nav));

            $this->view = str_replace("[nav_first]", 0, $this->view);
            $this->view = str_replace("[nav_prev]", ($this->navc - $this->navr), $this->view);
            $this->view = str_replace("[nav_next]", ($this->navc + $this->navr), $this->view);
            $this->view = str_replace("[nav_last]", $this->last, $this->view);


            $this->view = $this->replace_root($this->view);

            if($view)
            {
                echo $this->view;
            }
        }
    }

    //---------------------------------------------------------------------------
    // Функция возвращает Limit для SQL запроса по параметрам класса
    function query_navigation()
    {
        return " LIMIT " . (integer)$this->navc . ", " . (integer)$this->navr;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает часть массива из исходного $source_array
    function array_navigation($source_array)
    {
        $result_array = array();
        if(!$source_array)
        {
            return $result_array;
        }

        for($i=0;$i<count($source_array); $i++)
        {
            if($i < (integer)$this->navc)
            {
                continue;
            }
            if($i > (integer)$this->navc + (integer)$this->navr)
            {
                break;
            }
            $result_array[count($result_array)] = $source_array[$i];
        }
        return $result_array;
    }
}

?>