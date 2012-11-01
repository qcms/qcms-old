<?
//=============================================================================
// Определения классов сущностей и их списков

/**
 * @author Belyaev
 *
 */
class CArrayList
{
    // array ( "id1"  =>  array("__OBJECT" => "ddd", "__PARENT"=>"id0", "__CHILDREN" => array(...));
    var $array = array();
    var $object_key = "__OBJECT";
    var $parent_key = "__PARENT";
    var $children_key = "__CHILDREN";

    //var $array_path = array();


    /**
     * Функция возвращает id всех элементов списка
     * @param array $arr
     * @return multitype:
     */
    function GetIDs($arr=false)
    {
        $ret = array();
        if(!$arr)
        {
            $arr = $this->array;
            //var_dump($this->array);
        }
        //error_log(print_r($arr, true));
        //var_dump(array_keys($arr));
        if(array_keys($arr))
        {
            foreach($arr as $key => $value)
            {
                $ret[] = $key;
                if(is_array($value[$this->children_key]) && count($value[$this->children_key]))
                {
                    $ret = array_merge($ret, $this->GetIDs($value[$this->children_key]));
                }
            }
        }
        return $ret;
    }

    function Find($id, $current_childs=-1)
    {
        $ret = false;
        if($current_childs == -1)
        {
            $current_childs = $this->array;
        }

        if(count($current_childs))
        {
            //echo "<pre>"; print_r($current_childs);echo "</pre>";
            foreach($current_childs as $item_id=>$item)
            {
                //echo ".".$item_id.".";
                if($item_id == $id)
                {
                    // TODO: need to check
                    $ret = $item[$this->object_key];
                }

                if($ret)
                    return $ret;

                if(isset($item[$this->children_key])
                    && is_array($item[$this->children_key])
                    && count($item[$this->children_key]) )
                {
                    $ret = ($this->Find($id, $item[$this->children_key]));
                }

                if($ret)
                    return $ret;
            }
        }

        return $ret;
    }


    function &FindItem($id, &$current_childs)
    {
        $ret = false;
        if(count($current_childs))
        {
            //echo "<pre>"; print_r($current_childs);echo "</pre>";
            foreach($current_childs as $item_id=>&$item)
            {
                //echo ".".$item_id.".";
                if($item_id == $id)
                {
                    // TODO: need to check
                    //$this->array_path = array_merge($this->array_path, array($item_id));
                    $ret = & $item;
                }

                if($ret)
                    return $ret;

                if(isset($item[$this->children_key])
                    && is_array($item[$this->children_key])
                    && count($item[$this->children_key]) )
                {
                    //$this->array_path = array_merge($this->array_path, array($item_id));
                    $ret = & $this->FindItem($id, $item[$this->children_key]);
                }

                if($ret)
                    return $ret;
            }
        }

        return $ret;
    }


    /**
     * @param unknown_type $id
     * @param unknown_type $obj
     */
    function Add($id, $obj)
    {
        //echo "ID:"; var_dump($id);
        $this->array[$id] = array(
            $this->object_key=>$obj,
            $this->parent_key=>false,
            $this->children_key=>false
        );
    }

    /**
     * @param unknown_type $id
     * @param unknown_type $obj
     */
    function AddParent($id, $obj)
    {
        foreach ($this->array as  $item_id => $item)
        {
            $this->array[$this->parent_key] = $id;
        }

        $this->array = array(
            $id=>array(
                $this->object_key=>$obj,
                $this->parent_key=>false,
                $this->children_key=>$this->array
            )
        );
    }

    /**
     * @param unknown_type $id
     * @param unknown_type $obj
     * @param unknown_type $parent_id
     */
    function AddChild($id, $obj, $parent_id)
    {

        //array_walk ($this->array, array("CArrayList", "AddChildRecursive"), array("obj"=>$obj, "parent_id"=>$parent_id));
        //array_map (array("CArrayList", "AddChildRecursive"), $this->array, array("obj"=>$obj, "parent_id"=>$parent_id));

        //		
        //var_dump($id); echo "<br><br>";
        $arr = &$this->array;
        $item = &$this->FindItem($parent_id, $arr);
        //error_log(print_r($this->array_path, true));

        if(!isset($item[$this->children_key]) || !is_array($item[$this->children_key]))
        {
            $item[$this->children_key] = array();
        }
        //error_log("AAAA:".$id);
        //error_log("AAAA1:".$obj);

        $item[$this->children_key][$id] = array(
            $this->object_key=>$obj,
            $this->parent_key=>$parent_id,
            $this->children_key=>false
        );

        //var_dump($item);
        //var_dump($this);
    }

}


//-----------------------------------------------------------------------------
// Класс списка
class CList
{
    /**
     * @var array(CListItemBase)
     */
    var $items;

    function CList()
    {
        $items = array();
    }

    function Add($item, $parent_id=false)
    {
        if(intval($parent_id))
        {
            $parent_item = $this->Find($parent_id);
            if($parent_item)
            {
                $parent_item->childs[$item->id] = $item;
                $item->parent = $parent_item;
            }
        }
        else
        {
            $this->items[$item->id] = $item;
        }
    }

    function Find($id, $current_items = false)
    {
        $ret = false;

        if(!$current_items)
            $current_items = $this->items;

        //var_dump($current_items);
        if(is_array($current_items))
        {
            foreach ($current_items as $item)
            {
                //				var_dump($item, $id);
                //				echo "<br><br>AAAAAAAAAAAAAAAAAA";
                if($item->id === $id)
                {
                    return $item;
                }
                //				var_dump($item->GetCountChilds());
                if($item->GetCountChilds())
                {
                    $ret = $this->Find($id, $item->childs);

                }
                if($ret)
                {
                    return $ret;
                }
            }
        }
        return $ret;
    }

    function GetCount()
    {
        return count($this->items);
    }
}


/**
 * @author Belyaev
 *
 */
class CListItemBase
{
    var $id;
    var $parent = false;
    var $childs = array();

    function GetCountChilds()
    {
        if(is_array($this->childs))
            return count($this->childs);
        return 0;
    }

    function GetLevel()
    {
        $ret = 0;
        //var_dump($this->parent);
        $current = $this;
        while($current->parent)
        {
            $ret++;
            $current = $current->parent;
        }

        return $ret;
    }


    /**
     * @param CListItemBase $child
     */
    function AppendChild(&$child)
    {
        $this->childs[$child->id] = $child;
        $child->parent = $this;
    }

    /**
     * @param CListItemBase $parent
     */
    function AttachParent(&$parent)
    {
        $this->parent = $parent;
    }
}

/**
 * @author Belyaev
 * Класс элемента  списка в виде объекта
 */
class CListObject extends CListItemBase
{
    var $object;

    /**
     * @param int $id
     * @param CEntity $object
     * @param CListItemObject $parent
     * @param array(CListItemObject) $childs
     */
    function CListObject($id, $object, $parent=false, $childs=array())
    {
        $this->id = $id;
        $this->object = $object;
        $this->parent = $parent;
        $this->childs = $childs;
    }


}

//-----------------------------------------------------------------------------
// класс элемента списка
class CListItem extends CListItemBase
{
    var $name;

    function CListItem($id, $name, $parent=false, $childs=array())
    {
        $this->id = $id;
        $this->name = $name;
        $this->parent = $parent;
        $this->childs = $childs;
    }

}

//-----------------------------------------------------------------------------
// Класс списка сущностей
class CEntityList
{
    // основные переменные
    var $table;         // таблица которой принадлежат сущности
    var $index_suffix;  // суфикс для индексного поля

    /**
     * массив сущностей в списке
     * @var array
     * @var array[CEntity]
     * @var CEntity[]
     */
    var $list;
    var $keys;          // ключи которые будут использоваться для инициализации сущностей
    var $order_key;     // ключ по которому упорядочиваем список
    var $order_key_sort;// направление сортировки списка
    var $pagecount;     // количество итемов на странице

    /**
     * экземпляр класса навигации
     * @var NavigationClass
     */
    var $navigation;
    var $show_navigation; // показывать навигацию
    /*
     * @var CNavigationPager
     */
    var $navigation_pager;


    var $template; 			// шаблон по умолчанию для экземпляров списка
    var $template_first; 	// шаблон по умолчанию для экземпляров списка
    var $template_last; 	// шаблон по умолчанию для экземпляров списка

    // дополнительные переменные
    var $is_parent;     // признак того что у списка есть родитель
    var $table_parent;  // таблица элементов родителей для списка
    var $index_suffix_parent; // суффикс ключа таблицы родителей
    var $parent_id;     // ID родителя для списка
    var $parent;        // сущность родитель для списка
    var $key_parent;    // поле в котором лежит ссылка в объекте на родителя
    var $is_hierarchy;  // признак иерархии списка

    var $date_format;   // формат отображения даты/времени
    var $where;         // условие отбора в список
    var $limit;         // лимит для выборки (работает если нет навигации !$pagecount)
    var $query;					// полный текст запроса
    var $function;			// функция (переменная) для постобработки результата перед выводом 			

    /**
     * Текущий элемент списка
     * @var CEntity
     */
    var $item;					// текущее значение списка

    //---------------------------------------------------------------------------
    // Конструктор
    function CEntityList(
        $params = array(
            "table" => null,                // таблица списка
            "index_suffix"=>"id",           // суффикс индекса для элементов списка
            "keys"=>null,                   // поля для списка
            "pagecount" => 0,               // количество элементов списка на странице
            "show_navigation" => true,
            "order_key" => null,            // ключ сортировки
            "order_key_sort" => "ASC",      // сортировка списка
            "table_parent" => null,         // таблица родителя
            "index_suffix_parent" => null,  // суффикс индекса в таблице родителя
            "key_parent" => null,           // ключ в таблице списка для родителя
            "parent_id" => null,            // id родителя
            "date_format" => "d.m.Y H:i:s", // формат отображения даты/времени
            "template" => "none", 					// шаблон для отображения элемента списка
            "template_first" => "", 				// шаблон для отображения первого элемента списка
            "template_last" => "", 				// шаблон для отображения первого элемента списка
            "where" => null,								// условия (для SQL запроса)
            "limit" => null,								// пределы (для SQL запроса)
            "query" => null,								// SQL запрос
            "function" => null,							// функция (переменная) для постобработки результата перед выводом
        )
    )
    {
        global $MAIN;
        $this->table = $params["table"];
        $tableParams = $MAIN->GetTableParams($this->table);

        if(!isset($params["table"]) || !$params["table"])
        {
            die($MAIN->ErrorShow(array("message"=>"Не указана таблица для списка!")));
        }


        if(!isset($params["index_suffix"]))
        {
            $params["index_suffix"] = "id";
        }
        if(!isset($params["table_parent"]))
        {
            $params["table_parent"] =null;
        }
        if(!isset($params["index_suffix_parent"]))
        {
            $params["index_suffix_parent"] = "id";
        }
        if(!isset($params["key_parent"]))
        {
            $params["key_parent"] =null;
        }
        if(!isset($params["parent_id"]))
        {
            $params["parent_id"] =null;
        }
        if(!isset($params["pagecount"]))
        {
            $params["pagecount"] = 0;
        }
        if(!isset($params["show_navigation"]))
        {
            $params["show_navigation"] = true;
        }
        if(!isset($params["keys"]))
        {
            $params["keys"] = null;
        }
        if(!isset($params["date_format"]))
        {
            $params["date_format"] = "d.m.Y H:i:s";
        }
        if(!isset($params["where"]))
        {
            $params["where"] = null;
        }
        if(!isset($params["limit"]))
        {
            $params["limit"] = null;
        }
        if(!isset($params["query"]))
        {
            $params["query"] = null;
        }
        if(!isset($params["template"]))
        {
            $params["template"] = "none";
        }
        if(!isset($params["template_first"]))
        {
            $params["template_first"] = "";
        }
        if(!isset($params["template_last"]))
        {
            $params["template_last"] = "";
        }


        if(!isset($params["function"]))
        {
            $params["function"] = null;
        }


        if(!isset($params["order_key"]) && isset($tableParams["order"]) && is_array($tableParams["order"]) )
        {
            $obj = array_keys($tableParams["order"]);
            $params["order_key"] = $obj[0];
        }
        //var_dump($tableParams["order"]);
        if(!isset($params["order_key_sort"]) && isset($tableParams["order"]) && is_array($tableParams["order"]))
        {
            //var_dump($obj);
            $obj = array_values($tableParams["order"]);
            $params["order_key_sort"] = $obj[0];
        }


        $this->index_suffix = $params["index_suffix"];
        $this->keys = $params["keys"];
        $this->navigation = null;
        $this->show_navigation = $params["show_navigation"];

        // параметры сортировки по умочанию
        //$this->order_key = $this->table . "_order";
        //$this->order_key_sort = "ASC";
        $this->pagecount = 0;

        // инициализируем параметры сортировки
        if(isset($params["order_key"]))
        {
            $this->order_key = $params["order_key"];
        }
        if(isset($params["order_key_sort"]))
        {
            $this->order_key_sort = $params["order_key_sort"];
        }

        if(!$this->order_key)
        {
            if(isset($tableParams["order"]) && is_array($tableParams["order"]))
            {
                $order_keys = array_keys($tableParams["order"]);
                $this->order_key = $order_keys[0];
                $this->order_key_sort = $tableParams["order"][$this->order_key];
            }

        }
        /*
    var_dump($this->order_key);
    echo "<br />";
    var_dump($this->order_key_sort);
    echo "<br /><br />";
    */
        if(isset($params["pagecount"]))
        {
            $this->pagecount = $params["pagecount"];
        }

        // шаблон по умолчанию
        $this->template = $params["template"];
        $this->template_first = $params["template_first"];
        $this->template_last = $params["template_last"];

        // параметры родителя по умолчанию (нет родителя)
        // инициализируем параметры родителя, если есть
        $this->table_parent = $params["table_parent"];
        $this->index_suffix_parent = $params["index_suffix_parent"];
        $this->parent_id = $params["parent_id"];
        $this->key_parent = $params["key_parent"];
        $this->is_parent = false;
        $this->parent = null;
        $this->is_hierarchy = false;

        $this->date_format = $params["date_format"];
        $this->query = $params["query"];
        $this->where = $params["where"];
        $this->limit = $params["limit"];

        $this->function = $params["function"];


        // попробуем инициализировать родителя, если он есть
        if($this->table_parent && $this->parent_id && $this->key_parent && $this->index_suffix_parent)
        {
            $this->parent = new CEntity(array("table"=>$this->table_parent, "id"=>$this->parent_id, "index_suffix"=>$this->index_suffix_parent));
            if($this->parent && $this->parent->identity)
            {
                $this->is_parent = true; // он есть!
            }
        }
        // если есть только информация об иерархии то инициализируем ее
        else if($this->table_parent && $this->table_parent == $this->table && $this->key_parent)
        {
            $this->is_hierarchy = true;
        }

        // пересортировка списка
        if(isset($tableParams["autoreorder"]) && $tableParams["autoreorder"] == "true")
            $this->Reorder();

        // Инициализация списка
        $this->list = array();

        $this->list = $this->GetList();

    }


    //---------------------------------------------------------------------------
    // Функция для отображения списка с дополнительными полями (перемещение элементов, редактирование и т.п.)
    function ViewEditList($params = array("keys"=>array(), "template"=>"default", "actions"=>array(), "actions_table"=>false))
    {
        $ret = "";
        global $MAIN;

        // если не выбраны параметры установим значения по умолчанию
        if(!array_key_exists("keys",$params))
        {
            $params["keys"] = array();
        }
        if(!array_key_exists("template",$params))
        {
            $params["template"] = "default";
        }
        if(!array_key_exists("actions",$params))
        {
            $params["actions"] = array();
        }
        if(!array_key_exists("actions_table",$params))
        {
            $params["actions_table"] = false;
        }

        $templatePath = $MAIN->GetTemplatePath($params["template"], "vieweditlist", "default", true);

        global $_template_list_header,
               $_template_list_footer,
               $_template_list_tr_header,
               $_template_list_tr_footer,
               $_template_list_parent_level0,
               $_template_list_parent_level1,
               $_template_list_th_item,
               $_template_list_td_item;
        include($templatePath);


        if($this->pagecount && $this->navigation)
        {
            $this->navigation->view_navigation(false);
            $ret .= $this->navigation->view;
        }

        $ret .= $_template_list_header;


        // заголовки списка
        $ret .= $_template_list_tr_header;
        // названия списка
        $tableFieldsParams = $MAIN->GetTableFieldsParams($this->table);
        if(!count($params["keys"]))
        {
            $params["keys"] = array_keys($tableFieldsParams);
        }
        //var_dump($keys);

        foreach($params["keys"] as $key)
        {
            if(isset($tableFieldsParams[$key]))
                $ret .= str_replace("[item]",$MAIN->GetCurrentArrayLang($tableFieldsParams[$key]["name"]),$_template_list_th_item);
            else
                $ret .= str_replace("[item]","&nbsp;",$_template_list_th_item);
        }
        $ret .= str_replace("[item]","&nbsp;",$_template_list_th_item);

        $ret .= $_template_list_tr_footer;

        // Элементы списка
        if(isset($this->list) && $this->list && count($this->list))
        {
            foreach($this->list as $item)
            {
                $this->item = $item;

                $ret .= $_template_list_tr_header;
                $item_view = "";
                foreach($params["keys"] as $key)
                {
                    $template = "none";
                    if(isset($tableFieldsParams[$key]))
                    {
                        switch($tableFieldsParams[$key]["type"])
                        {
                            case "datetime":
                                $template = '[date:' . $key.']';
                                break;
                            case "image":
                                $template = '<img src="'.VIEW_IMAGE.'?id=[image:' . $key.']" />';
                                break;
                            case "file":
                                $template = '';
                                $file = $this->item->GetFile($key);
                                if($file->file_id)
                                {

                                    $template .= 'Размер файла: ' . $file->get_file_length();

                                    //			          if($file)
                                    //			          {
                                    //			            $template .= "<br>Имя файла: " . $file->file_filename();
                                    //			          }
                                    //			          if($file->file_type)
                                    //			          {
                                    //			            $template .= "<br>Тип файла: " . $file->file_type;
                                    //			          }

                                    $template .= '<br><a href="' . VN_ADMIN . '/file.php?id=' . $file->file_id . '" target="_blank">Открыть</a><br>
				<a href="' . VN_ADMIN . '/file.php?action=file&id=' . $file->file_id . '" target="_blank">Скачать</a>';

                                }
                                else
                                {
                                    $template .= "Нет файла";
                                }

                                break;
                            case "string":
                            case "number":
                                $template = '[header:' . $key.']';
                                break;
                            case "text":
                                $template = '[text:' . $key.']';
                                break;
                            case "checkbox":
                                $template = '[checkbox:' . $key.']&nbsp;';
                                break;
                            case "select":
                                if(isset($tableFieldsParams[$key]["params"]["select"]["values"]))
                                {
                                    $template = '[listitem:_values_:'.$tableFieldsParams[$key]["params"]["select"]["values"].':' . $key.']';
                                }
                                else
                                {
                                    $template = '[listitem:'.$tableFieldsParams[$key]["params"]["select"]["table"]["name"].':'.$tableFieldsParams[$key]["params"]["select"]["table"]["namefield"].':' . $key.']';
                                }
                                /*
	              {
	            		
	              	$select_id = $item->GetHeader($key);
	              	$select_entity = new CEntity(array("table"=>$tableFieldsParams[$key]["params"]["select"]["table"]["name"], "id"=>$select_id));
	              	$template = $select_entity->GetHeader($tableFieldsParams[$key]["params"]["select"]["table"]["namefield"]);
	              	unset($select_entity);
	              	unset($select_id);
	            	}
								*/
                                break;
                        }
                    }
                    //var_dump($template);
                    //var_dump($this->parent);

                    $levelstring = "";

                    if($key == $this->table."_name" && $this->is_hierarchy)
                    {
                        $level = $this->GetLevel($item);
                        //var_dump($level);

                        if($level > 0)
                        {
                            for($i=0; $i<$level-1; $i++)
                            {
                                $levelstring .= $_template_list_parent_level1;
                            }
                            $levelstring .= $_template_list_parent_level0;
                        }
                        //$_template_list_td_item = $levelstring.$_template_list_td_item;
                    }
                    $item_view .= str_replace("[item]", $levelstring . $item->View(array("template"=>$template)), $_template_list_td_item);
                }


                // действия над элементами списка тут
                $actions = "";
                $tableParams = $MAIN->GetTableParams($this->table);
                $table = $params["actions_table"]?$params["actions_table"]:$this->table;
                if(isset($tableParams["admin"]) && is_array($tableParams["admin"])
                    && is_array($params["actions"]) && count($params["actions"]) )
                {
                    foreach($params["actions"] as $action)
                    {
                        $actions .= '<a href="'.$MAIN->GetAdminPageUrl($table, $action, array("id"=>$item->identity), true, false) . '">' . $MAIN->GetAdminPageName($table, $action) . '</a>&nbsp;';
                        //$actions .= '<a href="'.$MAIN->ReplaceTemplateGetParams($tableParams["admin"][$action]["url_template"], array("id" => $item->identity), true). '">' . $MAIN->GetCurrentArrayLang($tableParams["admin"][$action]["name"]) . '</a>&nbsp;';


                        //str_replace("[id]", $item->identity, $tableParams["admin"][$action]["url_template"]) . '">' . $MAIN->GetCurrentArrayLang($tableParams["admin"][$action]["name"]) . '</a>&nbsp;';
                        //var_dump($action);
                    }
                }

                $item_view .= str_replace("[item]", $actions, $_template_list_td_item);
                $item_view .= $_template_list_tr_footer;

                if($this->function && is_callable($this->function))
                {
                    $function = $this->function;
                    $function($this, $item_view);
                }

                $ret .= $item_view;

            }
        }

        $ret .= $_template_list_footer;

        if($this->pagecount && $this->navigation)
        {
            $this->navigation->view_navigation(false);
            $ret .= $this->navigation->view;
        }


        return $ret;
    }


    /**
     * Функция редактирования списка элементов
     * @param string $file
     * @param array $params
     * @return string
     */
    function ViewEditListEx($file, $params = array("keys"=>array(), "template"=>"default", "actions"=>array(), "actions_table"=>false))
    {
        $ret = "";

        global $MAIN;

        // если не выбраны параметры установим значения по умолчанию
        if(!isset($params["keys"]) || !is_array($params["keys"]))
        {
            $params["keys"] = array();
        }
        if(!isset($params["template"]))
        {
            $params["template"] = "default";
        }
        if(!isset($params["actions"]))
        {
            $params["actions"] = array();
        }
        if(!isset($params["actions_table"]))
        {
            $params["actions_table"] = false;
        }

        //$templatePath = $MAIN->GetTemplatePath($params["template"], "vieweditlist", "default", true);
        //$template_file_name = $MAIN->GetTemplateFile($file, $params["template"], "page", "vieweditlist.php");

        global $vieweditlist_items,
               $vieweditlist_navigation_pager,
               $vieweditlist_ishierarchy;

        $template_content = $MAIN->GetTemplateContent($file, $params["template"], "page", "vieweditlist_item.php");
        $vieweditlist_items = $this->ViewEditListItemsEx($file, $params["template"], "page", $params["keys"], $params["actions"], $template_content);
        $vieweditlist_ishierarchy = $this->is_hierarchy;

        if($this->pagecount > 0)
        {
            $vieweditlist_navigation_pager = $this->navigation_pager->View($file, $params["template"], "page");
        }

        //error_log(print_r($vieweditlist_items, true));
        //$ret .= $template_file_name;


        $ret .= $MAIN->ShowTemplate($file, $params["template"], "page", "vieweditlist.php");



        return $ret;
    }

    /**
     * @param $file
     * @param $template
     * @param $component
     * @param $keys
     * @param $actions
     * @param $template_content
     * @param bool $parent_item
     * @return string
     */
    function ViewEditListItemsEx($file, $template, $component, $keys, $actions, $template_content, $parent_item = false)
    {
        global $MAIN;
        $ret = "";

        if(!is_array($keys) || !count($keys))
        {
            return $ret;
        }

        //$template_content = $MAIN->GetTemplateContent($file, $template, $component, "vieweditlist_item.php");

        foreach($this->list as $entity_item)
        {
            //error_log("A.1");
            if(!$parent_item && !$entity_item->GetParentId())
            {
                // уровень 0 для иерархии или простых таблиц
                $ret .= $this->ViewEditListItemEx($file, $template, $component,$keys, $actions, $template_content, $parent_item, $entity_item);
            }
            elseif($this->is_hierarchy && $parent_item && $parent_item->identity == $entity_item->GetParent())
            {
                // уровень > 0 при наличии иерархии списка
                // получаем список подчиненный $parent_item
                $ret .= $this->ViewEditListItemEx($file, $template, $component,$keys, $actions, $template_content, $parent_item, $entity_item);
            }
            elseif(!$parent_item && !$this->is_hierarchy)
            {
                // без иерархии но при наличии родителя (подчиненная таблица)
                $ret .= $this->ViewEditListItemEx($file, $template, $component,$keys, $actions, $template_content, false, $entity_item);

            }
        }

        return $ret;
    }

    /**
     * @param $file string
     * @param $template string
     * @param $component string
     * @param $keys array
     * @param $actions array
     * @param $template_content string
     * @param $parent_item CEntity | null
     * @param $item CEntity
     * @return string
     */
    function ViewEditListItemEx($file, $template, $component, $keys, $actions, $template_content, $parent_item, $item)
    {
        global $MAIN;
        global $vieweditlist_item_name,
               $vieweditlist_item_subinfo,
               $vieweditlist_item_actions,
               $vieweditlist_item_sublist;

        $ret = "";

        $vieweditlist_item_name = $item->GetFieldView($keys[0]);
        $vieweditlist_item_subinfo = "";
        $vieweditlist_item_actions = "";
        //$vieweditlist_item_sublist = "{variable:vieweditlist_item_sublist}";
        foreach($actions as $action)
        {
            $url = $MAIN->GetAdminPageUrl($this->table, $action, array("id"=>$item->identity), true, true);
            $name = $MAIN->GetAdminPageName($this->table, $action);
            $vieweditlist_item_actions .= <<<EOT
<a href="{$url}">{$name}</a>
EOT;
        }

        if(count($keys) > 1)
        {
            global $vieweditlist_subinfo_items;
            $vieweditlist_subinfo_items = "";


            $vieweditlist_item_subinfo_template_content = $MAIN->GetTemplateContent($file, $template, "page", "vieweditlist_subinfo.php");
            $vieweditlist_item_subinfo_item_template_content = $MAIN->GetTemplateContent($file, $template, "page", "vieweditlist_subinfo_item.php");

            $vieweditlist_item_subinfo_item_image_template_content = $MAIN->GetTemplateContent($file, $template, "page", "vieweditlist_subinfo_item_image.php");

            for($i=1; $i<count($keys); $i++)
            {
                // todo: добавить информацию о полях сущности

                global $vieweditlist_subinfo_item_name, $vieweditlist_subinfo_item_value;
                $vieweditlist_subinfo_item_name = $MAIN->GetAdminEntityFieldName($this->table, $keys[$i]);
                if(!$vieweditlist_subinfo_item_name && $keys[$i])
                {
                    $vieweditlist_subinfo_items .= $keys[$i];
                    unset($vieweditlist_subinfo_item_name);
                    unset($vieweditlist_subinfo_item_value);
                    continue;
                }

                if($MAIN->GetTableFieldParam($item->table,$keys[$i], "type") == "image")
                {
                    global $vieweditlist_subinfo_item_image_id,
                           $VIEW_IMAGE,
                           $vieweditlist_subinfo_item_image_sizex,
                           $vieweditlist_subinfo_item_image_sizey
                           ;
                    $VIEW_IMAGE = VIEW_IMAGE;
                    $vieweditlist_subinfo_item_image_id = $vieweditlist_subinfo_item_image_sizex = $vieweditlist_subinfo_item_image_sizey = "";

                    $img = $item->GetImage($keys[$i]);
                    if($img->image_id)
                    {
                        $vieweditlist_subinfo_item_image_id = $img->image_id;
                        $vieweditlist_subinfo_item_image_sizex = $img->GetSizeX();
                        $vieweditlist_subinfo_item_image_sizey = $img->GetSizeY();
                    }
                    unset($img);

                    $vieweditlist_subinfo_item_value = $MAIN->ShowTemplateContent($vieweditlist_item_subinfo_item_image_template_content);
                }
                else
                {
                    $vieweditlist_subinfo_item_value = $item->GetFieldView($keys[$i]);
                }


                $vieweditlist_subinfo_items .= $MAIN->ShowTemplateContent($vieweditlist_item_subinfo_item_template_content);
                //$vieweditlist_item_subinfo .= "____{$keys[$i]}:_".$item->GetField($keys[$i]);
                unset($vieweditlist_subinfo_item_name);
                unset($vieweditlist_subinfo_item_value);
            }

            if($this->function)
            {
                $this->item = $item; // установим текущий элемент

                if($this->function && is_callable($this->function))
                {
                    $function = $this->function;
                    $function($this, $vieweditlist_subinfo_items);
                }
            }
            global $vieweditlist_item_subinfo;
            $vieweditlist_item_subinfo = $MAIN->ShowTemplateContent($vieweditlist_item_subinfo_template_content);

            unset($vieweditlist_item_subinfo);
            unset($vieweditlist_subinfo_items);
        }


        $vieweditlist_item_sublist = "{variable:vieweditlist_item_sublist}";
        $ret = $MAIN->ShowTemplateContent($template_content);

        $vieweditlist_item_sublist = $this->ViewEditListItemsEx($file, $template, $component, $keys, $actions, $template_content, $item);
        if($vieweditlist_item_sublist)
        {
            $vieweditlist_item_sublist = <<<EOT
<ul>
$vieweditlist_item_sublist
</ul>
EOT;
        }
        $ret  = $MAIN->ShowTemplateContent($ret);

        return $ret;
    }

    /*
    function ViewEditListSubItemsEx($parent_item, $template_content)
    {
        global $MAIN,
               $keys,
               $actions;
        $ret = "";

        if(!is_array($keys) || !count($keys))
        {
            return $ret;
        }

        foreach($this->list as $item)
        {
            if(!$parent_item)
            {
                global $vieweditlist_item_name,
                       $vieweditlist_item_subinfo,
                       $vieweditlist_item_actions,
                       $vieweditlist_item_sublist;

            }

            $vieweditlist_item_name = $item->GetField($keys[0]);
            $vieweditlist_item_subinfo = "";
            $vieweditlist_item_actions = "";
            $vieweditlist_item_sublist = "";
            foreach($actions as $action)
            {
                $url = $MAIN->GetAdminPageUrl($this->table, $action, array("id"=>$item->identity));
                $name = $MAIN->GetAdminPageName($this->table, $action);
                $vieweditlist_item_actions .= <<<EOT
<a href="{$url}">{$name}</a>
EOT;
            }

            if(count($keys) > 1)
            {
                for($i=1; $i<count($keys); $i++)
                {
                    // todo: добавить информацию о полях сущности
                    //$vieweditlist_item_subinfo .= $item->GetField($keys[$i]);
                }
            }

            if($parent_item)
            {
                $parent = $item->GetParent();
                if($parent && $parent_item->identity == $parent->identity)
                {
                    //$ret .= "<ul>";
                    $ret .= $MAIN->ShowTemplate($file, $template, $component, "vieweditlist_item.php");
                    //$ret .= "</ul>";
                    error_log("AAAAAAAAAAAA");
                }
                //                $vieweditlist_item_sublist .= "<ul>";
                //                $vieweditlist_item_sublist .= $this->ViewEditListItemsEx($file, $template, $component, $keys, $actions, $item);
                //                $vieweditlist_item_sublist .= "</ul>";
                error_log("AAAAAAAAAAAABBBBBBBBB");
                continue;
            }
            //            elseif(!$parent_item && $this->GetLevel($item) != 0)
            //            {
            //                error_log("CCCCCCCCCCCCCCCCCCCCCCCC");
            //                continue;
            //            }
            error_log("DDDDDDDDDDDDDDDDD");

            $vieweditlist_item_sublist .= $this->ViewEditListItemsEx($file, $template, $component, $keys, $actions, $item);
            if(strlen($vieweditlist_item_sublist))
            {
                $vieweditlist_item_sublist = <<<EOT
<ul>
{$vieweditlist_item_sublist}
</ul>
EOT;

            }

            $ret .= $MAIN->ShowTemplate($file, $template, $component, "vieweditlist_item.php");

        }

        return $ret;
    }
    */


    //---------------------------------------------------------------------------
    // Функция для отображения списка
    function ViewList(
        $params=array(
            "template" => "",
            "template_first" => "",
            "template_last" => "",
            "header" => "", // перед навигацией
            "footer" => "", // после навигации
            "header_after_nav" => "", // после навигации
            "footer_before_nav" => "", // перед навигацией
        )

    )
    {
        if(!isset($params["template"]) || !$params["template"])
        {
            $params["template"] = $this->template;
        }
        if(!isset($params["template_first"]) || !$params["template_first"])
        {
            $params["template_first"] = $this->template_first;
        }
        if(!isset($params["template_last"]) || !$params["template_last"])
        {
            $params["template_last"] = $this->template_last;
        }
        if(!isset($params["header"]))
        {
            $params["header"] = "";
        }
        if(!isset($params["footer"]))
        {
            $params["footer"] = "";
        }
        if(!isset($params["header_after_nav"]))
        {
            $params["header_after_nav"] = "";
        }
        if(!isset($params["footer_before_nav"]))
        {
            $params["footer_before_nav"] = "";
        }

        $ret = "";

        if(isset($params["header"]) && $params["header"])
        {
            $ret .= $params["header"];
        }

        if($this->pagecount && $this->navigation && $this->show_navigation)
        {
            $this->navigation->view_navigation(false);
            $ret .= $this->navigation->view;
        }

        if(isset($params["header_after_nav"]) && $params["header_after_nav"])
        {
            $ret .= $params["header_after_nav"];
        }
        /*
    if(!isset($params["template"]))
    {
      $params["template"] = "none";
    }
		*/
        //var_dump($this->list);
        if($this->list)
        {
            //$first = true;
            //foreach($this->list as $item)
            for($i=0; $i<count($this->list); $i++)
            {
                $item = $this->list[$i];
                //var_dump($item);
                $this->item = $item;
                $item_view = "";
                if($i==0
                    && isset($params["template_first"])
                    && $params["template_first"])
                {
                    $item_view .= $item->View(array("template"=>$params["template_first"]));
                }
                elseif($i == ($this->GetCount()-1)
                    && isset($params["template_last"])
                    && $params["template_last"])
                {
                    $item_view .= $item->View(array("template"=>$params["template_last"]));
                }
                else
                {
                    if(isset($params["template"]))
                    {
                        $item_view .= $item->View(array("template"=>$params["template"]));
                    }
                    else
                    {
                        $item_view .= $item->View(); // вывод с шаблоном по умолчанию
                    }
                }

                if($this->function && is_callable($this->function))
                {
                    $function = $this->function;
                    $function($this, $item_view);
                }

                $ret .= $item_view;

                //        if($first)
                //      	{
                //      		$first = false;
                //      	}
            }
        }

        if(isset($params["footer_before_nav"]) && $params["footer_before_nav"])
        {
            $ret .= $params["footer_before_nav"];
        }

        if($this->pagecount && $this->navigation && $this->show_navigation)
        {
            $this->navigation->view_navigation(false);
            $ret .= $this->navigation->view;
        }

        if(isset($params["footer"]) && $params["footer"])
        {
            $ret .= $params["footer"];
        }



        return $ret;
    }

    //---------------------------------------------------------------------------
    // Функция пересортировки списка в рамках родителя, если есть
    function Reorder()
    {
        global $MAIN;
        if($MAIN->is_admin)
        {
            //var_dump($this->is_parent);exit;
            if($this->is_hierarchy)
            {
                //echo "0000000000000"; exit;
                //admin_reorder_table($this->table, $this->index_suffix, $this->parent->identity, $this->index_suffix_parent);

                //admin_reorder_table_recursion($this->table, "0", $this->key_parent);
                $this->ReorderTableRecursion($this->table, $this->key_parent, "0");
            }
            else if($this->is_parent)
            {
                //echo "AAAAAAAAA";
                //var_dump($this->index_suffix);var_dump($this->parent_id);var_dump($this->key_parent);exit;

                //admin_reorder_table($this->table, $this->index_suffix, $this->parent_id, $this->key_parent);
                $this->ReorderTable($this->table, $this->index_suffix, $this->key_parent, $this->parent_id);
            }
            else
            {
                //echo "BBBBBBBBB";

                //admin_reorder_table($this->table, $this->index_suffix);
                $this->ReorderTable($this->table, $this->index_suffix);
            }
        }
    }

    //---------------------------------------------------------------------------
    // Функция перемещения элемента списка вверх в рамках родителя, если есть
    function MoveUp($id)
    {
        if($this->is_parent)
        {
            //error_log("1");
            //            admin_move_updown($this->table, $id, NULL,
            //                "up", $this->index_suffix, $this->parent->identity, $this->key_parent);
            $this->MoveUpDownEx("up", $this->table, $id, $this->index_suffix, $this->key_parent, $this->parent->identity);
        }
        else if($this->is_hierarchy)
        {
            //error_log("2");
            $item_by_id = null;
            $parent_id = null;

            foreach($this->list as $item)
            {
                if($item->identity == $id)
                {
                    $item_by_id = $item;
                    break;
                }
            }



            if($item_by_id)
            {
//                error_log("2.1");
//                error_log('$this->key_parent: '.$this->key_parent);
                $parent_id = $item_by_id->GetHeader($this->key_parent);
//                error_log('$parent_id: '.$parent_id);
                //                admin_move_updown($this->table, $id, NULL,
                //                    "up", $this->index_suffix, $parent_id, $this->key_parent/*, $this->table."_".$this->index_suffix_parent*/);
                $this->MoveUpDownEx("up", $this->table, $id, $this->index_suffix, $this->key_parent, $parent_id);
            }
        }
        else
        {
            //error_log("3");
            //            admin_move_updown($this->table, $id, NULL,
            //                "up", $this->index_suffix);
            $this->MoveUpDownEx("up", $this->table, $id, $this->index_suffix);
        }
    }

    //---------------------------------------------------------------------------
    // Функция перемещения элемента списка вниз в рамках родителя, если есть
    function MoveDown($id)
    {

        if($this->is_parent)
        {
            $this->MoveUpDownEx("down", $this->table, $id, $this->index_suffix, $this->key_parent, $this->parent->identity);
            //            admin_move_updown($this->table, $id, NULL,
            //                "down", $this->index_suffix, $this->parent->identity, $this->key_parent);
        }
        else if($this->is_hierarchy)
        {
            $item_by_id = null;
            $parent_id = null;

            foreach($this->list as $item)
            {
                if($item->identity == $id)
                {
                    $item_by_id = $item;
                    break;
                }
            }



            if($item_by_id)
            {
                $parent_id = $item_by_id->GetHeader($this->key_parent);
                //                admin_move_updown($this->table, $id, NULL,
                //                    "down", $this->index_suffix, $parent_id, $this->key_parent/*, $this->table."_".$this->index_suffix_parent*/);
                $this->MoveUpDownEx("down", $this->table, $id, $this->index_suffix, $this->key_parent, $parent_id);

            }
        }
        else
        {
            $this->MoveUpDownEx("down", $this->table, $id, $this->index_suffix);
            //            admin_move_updown($this->table, $id, NULL,
            //                "down", $this->index_suffix);
        }
    }


    /**
     * Функция передвигает строку заданной таблицы вверх или вниз
     * @param string $updown            - признак куда перемещать "up" | "down"
     * @param string $table             - имя таблицы
     * @param string $id                - идентификатор строки
     * @param string $id_field_suffix          - имя поля идентификатора
     * @param string $parent_field      - имя поля родителя в таблице $table
     * @param string $parent_id         - значение поля родитель в таблице $table
     * @param string $where             - дополнительные условия WHERE
     */
    function MoveUpDownEx($updown,
                          $table,
                          $id,
                          $id_field_suffix="id",
                          $parent_field="",
                          $parent_id="",
                          $where=""
    )
    {
        $tempvalue = 100000;

        // найдем текущую запись в БД
        $query = "
SELECT " . $table . "_" . $id_field_suffix . ", ". $table . "_order
FROM  " . DATABASE_PREFIX . $table . "
WHERE NULL IS NULL
AND " . $table . "_" . $id_field_suffix . " = '" .$id. "'";

        if($parent_id && $parent_field)
        {
            $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
        }

        if($where)
        {
            // дополнительное условие
            $query .= "
AND " . $where;

        }
        // нужна только одна строка
        $query .= "
LIMIT 0,1
";

        //var_dump($query);exit;

        $db = new CDatabase();
        $db->Query($query);
        //error_log(print_r($query,true));

        if($row = $db->NextAssoc())
        {
            $current_order = $row[$table . "_order"];
            //var_dump($current_order);exit;

            // найдем строку до или после текущей строки
            $query = "
SELECT " . $table . "_" . $id_field_suffix . ", ". $table . "_order
FROM  " . DATABASE_PREFIX . $table . "
WHERE NULL IS NULL";

            if(strlen($parent_id) && strlen($parent_field))
            {
                $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
            }
            switch($updown)
            {
                case "up":
                    $query .= "
AND " . $table . "_order < '" . $current_order . "'";
                    break;
                case "down":
                    $query .= "
AND " . $table . "_order > '" . $current_order . "'";
                    break;
                default:
                    $query .= "
AND NULL IS NOT NULL";
            }
            // нужна только одна строка
            $query .= "
ORDER BY " . $table . "_order " . ($updown=="up"?"DESC":"ASC") . "
LIMIT 0,1
";

            //var_dump($query);exit;

            $db->Query($query);
            //error_log(print_r($query,true));

            //$result = mysql_query($query) or die(die_mysql_error_show($query));
            if($row = $db->NextAssoc())
            {

                $prevnext_id  = $row[$table . "_" . $id_field_suffix];
                $prevnext_order  = $row[$table . "_order"];

                // поменяем строки местами
                $temp = $current_order;
                $current_order = $prevnext_order;
                $prevnext_order = $temp;


                if($updown == "up")
                {
                    // поменяем местами
                    $query = "
UPDATE " . DATABASE_PREFIX . $table . " SET " . $table . "_order = '" . $current_order . "'
WHERE " . $table . "_" . $id_field_suffix . " = '" . $id . "'";
                    if(strlen($parent_id) && strlen($parent_field))
                    {
                        $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
                    }
                    $db->Query($query);
                    //error_log(print_r($query,true));


                    $query = "
UPDATE " . DATABASE_PREFIX . $table . " SET ". $table . "_order = '" . $prevnext_order . "'
WHERE ". $table. "_" . $id_field_suffix ." = '" . $prevnext_id . "'";
                    if(strlen($parent_id) && strlen($parent_field))
                    {
                        $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
                    }

                    $db->Query($query);
                    //error_log(print_r($query,true));


                }
                elseif($updown == "down")
                {
                    // поменяем местами
                    $query = "
UPDATE " . DATABASE_PREFIX . $table . " SET ". $table . "_order = '" . $prevnext_order . "'
WHERE ". $table. "_" . $id_field_suffix ." = '" . $prevnext_id . "'";
                    if(strlen($parent_id) && strlen($parent_field))
                    {
                        $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
                    }
                    $db->Query($query);
                    //error_log(print_r($query,true));


                    $query = "
UPDATE " . DATABASE_PREFIX . $table . " SET " . $table . "_order = '" . $current_order . "'
WHERE " . $table . "_" . $id_field_suffix . " = '" . $id . "'";
                    if(strlen($parent_id) && strlen($parent_field))
                    {
                        $query .= "
AND " . $parent_field . " = '" . $parent_id . "'";
                    }
                    $db->Query($query);
                    //error_log(print_r($query,true));
                }

            }

        }

        //admin_reorder_table($table, $id_field="id", $parent_id="", $parent_field="id");
    }


    /**
     * Функция сортировки таблицы
     * @param $table
     * @param string $id_field_suffix
     * @param string $parent_field
     * @param string $parent_id
     * @param string $where
     */
    function ReorderTable($table, $id_field_suffix="id", $parent_field="", $parent_id="", $where="")
    {
        // сделаем все записи попорядку
        $query = "
SELECT ". $table . "_" . $id_field_suffix . ", IF(ISNULL(". $table . "_order)=1,1000000000, ". $table . "_order) AS ". $table . "_order
FROM  " . DATABASE_PREFIX . $table . "
WHERE NULL IS NULL";


        if($parent_field && $parent_id)
        {
            $query .= "
AND $parent_field = '$parent_id'";
        }

        if($where)
        {
            // дополнительное условие
            $query .= "
AND " . $where;

        }

        $query .= "
ORDER BY " . $table . "_order ASC;";


        $order = 1;

        $db=new CDatabase();
        $result = $db->Query($query);

        while($result && $row = $db->NextAssoc($result))
        {
            $current_id = $row[$table . "_" . $id_field_suffix];
            $query_reorder = "

UPDATE " . DATABASE_PREFIX . $table . " SET " . $table . "_order = '" . $order . "'
WHERE " . $table . "_" . $id_field_suffix . " = '" . $current_id . "';";
            $order++;

            $db1=new CDatabase();
            $result = $db1->Query($query_reorder);
        }
    }


    /**
     * Функция рекурсивной сортировки иерархической
     * @param $table
     * @param $parent_field
     * @param $parent_id
     */
    function ReorderTableRecursion($table, $parent_field, $parent_id)
    {
        // сделаем все записи попорядку
        $query = "
SELECT ". $table . "_id, IF(ISNULL(". $table . "_order)=1,1000000000, ". $table . "_order) AS ". $table . "_order
FROM  " . DATABASE_PREFIX . $table . "
WHERE NULL IS NULL";

        $query .= "
AND ($parent_field = '$parent_id'";
        if($parent_id == "0")
        {
            $query .= " OR $parent_field IS NULL";
        }
        $query .= ")";

        $query .= "
ORDER BY " . $table . "_order ASC;";

        $order = 1;
        $db = new CDatabase();
        $db->Query($query);
        while($row = $db->NextAssoc())
        {
            $current_id = $row[$table . "_id"];
            $query_reorder = "
UPDATE " . DATABASE_PREFIX . $table . " SET " . $table . "_order = '" . $order . "'
WHERE " . $table . "_id" . " = '" . $current_id . "';";
            $order++;

            $db2 = new CDatabase();
            $db2->Query($query_reorder);
            $db2->Free();

            //admin_reorder_table_recursion($table, $row[$table . "_id"], $parent_field);
            $this->ReorderTableRecursion($table, $parent_field, $row[$table . "_id"]);
        }
        $db->Free();
    }

    //---------------------------------------------------------------------------
    // Функция возвращает количество элементов в списке
    function GetCount()
    {
        if(is_array($this->list))
        {
            return count($this->list);
        }
        return 0;
    }

    //---------------------------------------------------------------------------
    // Функция возвращает уровень элемента в иерархическом списке
    function GetLevel($item)
    {
        if($item && $this->is_hierarchy)
        {
            //echo "AAA&nbsp;&nbsp;&nbsp;";
            $parent_id = $item->GetHeader($this->key_parent);

            //var_dump($this->table_parent . "_" . $this->index_suffix_parent);
            //var_dump($parent);
            if($parent_id)
            {
                return 1+$this->GetLevel(new CEntity(array("table"=>$item->table, "id"=>$parent_id, "index_suffix"=>$this->index_suffix_parent)));
            }
        }
        return 0;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает массив элементов списка по заданному паренту
    function GetList($parent_id=null)
    {
        $ret = array();

        if(!$this->query)
        {
            $query = "
SELECT *
FROM ".$this->table . "
WHERE
  NULL IS NULL
";

            if($this->where)
            {
                $query .= '
  AND '.$this->where;
            }

            if($this->is_parent)
            {
                // есть родитель
                $query .="
  AND ".$this->key_parent."=".$this->parent->identity;

            }
            else if($this->is_hierarchy)
            {
                // есть иерархия
                if($parent_id)
                {
                    // указан родитель - ищем потомков
                    $query .="
  AND ".$this->key_parent."='".$parent_id."'";
                }
                else
                {
                    // ищем нулевой уровень
                    $query .="
  AND (".$this->key_parent."='0' OR ".$this->key_parent." IS NULL)";
                }
            }

            if($this->order_key && $this->order_key_sort)
            {
                $query .="
ORDER BY ";

                $query .= $this->table.".".$this->order_key . " " .$this->order_key_sort ."
";
            }

            //var_dump($this->parent);
            //var_dump($query);//exit;

            //var_dump($this->pagecount);
            // проверим нужна ли навигация
            if($this->pagecount)
            {
                // нужна
                $this->navigation = new NavigationClass( $query, $this->pagecount );
//                $query .= $this->navigation->query_navigation();
                $this->navigation_pager = new CNavigationPager($query, $this->pagecount, $this->table);
                $query .= $this->navigation_pager->GetQuery();


            }
            // проверим нужны ли лимиты
            elseif($this->limit)
            {
                $query .= $this->limit;
            }
        }
        else
        {
            $query = $this->query;

            //error_log($query);

            // проверим нужна ли навигация
            if(!preg_match('/\s+LIMIT\s+/ims', $query))
            {
                if($this->pagecount)
                {
                    // нужна
                    $this->navigation = new NavigationClass( $query, $this->pagecount );
                    //                $query .= $this->navigation->query_navigation();
                    $this->navigation_pager = new CNavigationPager($query, $this->pagecount, $this->table);
                    $query .= $this->navigation_pager->GetQuery();


                }
                // проверим нужны ли лимиты
                elseif($this->limit)
                {
                    $query .= $this->limit;
                }

            }
        }

        $db = new CDatabase();
        $db->Query($query);
        //var_dump($db->result);
        while($row = $db->NextAssoc())
        {
            //var_dump($row[$this->table."_".$index_suffix]);
            //echo "<br /><br />";
            $item = new CEntity(
                array(
                    "table"=>$this->table,
                    "id"=>$row[$this->table."_".$this->index_suffix],
                    "index_suffix"=>$this->index_suffix,
                    "date_format"=>$this->date_format,
                    "template"=>$this->template,
                )
            );
            array_push($ret, $item);
            if($this->is_hierarchy)
            {
                // если иерархия, то ищем потомков
                $list = array();
                $list=$this->GetList($item->identity);
                $ret = array_merge($ret, $list);
            }
        }

        //var_dump($ret);
        //exit;
        return $ret;
    }


    /**
     * Функция возвращает следующий элемент списка, относительно элемента $item
     * @param CEntity $item
     */
    function GetNext($item)
    {
        $ret = false;
        $pagecount = 0;
        if($this->pagecount)
        {
            $pagecount = $this->pagecount;
        }
        $list_items = $this->GetList();

        for($i=0; $i<count($list_items); $i++)
        {
            if($list_items[$i]->identity == $item->identity)
            {
                if($i+1<count($list_items))
                {
                    $ret = $list_items[$i+1];
                }
                break;
            }
        }
        unset($list_items);

        $this->pagecount = $pagecount;

        return $ret;
    }

    /**
     * Функция возвращает предыдущий элемент списка, относительно элемента $item
     * @param CEntity $item
     */
    function GetPrev($item)
    {
        $ret = false;
        $pagecount = 0;
        if($this->pagecount)
        {
            $pagecount = $this->pagecount;
        }
        $list_items = $this->GetList();

        for($i=0; $i<count($list_items); $i++)
        {
            if($list_items[$i]->identity == $item->identity)
            {
                if($i-1>=0)
                {
                    $ret = $list_items[$i-1];
                }
                break;
            }
        }
        unset($list_items);

        $this->pagecount = $pagecount;

        return $ret;
    }


}

//-----------------------------------------------------------------------------
// Внутренний класс
class CEntityEx
{
    var $id;     // индекс записи в БД
    var $identity;      // ID записи в БД
    var $tablename;
    var $table;         // имя таблицы где хранятся записи поля текстов, заголовков, идентификаторы картинок
    var $index_suffix;  // суфикс для индексного поля
    //var $entity;        // экземпляр сущности
    //var $order;					// значение для упорядочивания
    var $where; 				// условия для отбора сущности
    var $template;
    var $date_format;
    var $function;
    var $auth_function;

    var $order = NULL; 	// массив полей упорядочивания
    var $order_value = NULL; // значение первого поля упорядочивания


    // типизировнные поля
    var $headers=array();
    var $texts=array();
    var $dates=array();
    var $images=array();
    var $files=array();
    var $sounds=array();
    var $videos=array();



    //---------------------------------------------------------------------------
    // Конструктор
    function CEntityEx(
        $params = array(
            "table"=>null,  // таблица
            "id"=>null,     // идентификатор
            "index_suffix"=>"id", // суффикс
            "keys"=>array(), // ключи для сущности
            "date_format" => "d.m.Y H:i:s", // формат отображения даты/времени
            "template" => "none",
            "where"=>"",
            "function"=>false, // функция кастомизации отображения func($this, &$template);
            "auth_function"=>"",
        ))
    {
        global $MAIN;

        if(!isset($params["table"]))
        {
            die($MAIN->ErrorShow(array("message"=>"Не указана таблица!")));
        }
        if(!isset($params["id"]))
        {
            $params["id"] = null;
        }
        if(!isset($params["index_suffix"]))
        {
            $params["index_suffix"] = "id";
        }
        if(!isset($params["keys"]))
        {
            $params["keys"] = array();
        }
        if(!isset($params["date_format"]))
        {
            $params["date_format"] = "d.m.Y H:i:s";
        }
        if(!isset($params["template"]))
        {
            $params["template"] = "none";
        }
        if(!isset($params["where"]))
        {
            $params["where"] = "";
        }
        if(!isset($params["function"]))
        {
            $params["function"] = false;
        }
        if(!isset($params["auth_function"]))
        {
            $params["auth_function"] = "";
        }
        if(!isset($params["order"]))
        {
            $params["order"] = $params["table"]."_order";
        }

        $this->id = $params["id"];
        $this->table = $params["table"];
        $this->index_suffix = $params["index_suffix"];
        $this->where = $params["where"];
        if(defined("DATABASE_PREFIX") && DATABASE_PREFIX)
            $this->tablename = DATABASE_PREFIX.$this->table;
        else
            $this->tablename = $this->table;

        $this->date_format = $params["date_format"];
        $this->template = $params["template"];
        $this->function = $params["function"];
        $this->order = $params["order"];
        $this->auth_function = $params["auth_function"];


        $db = new CDatabase();
        // инициализация значений записи из таблицы
        if( $this->id )
        {
            // проверим id записи в таблице
            $query = "
SELECT *
FROM " . $this->tablename . "
WHERE " . $this->table . "_" . $this->index_suffix ." = '" . $this->id . "'
";
            if($this->where)
            {
                $this->where;
                $query .= " AND " . $this->where;
            }
            //var_dump($query);
            $db->Query($query);


            //$result = mysql_query($sql)  or die(die_mysql_error_show($sql));
            if( $row = $db->NextAssoc() )
            {
                $this->identity = $row[$this->table . "_id"];

                // есть такая партия (запись)
                if(isset($row[$this->order]))
                {
                    //error_log(var_export($row, true));
                    // значение поля упорядочивания
                    $this->order_value = $row[$this->order];
                }

                //var_dump($MAIN->GetTableFieldsTypesArray($this->table, array("string", "select", "password", "number", "checkbox")));
                // типы "string", "password", "number", "checkbox"
                foreach( $MAIN->GetTableFieldsTypesArray($this->table, array("string", "select", "password", "number", "checkbox")) as $key )
                {
                    //var_dump($key);
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->headers[$key] = $row[$key];
                    }
                    elseif(isset($row[$key]))
                    {
                        $this->headers[$key] = $MAIN->GetCurrentStrictValueLang($row[$key]);
                    }
                    else
                    {
                        $this->headers[$key] = "";
                    }
                }

                // тексты
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "text") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->texts[$key] = $row[$key];
                    }
                    elseif(isset($row[$key]))
                    {
                        $this->texts[$key] = $MAIN->GetCurrentStrictValueLang($row[$key]);
                    }
                    else
                    {
                        $this->texts[$key] = "";
                    }

                }

                // даты
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "datetime") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if( preg_match("/^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})$/", $row[$key], $matches) )
                    {
                        $this->dates[$key] = $row[$key]; //mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1] );
                    }
                    else
                    {
                        $this->dates[$key] = date("Y-m-d H:i:s");
                    }
                }
                //var_dump($this->dates);

                // изображения
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "image") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->images[$key] = new ImageClass($row[$key]);
                    }
                    else
                    {
                        $this->images[$key] = new ImageClass($MAIN->GetCurrentStrictValueLang($row[$key]));
                    }
                }
                // файлы
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "file") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->files[$key] = new FileClass($row[$key]);
                    }
                    else
                    {
                        $this->files[$key] = new FileClass($MAIN->GetCurrentStrictValueLang($row[$key]));
                    }
                }

                // звук
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "sound") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->sounds[$key] = new SoundClass($row[$key]);
                    }
                    else
                    {
                        $this->sounds[$key] = new SoundClass($MAIN->GetCurrentStrictValueLang($row[$key]));
                    }
                }
                // видео
                foreach( $MAIN->GetTableFieldsTypeArray($this->table, "video") as $key )
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    {
                        continue;
                    }
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $this->videos[$key] = new VideoClass($row[$key]);
                    }
                    else
                    {
                        $this->videos[$key] = new VideoClass($MAIN->GetCurrentStrictValueLang($row[$key]));
                    }
                }
                $this->entity_id = $this->id;
                $db->Free();
            }


        }
        else
        {
            //echo "BBBBB";
            // типы "string", "password", "number", "checkbox"
            foreach( $MAIN->GetTableFieldsTypesArray($this->table, array("string", "select", "password", "number", "checkbox")) as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->headers[$key] = "";
            }
            // тексты
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "text") as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->texts[$key] = "";
            }
            // даты
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "datetime") as $key )
            {
                //echo "AAAAA";
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->dates[$key] = date("Y-m-d H:i:s");
            }
            // изображения
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "image") as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->images[$key] = new ImageClass();
            }

            // файлы
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "file") as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->files[$key] = new FileClass();
            }
            // звук
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "sound") as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->sounds[$key] = new SoundClass();
            }

            // видео
            foreach( $MAIN->GetTableFieldsTypeArray($this->table, "video") as $key )
            {
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                {
                    continue;
                }
                $this->videos[$key] = new VideoClass();
            }
        }
    }


    //---------------------------------------------------------------------------
    // Функция сохранения записи в БД
    function SaveEx($values = array())
    {
        global $MAIN;
        //error_log(print_r($values,true));
        $headers = array();
        $texts = array();
        $dates = array();
        $images = array();
        $sounds = array();
        $videos = array();
        $files = array();

        foreach($values as $key => $value)
        {
            $tableFieldParams = $MAIN->GetTableFieldParams($this->table, $key);
            //error_log("tableFieldParams:".$this->table.":".$key.":".print_r($tableFieldParams, true));
            switch($tableFieldParams["type"])
            {
                case "text":
                    $texts[$key] = $value;
                    continue;
                case "datetime":
                    $dates[$key] = $value;
                    continue;
                case "string":
                case "password":
                case "number":
                case "select":
                case "checkbox":
                    $headers[$key] = $value;
                    continue;
                case "image":
                    $images[$key] = $value; // entity_pictures_array($key,$images);
                    continue;
                case "sound":
                    $sounds[$key] = $value; // entity_sounds_array($key,$sounds);
                    continue;
                case "video":
                    $videos[$key] = $value; // entity_videos_array($key,$videos);
                    continue;
                case "file":
                    $files[$key] = $value; // entity_file_array($key,$files);
                    continue;
                default:
                    continue;
            }
            unset($tableFieldParams);
        }
        //error_log("headers: ".print_r($headers, true));
        // даты
        if( count($dates) )
        {
            foreach( $dates as $key => $value )
            {
                // если дата правильная то пишем в данные
                if( preg_match('/^(\d{4})\-(\d{2})\-(\d{2})\s*$/', $value, $matches) )
                {
                    if( checkdate($matches[2], $matches[3], $matches[1]))
                    {
                        $this->dates[$key] = $matches[1] . "-" . $matches[2] . "-" . $matches[3]
                            . " 00:00:00";
                    }
                }
                else if( preg_match('/^(\d{4})\-(\d{2})\-(\d{2})\s(\d{2})\:(\d{2})\:(\d{2})$/', $value, $matches) )
                {
                    if( checkdate($matches[2], $matches[3], $matches[1])
                        && (0 <= (integer)$matches[4] && (integer)$matches[4] <= 23)
                        && (0 <= (integer)$matches[5] &&  (integer)$matches[5] <= 59)
                        && (0 <= (integer)$matches[6] && (integer)$matches[6] <= 59) )
                    {
                        $this->dates[$key] = $matches[1] . "-" . $matches[2] . "-" . $matches[3]
                            . " "
                            . $matches[4] .":" . $matches[5] . ":".  $matches[6] ;
                    }
                }
            }
        }
        // заголовки
        if( count($headers) )
        {
            foreach( $headers as $key => $value )
            {
                $this->headers[$key] = NULL;
                if(!is_null($value))
                {
                    $this->headers[$key] = get_magic_quotes_gpc()?stripslashes($value):$value;
                }
            }
        }
        unset($headers);

        // тексты
        if( count($texts) )
        {
            foreach( $texts as $key => $value )
            {
                $this->texts[$key] = get_magic_quotes_gpc()?stripslashes($value):$value;
                //$this->texts[$key] = $value;
            }
        }
        unset($texts);

        // картинки
        if( count($images) )
        {
            foreach( $images as $key => $value )
            {
                if( is_array($value)
                    && isset($value["tmp_name"]) && $value["tmp_name"]
                    && isset($value["contenttype"]) && $value["contenttype"]
                    && isset($value["filename"]) && $value["filename"] )
                {
                    $this->images[$key]->insert_image($value["tmp_name"], $value["contenttype"], $value["filename"]);
                }
                elseif(is_int($value))
                {
                    $this->images[$key] = new ImageClass($value);
                }
            }
        }
        unset($images);

        // видео
        if( count($videos) )
        {
            foreach( $videos as $key => $value )
            {
                if( is_array($value)
                    && isset($value["tmp_name"]) && $value["tmp_name"]
                    && isset($value["contenttype"]) && $value["contenttype"]
                    && isset($value["filename"]) && $value["filename"] )
                {
                    $this->videos[$key]->insert_video($value["tmp_name"], $value["contenttype"], $value["filename"]);
                    //var_dump($this->videos[$key]);exit;
                }
                elseif(is_int($value))
                {
                    $this->videos[$key] = new VideoClass($value);
                }
            }
        }
        unset($videos);

        // звуки
        if( count($sounds) )
        {
            foreach( $sounds as $key => $value )
            {
                if( is_array($value)
                    && isset($value["tmp_name"]) && $value["tmp_name"]
                    && isset($value["contenttype"]) && $value["contenttype"]
                    && isset($value["filename"]) && $value["filename"] )
                {
                    $this->sounds[$key]->insert_sound($value["tmp_name"], $value["contenttype"], $value["filename"]);
                }
                elseif(is_int($value))
                {
                    $this->sounds[$key] = new SoundClass($value);
                }
            }
        }
        unset($sounds);

        // файлы
        if( count($files) )
        {
            foreach( $files as $key => $value )
            {
                if( is_array($value)
                    && isset($value["tmp_name"]) && $value["tmp_name"]
                    && isset($value["contenttype"]) && $value["contenttype"]
                    && isset($value["filename"]) && $value["filename"] )
                {
                    $this->files[$key]->insert_file($value["tmp_name"], $value["contenttype"], $value["filename"], $this->table, $this->identity, $key);
                }
                elseif(is_int($value))
                {
                    $this->files[$key] = new FileClass($value);
                }
            }
        }
        unset($files);

        // проверим существует ли текущая запись
        $row = null;
        $query = "
SELECT *
FROM " . $this->tablename . "
WHERE " . $this->table . "_" . $this->index_suffix . " = '" . $this->id . "'";

        //error_log($query);

        $db = new CDatabase();
        $db->Query($query);

        if($db->RowCount())
        {
            $need_insert = false;
            $row = $db->NextAssoc();
        }
        else
        {
            $need_insert = true;
        }

        $new_entity = NULL;
        // проверим что нужно сделать (insert || update)
        $sql = "";
        if( !$need_insert && $this->id )
        {
            // update
            $sql .= "
UPDATE " . $this->tablename . "
SET ";

            //error_log(print_r($this->headers, true));

            // заголовки
            foreach( $this->headers as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                if($row[$key])
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
                    {
                        $sql .= " " . $key . "='" . str_replace("'", "''",$value) . "',";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],$value);
                        $sql .= " " . $key . "='" . str_replace("'", "''",$row[$key]) . "',";
                    }
                }
                else
                {
                    if(!is_null($value))
                    {
                        $sql .= " " . $key . "='" . str_replace("'", "''",$value) . "',";
                    }
                    else
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                }

                //			  if($row[$key] && $MAIN->GetTableFieldParam($this->table, $key, "hidden") != "true" && $MAIN->GetTableFieldParam($this->table, $key, "nolang") != "true" )
                //        {
                //          $MAIN->SetCurrentValueLang($row[$key],$value);
                //          $sql .= " " . $key . "='" . str_replace("'", "''",$row[$key]) . "',";
                //        }
                //        else
                //        {
                //          if(!is_null($value))
                //          {
                //            $sql .= " " . $key . "='" . str_replace("'", "''",$value) . "',";
                //          }
                //          else
                //          {
                //            $sql .= " " . $key . "=NULL,";
                //          }
                //        }

                if($key == $this->table . "_" . $this->index_suffix)
                {
                    $new_entity = str_replace("'", "''",$value);
                }
            }
            // тексты
            foreach( $this->texts as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                if($row[$key])
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "='" . str_replace("'", "''",$value) . "',";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],$value);
                        $sql .= " " . $key . "='" . str_replace("'", "''",$row[$key]) . "',";
                    }

                }
                else
                {
                    if(!is_null($value))
                    {
                        $sql .= " " . $key . "='" . str_replace("'", "''",$value) . "',";
                    }
                    else
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                }

            }
            // даты
            foreach( $this->dates as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key . "='" .  $value . "',";
            }
            // картинки
            foreach( $this->images as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                if($value->image_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        //echo "AAAAAA1";
                        $sql .= " " . $key . "='" . $value->image_id . "',";
                    }
                    else
                    {
                        //echo "AAAAAA2";
                        $MAIN->SetCurrentValueLang($row[$key],$value->image_id);
                        $sql .= " " . $key . "='" . $row[$key] . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],'');
                        $sql .= " " . $key . "='".$row[$key]."',";
                    }
                }
            }

            // видео
            foreach( $this->videos as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;
                if($value->video_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "='" . $value->video_id . "',";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],$value->video_id);
                        $sql .= " " . $key . "='" . $row[$key] . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],'');
                        $sql .= " " . $key . "='".$row[$key]."',";
                    }
                }
            }
            // звуки
            foreach( $this->sounds as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                if($value->sound_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "='" . $value->sound_id . "',";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],$value->sound_id);
                        $sql .= " " . $key . "='" . $row[$key] . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],'');
                        $sql .= " " . $key . "='".$row[$key]."',";
                    }
                }
            }


            // файлы
            foreach( $this->files as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;
                if($value->file_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "='" . $value->file_id . "',";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],$value->file_id);
                        $sql .= " " . $key . "='" . $row[$key] . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true" )
                    {
                        $sql .= " " . $key . "=NULL,";
                    }
                    else
                    {
                        $MAIN->SetCurrentValueLang($row[$key],'');
                        $sql .= " " . $key . "='".$row[$key]."',";
                    }
                }
            }

            $sql = substr($sql, 0, -1) . "
WHERE
";
            $sql .=  $this->table . "_" . $this->index_suffix . " = '" . $this->entity_id . "'";

            //error_log($sql);
            $db->Query($sql);


            // проверим нужно ли упорядочить таблицу
            if($MAIN->GetTableParam($this->table, "autoreorder")=="true")
            {
                // проверим есть ли иерархия и отсортируем список 
                $tableParamHierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
                if(is_array($tableParamHierarchy))
                {
                    // подчиненный объект
                    if(isset($tableParamHierarchy["parent"])
                        && $tableParamHierarchy["parent"] == "true"
                        && isset($tableParamHierarchy["parent_table"])
                        && $tableParamHierarchy["parent_table"]
                        && isset($tableParamHierarchy["parent_field"])
                        && $tableParamHierarchy["parent_field"])
                    {
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                                "table_parent" => $tableParamHierarchy["parent_table"],
                                "key_parent" => $tableParamHierarchy["parent_field"],
                                "parent_id" => $this->headers[$tableParamHierarchy["parent_field"]],
                            )
                        );
                        $list->Reorder();
                    }
                    // иерархия внутри таблицы
                    elseif(isset($tableParamHierarchy["hierarchy"])
                        && $tableParamHierarchy["hierarchy"] == "true"
                        && isset($tableParamHierarchy["hierarchy_parent_field"])
                        && $tableParamHierarchy["hierarchy_parent_field"]
                    )
                    {
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                                "table_parent" => $this->table,
                                "key_parent" => $tableParamHierarchy["hierarchy_parent_field"],
                                "parent_id" => $this->headers[$tableParamHierarchy["hierarchy_parent_field"]],
                            )
                        );
                        $list->Reorder();
                    }
                }
                else
                {
                    //echo $sql;
                    //echo "AAAAAAA";
                    //var_dump($this->identity );
                    //debug_print_backtrace();
                    if($db->FieldExists($this->table, $this->table."_order")) // если есть поле для упорядочивания
                    {
                        // нет иерархии, просто упорядочим
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                            )
                        );
                        $list->Reorder();
                    }
                }
            }
        }
        else // insert
        {

            $sql .= "
INSERT INTO " . $this->tablename . " ( ";
            $sql_values = "
VALUES( ";

            // заголовки
            foreach( $this->headers as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";

                if(!is_null($value))
                {
                    $sql_values .= " '" . $value . "',";
                }
                else
                {
                    $sql_values .= " NULL,";
                }

                if($key == $this->table . "_" . $this->index_suffix)
                {
                    $new_entity = $value;
                }
            }
            // тексты
            foreach( $this->texts as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";
                $sql_values .= " '" . $value . "',";
            }
            // даты
            foreach( $this->dates as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;
                $sql .= " " . $key .",";
                $sql_values .= " '" . $value . "',";
            }
            // картинки
            foreach( $this->images as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";
                $newvalue = '';
                if($value->image_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->image_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " '" . $value->image_id . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->image_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " NULL,";
                    }
                }
            }
            // видео
            foreach( $this->videos as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";
                $newvalue = '';
                if($value->video_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->video_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " '" . $value->video_id . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->video_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " NULL,";
                    }
                }
            }
            // звуки
            foreach( $this->sounds as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";
                $newvalue = '';
                if($value->sound_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->sound_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " '" . $value->sound_id . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->sound_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " NULL,";
                    }
                }
            }

            // файлы
            foreach( $this->files as $key => $value )
            {
                if($key == $this->order)
                    continue;
                if($MAIN->GetTableFieldParamsParam($this->table, $key, "hidden") == "true")
                    continue;

                $sql .= " " . $key .",";
                $newvalue = '';
                if($value->file_id)
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->file_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " '" . $value->file_id . "',";
                    }
                }
                else
                {
                    if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") != "true" )
                    {
                        $MAIN->SetCurrentValueLang($newvalue,$value->file_id);
                        $sql_values .= " '$newvalue',";
                    }
                    else
                    {
                        $sql_values .= " NULL,";
                    }
                }

            }

            $sql = substr($sql, 0, -1) . ") " . substr($sql_values, 0, -1) . ")";

            //var_dump($sql);exit;
            $db->Query($sql);
            $this->identity = $db->InsertId();

            // проверим нужно ли упорядочить таблицу
            if($MAIN->GetTableParam($this->table, "autoreorder")=="true")
            {
                // проверим есть ли иерархия и отсортируем список 
                $tableParamHierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
                if(is_array($tableParamHierarchy))
                {
                    // подчиненный объект
                    if(isset($tableParamHierarchy["parent"])
                        && $tableParamHierarchy["parent"] == "true"
                        && isset($tableParamHierarchy["parent_table"])
                        && $tableParamHierarchy["parent_table"]
                        && isset($tableParamHierarchy["parent_field"])
                        && $tableParamHierarchy["parent_field"])
                    {
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                                "table_parent" => $tableParamHierarchy["parent_table"],
                                "key_parent" => $tableParamHierarchy["parent_field"],
                                "parent_id" => $this->headers[$tableParamHierarchy["parent_field"]],
                            )
                        );
                        $list->Reorder();
                    }
                    // иерархия внутри таблицы
                    elseif(isset($tableParamHierarchy["hierarchy"])
                        && $tableParamHierarchy["hierarchy"] == "true"
                        && isset($tableParamHierarchy["hierarchy_parent_field"])
                        && $tableParamHierarchy["hierarchy_parent_field"]
                    )
                    {
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                                "table_parent" => $this->table,
                                "key_parent" => $tableParamHierarchy["hierarchy_parent_field"],
                                "parent_id" => $this->headers[$tableParamHierarchy["hierarchy_parent_field"]],
                            )
                        );
                        $list->Reorder();
                    }
                }
                else
                {
                    //echo $sql;
                    //echo "AAAAAAA";
                    //var_dump($this->identity );
                    //debug_print_backtrace();
                    if($db->FieldExists($this->table, $this->table."_order")) // если есть поле для упорядочивания
                    {
                        // нет иерархии, просто упорядочим
                        $list = new CEntityList(
                            array(
                                "table" => $this->table,
                            )
                        );
                        $list->Reorder();
                    }
                }
            }

        }

        // проверим изменился ли текущий id и вернем его через entity_id
        if($new_entity != NULL)
        {
            $this->entity_id = $new_entity;
        }
    }



    //---------------------------------------------------------------------------
    function __toString()
    {
        if($this->identity)
            return "".$this->identity;

        return "";
    }

    //---------------------------------------------------------------------------
    // Функция удаления записи из БД
    function DeleteEx()
    {
        global $MAIN;

        if( !$this->identity )
            return false;

        // удаление потомков в иерархии
        // TODO: сделать удаление потомков в иерархии
        $params = $MAIN->GetParams();
        foreach($params as $tableName => $tableParam)
        {
            // поиск потомков
            if(isset($tableParam["hierarchy"])
                && is_array($tableParam["hierarchy"]))
            {
                // подчиненный объект
                if(isset($tableParam["hierarchy"]["parent"])
                    && $tableParam["hierarchy"]["parent"] == "true"
                    && isset($tableParam["hierarchy"]["parent_table"])
                    && $tableParam["hierarchy"]["parent_table"] == $this->table
                    && isset($tableParam["hierarchy"]["parent_field"])
                    && $tableParam["hierarchy"]["parent_field"])
                {
                    $list = new CEntityList(
                        array(
                            "table" => $tableName,
                            "table_parent" => $this->table,
                            "key_parent" => $tableParam["hierarchy"]["parent_field"],
                            "parent_id" => $this->identity,
                        )
                    );

                    foreach($list->list as $item)
                    {
                        //var_dump($item);
                        $item->Delete();
                    }
                }

                // иерархия внутри таблицы
                if($tableName == $this->table
                    && isset($tableParam["hierarchy"]["hierarchy"])
                    && $tableParam["hierarchy"]["hierarchy"] == "true"
                    && isset($tableParam["hierarchy"]["hierarchy_parent_field"])
                    && $tableParam["hierarchy"]["hierarchy_parent_field"])
                {
                    $list = new CEntityList(
                        array(
                            "table" => $this->table,
                            "table_parent" => $this->table,
                            "key_parent" => $tableParam["hierarchy"]["hierarchy_parent_field"],
                            "parent_id" => $this->identity,
                        )
                    );
                    foreach($list->list as $item)
                    {
                        //var_dump($item);
                        $item->Delete();
                    }
                }
            }
        }
        //exit;


        // удаление картинок
        foreach( $this->images as $key => $value ){
            $value->delete_image();
        }
        // удаление звуков
        foreach( $this->sounds as $key => $value ){
            $value->delete_sound();
        }
        // удаление видео
        foreach( $this->videos as $key => $value ){
            $value->delete_video();
        }
        // удаление файлов
        foreach( $this->files as $key => $value ){
            $value->delete_file();
        }

        // TODO: сделать удаление папки для картинок текущего объекта
        $image_dir = $MAIN->root.IMAGE_DIR."/".$this->table."/".$this->identity;
        if(file_exists($image_dir) && is_dir($image_dir))
        {
            rrmdir($image_dir);
        }

        // удаление записи
        $sql = "
DELETE FROM " . $this->tablename . "
WHERE " . $this->table . "_" . $this->index_suffix . " = '" . $this->id . "'
";
        $result = mysql_query($sql)  or die(die_mysql_error_show($sql));
        if(mysql_affected_rows() > 0)
        {
            return true;
        }
        return false;
    }

    //---------------------------------------------------------------------------
    // Функция отображения объекта
    function ViewEx($params = array(
        "output" => "true",
        "template" => "none",
        "template_name" => "default",
        "template_type" => "default",
        "function" => false,
    ))
    {
        global $MAIN;

        if(!isset($params["function"]))
            $params["function"] = $this->function;
        if(!isset($params["output"]))
            $params["output"] = "true";
        if(!isset($params["template"]) || $params["template"] == "none")
        {
            if($this->template)
            {
                $params["template"] = $this->template;
            }
            else
            {
                $params["template"] = "none";
            }
        }

        if(!isset($params["template_name"]))
            $params["template_name"] = "default";
        if(!isset($params["template_type"]))
            $params["template_type"] = "default";

        if($params["function"] && !$this->function)
        {
            $this->function = $params["function"];
        }

        $this->template = $params["template"];
        //var_dump($this->template);

        $templatePath = $MAIN->GetTemplatePath($params["template_name"], "view", $params["template_type"], $MAIN->is_admin);
        if(file_exists($templatePath))
        {
            include($templatePath);
        }


        // вывод в эту переменную
        $this->view = $this->template;

        // идентификаторы
        $this->view = preg_replace( ("/\\[entity_id\\]/"),
            ($this->id ),
            $this->view
        );
        $this->view = preg_replace( ("/\\[identity\\]/"),
            ($this->identity ),
            $this->view
        );

        // языковые варианты
        // конструкция вида [lang:К списку новостей|To the list of news]
        if(preg_match_all("/\\[lang:([^>]*)\\]/", $this->view, $matches))
        {
            for($i=0; $i< count($matches[0]); $i++)
            {
                //$this->view = preg_replace("/". preg_quote($matches[0][$i]) ."/", get_value_lang(preg_replace("/\|/",LANGUAGE_SPLITTER,$matches[1][$i]), $_SESSION["lang"]), $this->view);
                $this->view = preg_replace("/". preg_quote($matches[0][$i]) ."/", $MAIN->GetCurrentValueLang(preg_replace("/\|/",LANGUAGE_SPLITTER,$matches[1][$i])), $this->view);
            }
        }

        // разместим файлы
        foreach( array_keys($this->files) as $key ){
            if( $this->files[$key]->file_id ){
                $this->view = preg_replace( ("/\\[file:" . $key . "\\]/"),
                    ($this->files[$key]->file_id ),
                    $this->view );
            }
            else{
                $this->view = preg_replace( ("/\\[file:" . $key . "\\]/"),
                    ($this->files[$key]->file_id ),
                    $this->view );
            }
        }

        // разместим звук
        foreach( array_keys($this->sounds) as $key ){
            if( $this->sounds[$key]->sound_id ){
                $this->view = preg_replace( ("/\\[sound:" . $key . "\\]/"),
                    ($this->sounds[$key]->sound_id ),
                    $this->view );
            }
            else{
                $this->view = preg_replace( ("/\\[sound:" . $key . "\\]/"),
                    ($this->sounds[$key]->sound_id ),
                    $this->view );
            }
        }

        // разместим видео
        foreach( array_keys($this->videos) as $key ){
            if( $this->videos[$key]->video_id ){
                $this->view = preg_replace( ("/\\[video:" . $key . "\\]/"),
                    ($this->videos[$key]->video_id ),
                    $this->view );
            }
            else{
                $this->view = preg_replace( ("/\\[video:" . $key . "\\]/"),
                    ($this->videos[$key]->video_id ),
                    $this->view );
            }
        }
        // разместим картинки
        foreach( array_keys($this->images) as $key ){
            if( $this->images[$key]->image_id ){
                $this->view = preg_replace( ("/\\[image:" . $key . "\\]/"),
                    ($this->images[$key]->image_id ),
                    $this->view );
            }
            else{
                $this->view = preg_replace( ("/\\[image:" . $key . "\\]/"),
                    ($this->images[$key]->image_id ),
                    $this->view );
            }
        }
        // разместим картинки image_file
        foreach( array_keys($this->images) as $key ){
            if( $this->images[$key]->image_id ){
                $this->view = preg_replace( ("/\\[image_file:" . $key . "\\]/"),
                    ($this->images[$key]->image_filename() ),
                    $this->view );
            }
            else{
                $this->view = preg_replace( ("/\\[image_file:" . $key . "\\]/"),
                    ($this->images[$key]->image_filename() ),
                    $this->view );
            }
        }
        // разместим даты
        foreach( array_keys($this->dates) as $key ){
            $this->view = preg_replace( ("/\\[date:" . $key . "\\]/"),
                date($this->date_format, $this->GetDatetimeFromString($this->dates[$key])),
                $this->view );
        }
        // разместим заголовки
        foreach( array_keys($this->headers) as $key ){
            if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
            {
                $this->view = preg_replace( ("/\\[header:" . $key . "\\]/"),
                    $this->headers[$key],
                    $this->view );
            }
            else
            {
                $this->view = preg_replace( ("/\\[header:" . $key . "\\]/"),
                    $MAIN->GetCurrentValueLang($this->headers[$key]),
                    $this->view );

            }
        }
        // разместим тексты
        foreach( array_keys($this->texts) as $key )
        {
            /*
      $this->view = preg_replace( ("/\\[text:" . $key . "\\]/"),
                    nl2br($this->texts[$key]),
                    $this->view );
      */
            if($MAIN->GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
            {
                $this->view = preg_replace( ("/\\[text:" . $key . "\\]/"),
                    $this->texts[$key],
                    $this->view );
                $this->view = preg_replace( ("/\\[text:nl2br:" . $key . "\\]/"),
                    nl2br($this->texts[$key]),
                    $this->view );
            }
            else
            {
                $this->view = preg_replace( ("/\\[text:" . $key . "\\]/"),
                    $MAIN->GetCurrentValueLang($this->texts[$key]),
                    $this->view );
                $this->view = preg_replace( ("/\\[text:nl2br:" . $key . "\\]/"),
                    nl2br($MAIN->GetCurrentValueLang($this->texts[$key])),
                    $this->view );

            }
        }

        // разместим checkbox-ы
        foreach( array_keys($this->headers) as $key ){
            $this->view = preg_replace( ("/\\[checkbox:" . $key . "\\]/"),
                $this->headers[$key]?(isset($_template_yes)?$_template_yes:"Да"):(isset($_template_no)?$_template_no:"Нет"),
                $this->view );
        }
        //var_dump($_template_yes);
        /*
    // разместим списки
    foreach( array_keys($this->lists) as $key ){
      $view = preg_replace( ("/\\[list:" . $key . "\\]/"),
                    $this->lists[$key]->view_list(),
                    $view );
    }
    */

        // разместим значения listitem-ов
        foreach( array_keys($this->headers) as $key ){
            $this->view = preg_replace_callback("/\\[listitem:([^:]+):([^:]+):(" . $key . ")\\]/",
                array($this, 'ListItemReplaceEx'),
                $this->view );
        }


        if($this->function && is_callable($this->function))
        {
            $function = $this->function;
            $function($this, $this->view);
        }

        // выведем результат
        if($params["output"] == "true")
        {
            echo $this->view;
        }
        return $this->view;
    }

    //---------------------------------------------------------------------------
    function ViewEditEx($params = array(
        "template_name" => "default",
        "template_type" => "default",
        "template"=>"none",
        "function"=>false,
        "custom_captions"=>array(),
    ))
    {
        global $MAIN;
        //var_dump($this);
        //var_dump($lists_table);

        if(!isset($params["template_name"]))
            $params["template_name"] = "default";
        if(!isset($params["template_type"]))
            $params["template_type"] = "default";
        if(!isset($params["template"]))
            $params["template"] = "none";
        if(!isset($params["function"]))
            $params["function"] = false;
        if(!isset($params["custom_captions"]))
            $params["custom_captions"] = array();

        if($params["function"] && !$this->function)
        {
            $this->function = $params["function"];
        }

        $templatePath = $MAIN->GetTemplatePath($params["template_name"], "viewedit", $params["template_type"], $MAIN->is_admin);
        if(file_exists($templatePath))
        {
            global $_template_form_tr_footer,
                   $_template_form_tr_header,
                   $_template_playeraudio,
                   $_template_form_tr_td_right,
                   $_template_playervideo,
                   $_template_form_tr_td_left_rowspan2,
                   $_template_form_header,
                   $_template_form_tr_header,
                   $_template_form_tr_td_right,
                   $_template_form_tr_td_left,
                   $_template_form_update,
                   $_template_form_footer,
                   $_template_form_delete;
            include($templatePath);
        }


        $return = '';

        global $_template_form_scripts;
        $return .= $_template_form_scripts;
        if(defined("VN_EDITOR3"))
        {
            $return .= '
<script  language="JavaScript">
opts.fmOpen = '.preg_replace("/".preg_quote("[params]")."/ims", 'table='.$this->table.'&identity='.$this->identity, VN_EDITOR3_FILES_FUNCTION).'
</script>			
			';
        }


        $table_fields_params = $MAIN->GetTableFieldsParams($this->table);

        //var_dump($table_fields_params);
        //var_dump($this->headers);

        if(!count($table_fields_params))
            return $return;

        if( count($table_fields_params) )
        {
            $_template_form_header = str_replace("[form_name]", "form_".$this->table, $_template_form_header);
            $_template_form_header = str_replace("[action]", $MAIN->QueryStringWithoutParams(), $_template_form_header);
            $return .= str_replace("[id]", $this->id, $_template_form_header);
        }


        foreach( $table_fields_params as $element_name => $element_description )
        {
            // если скрытый элемент то его показывать не следует
            if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "hidden") == "true")
                continue;



            if($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "string")
            {
                //var_dump($element_name);var_dump($this->headers[$element_name]);var_dump(is_null($this->headers[$element_name]));
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);

                if($MAIN->GetTableFieldParamsParam($this->table, $element_name, "template")=="true")
                {
                    $return .= str_replace("[content]", "[header:{$element_name}]", $_template_form_tr_td_right);
                }
                else
                {
                    $return .= str_replace("[content]", '<input name="' . $element_name . '" type="' . "text" . '" size="50" value="' . htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])) . '"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>', $_template_form_tr_td_right);
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])).'" />';
                }
                $return .= $_template_form_tr_footer;
            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "password")
            {
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                $return .= str_replace("[content]", '<input name="' . $element_name . '" type="' . "password" . '" size="50" value="' . htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])) . '"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>', $_template_form_tr_td_right);
                if($MAIN->GetTableFieldParamsParam($this->table, $element_name, "template")=="true")
                {
                    $return .= str_replace("[content]", "[header:{$element_name}]", $_template_form_tr_td_right);
                }
                else
                {
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])).'" />';
                }
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "number")
            {
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                $return .= str_replace("[content]", '<input name="' . $element_name . '" type="' . "text" . '" size="50" value="' . htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])) . '"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>', $_template_form_tr_td_right);
                if($MAIN->GetTableFieldParamsParam($this->table, $element_name, "template")=="true")
                {
                    $return .= str_replace("[content]", "[header:{$element_name}]", $_template_form_tr_td_right);
                }
                else
                {
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->headers[$element_name]:$MAIN->GetCurrentValueLang($this->headers[$element_name])).'" />';
                }
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "text")
            {
                $return .= $_template_form_tr_header
                    . str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);

                if($MAIN->GetTableFieldParamsParam($this->table, $element_name, "template")=="true")
                {
                    $return .= str_replace("[content]", "[text:{$element_name}]", $_template_form_tr_td_right);
                }
                else
                {
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "editor") == "true")
                    {
                        if(defined("VN_EDITOR3"))
                        {
                            $textarea = '<textarea class="el-rte" id="' . $element_name . '"'. ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>';
                            $textarea .= (($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->texts[$element_name]:$MAIN->GetCurrentValueLang($this->texts[$element_name]));

                            if(!trim($this->texts[$element_name]))
                            {
                                $textarea .= "<p> </p>";
                            }
                            $textarea .= ' </textarea>';

                            $return .= str_replace("[content]", $textarea, $_template_form_tr_td_right);


                            //$return .= str_replace("[content]", '<textarea class="el-rte" id="' . $element_name . '"'. ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>' . (($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->texts[$element_name]:$MAIN->GetCurrentValueLang($this->texts[$element_name])) . ' </textarea>', $_template_form_tr_td_right);

                            //$return .= str_replace("[content]", '<textarea id="' . $element_name . '"'. ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>' . (($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->texts[$element_name]:$MAIN->GetCurrentValueLang($this->texts[$element_name])) . '</textarea>', $_template_form_tr_td_right);
                            if(defined("VN_EDITOR3_SCRIPT"))
                            {
                                $return .= "
<script type=\"text/javascript\">
".preg_replace('/'.preg_quote("[id]").'/ims', $element_name,  VN_EDITOR3_SCRIPT)."
</script>";
                            }
                        }
                    }
                    else
                    {
                        $return .= str_replace("[content]", '<textarea name="' . $element_name . '" id="' . $element_name . '" cols="46" rows="10"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>' . htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->texts[$element_name]:$MAIN->GetCurrentValueLang($this->texts[$element_name])) . '</textarea>', $_template_form_tr_td_right);

                    }
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars(($MAIN->GetTableFieldParamsParam($this->table, $element_name, "nolang") == "true")?$this->texts[$element_name]:$MAIN->GetCurrentValueLang($this->texts[$element_name])).'" />';
                }

                $return .= $_template_form_tr_footer;
                continue;
            }

            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "datetime")
            {
                $str = '<input name="' . $element_name . '" type="text" maxlength="19" size="20" value="' . $this->dates[$element_name] . '"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '><br />
';
                //				if(in_array( $element_name, $default_dates))
                //        {
                //          $str .= '<input type="checkbox" name="' . $element_name . '_default" value="1"' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>Текущая дата/время<br />';
                //        }
                $str .= '
    <font size="-2">datetime format is "YYYY-MM-DD hh:mm:ss"</font>';

                $return .= $_template_form_tr_header
                    . str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                    $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars($this->dates[$element_name]).'" />';

                $return .= $_template_form_tr_footer;
            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "checkbox")
            {
                $return .= $_template_form_tr_header
                    . str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                $return .= str_replace("[content]", '<input type="checkbox" name="' . $element_name . '" value="1"' . ($this->headers[$element_name]=="1"?" checked":"") . '' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>', $_template_form_tr_td_right);
                if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                    $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars($this->headers[$element_name]).'" />';
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "select")
            {

                if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "select", "values"))
                {
                    // список из глобального массива 
                    //					echo "AAAAAAA";
                    //					echo $MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "select", "values");

                    $list = false;
                    $this->GetSelectValuesListArray($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "select", "values"), $list);

                    $return .= $_template_form_tr_header;
                    $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                    $return .= str_replace("[content]", $this->GetListEx($this->table, $element_name, $list->items, $this->headers[$element_name]), $_template_form_tr_td_right);
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars($this->headers[$element_name]).'" />';
                    $return .= $_template_form_tr_footer;

                    //$return .= $this->GetListEx($this->table, $element_name, $list->items); 
                }
                elseif($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "select", "table", "name"))
                {
                    // список из таблицы
                    //echo "BBBBBB";
                    //echo $MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "select", "table", "name");
                    $list = false;
                    $this->GetSelectTableListArrayRecursion($this->table, $element_name, $list);

                    //echo "<pre>".print_r($list,true)."</pre>";

                    //var_dump($list);
                    $return .= $_template_form_tr_header;
                    $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left);
                    $return .= str_replace("[content]", $this->GetListEx($this->table, $element_name, $list->items, $this->headers[$element_name]), $_template_form_tr_td_right);
                    if($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true")
                        $return .= ' <input type="hidden" name="'.$element_name.'" id="'.$element_name.'" value="'.htmlspecialchars($this->headers[$element_name]).'" />';
                    $return .= $_template_form_tr_footer;

                }
            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "image")
            {
                $return .= $_template_form_tr_header
                    . str_replace("[content]", $this->GetCaption($element_name, $params["custom_captions"]), $_template_form_tr_td_left_rowspan2);



                $str = "";

                if($this->images[$element_name]->image_id)
                {
                    if($this->images[$element_name]->image_type == image_type_to_mime_type(IMAGETYPE_SWF))
                    {
                        // flash
                        $str .= '<object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0">
          <param name="movie" value="' . VIEW_IMAGE . '?id=' . $this->images[$element_name]->image_id . '">
          <param name="quality" value="low">
          <embed src="' . VIEW_IMAGE . '?id=' . $this->images[$element_name]->image_id . '" quality="low" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed></object>';
                    }
                    else
                    {
                        // simple image
                        $str .= '<img src="' . VIEW_IMAGE . '?id=' . $this->images[$element_name]->image_id . '" alt="" border="0">';
                    }
                }
                else
                {
                    $str .= 'none';
                }
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);

                $return .= $_template_form_tr_footer;



                $str = '';
                $str .= '<input name="' . $element_name . '" type="file" value="">';
                if($this->images[$element_name]->image_id)
                {
                    $str .= '<br /><a href="image_delete.php?id=' . $this->images[$element_name]->image_id  . '&table=' . $this->table . '&field=' . $element_name . '&ret='  . base64_encode( $_SERVER["REQUEST_URI"] ) . '">Delete</a>';
                }


                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "sound")
            {
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]",$this->GetCaption($element_name, $params["custom_captions"]),$_template_form_tr_td_left_rowspan2);


                $str = "";

                if($this->sounds[$element_name]->sound_id)
                {
                    $str .= str_replace("[sound_id]", $this->sounds[$element_name]->sound_id, $_template_playeraudio);
                    $return .= '
';
                }
                else
                {
                    $str .= 'none';
                }
                $str .= "";

                //$return .= $_template_form_tr_header;
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                $return .= $_template_form_tr_footer;


                $str = "";
                $str .= '<input name="' . $element_name . '" type="file" value="">';
                if($this->sounds[$element_name]->sound_id)
                {
                    $str .= '<br /><a href="sound_delete.php?id=' . $this->sounds[$element_name]->sound_id . '&table=' . $this->table . '&field=' . $element_name . '&ret='  . base64_encode( $_SERVER["REQUEST_URI"] ) . '">Delete</a>';
                }
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "video")
            {
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]", $this->GetCaption($element_name, $params["custom_captions"]), $_template_form_tr_td_left_rowspan2);

                $str = '';
                if($this->videos[$element_name]->video_id)
                {
                    // video
                    $str_tmp = str_replace("[video_id]", $this->videos[$element_name]->video_id, $_template_playervideo);
                    $str_tmp = str_replace("[table]", $this->table, $str_tmp);
                    $str_tmp = str_replace("[id]", $this->identity, $str_tmp);
                    $str_tmp = str_replace("[video]", $element_name, $str_tmp);
                    $str_tmp = str_replace("[preview]", $MAIN->GetTableFieldParamsParam($this->table, $element_name, "preview_field"), $str_tmp);
                    $str_tmp = str_replace("[width]", $MAIN->GetTableFieldParamsParam($this->table, $element_name, "width"), $str_tmp);
                    $str_tmp = str_replace("[height]", $MAIN->GetTableFieldParamsParam($this->table, $element_name, "height"), $str_tmp);

                    $str .= $str_tmp;
                    unset($str_tmp);
                }
                else
                {
                    $str .= 'none';
                }

                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                $return .= $_template_form_tr_footer;



                $str = "";
                $str .= '<input name="' . $element_name . '" type="file" value="">';
                if($this->videos[$element_name]->video_id)
                {
                    $str .= '<br /><a href="video_delete.php?id=' . $this->videos[$element_name]->video_id . '&table=' . $this->table . '&field=' . $element_name . '&ret='  . base64_encode( $_SERVER["REQUEST_URI"] ) . '">Delete</a>';
                }
                $return .= $_template_form_tr_header;
                $return .= str_replace("[content]", $str, $_template_form_tr_td_right);
                $return .= $_template_form_tr_footer;

            }
            elseif($MAIN->GetTableFieldParam($this->table, $element_name, "type") == "file")
            {
                //print_r($element_name);
                $return .= $_template_form_tr_header
                    . str_replace("[content]", $this->GetCaption($element_name, $params["custom_captions"]), $_template_form_tr_td_left_rowspan2)
                    . str_replace("[content]", '<input type="File" name="' . $element_name . '" value=""' . ($MAIN->GetTableFieldParamEx($this->table, $element_name, "params", "readonly") == "true"?' disabled="disabled"':'') . '>', $_template_form_tr_td_right)
                    . $_template_form_tr_footer;
                //var_dump($this->files[$element_name]);
                if($this->files[$element_name]->file_id)
                {

                    $str = 'Размер файла: ' . $this->files[$element_name]->get_file_length();

                    if($this->files[$element_name])
                    {
                        $str .= "<br>Имя файла: " . $this->files[$element_name]->file_filename();
                    }
                    if($this->files[$element_name]->file_type)
                    {
                        $str .= "<br>Тип файла: " . $this->files[$element_name]->file_type;
                    }

                    $str .= '<br><a href="' . VN_ADMIN . '/file.php?id=' . $this->files[$element_name]->file_id . '" target="_blank">Открыть</a><br>
<a href="' . VN_ADMIN . '/file.php?action=file&id=' . $this->files[$element_name]->file_id . '" target="_blank">Скачать</a>
<br /><br />
<a href="' . VN_ADMIN . '/file.php?action=delete&id=' . $this->files[$element_name]->file_id . '">Удалить</a>';

                    $return .= $_template_form_tr_header
                        . str_replace("[content]", $str, $_template_form_tr_td_right)
                        . $_template_form_tr_footer;
                }
                else
                {
                    $return .= $_template_form_tr_header
                        . str_replace("[content]", "Нет файла", $_template_form_tr_td_right)
                        . $_template_form_tr_footer;
                }
                continue;

            }

        }

        if( count($table_fields_params) )
        {
            $return .= $_template_form_update;
        }
        $return .= $_template_form_footer . '
';


        if($MAIN->GetAdminPageFile($this->table, "delete"))
        {
            $_template_form_delete = str_replace("[action]", $MAIN->QueryStringWithoutParams(), $_template_form_delete);
            $_template_form_delete = str_replace("[id]", $this->identity, $_template_form_delete);
            $return .= str_replace("[action]", $MAIN->QueryStringWithoutParams(), $_template_form_delete) . '
';
        }

        if($this->function && is_callable($this->function))
        {
            $function = $this->function;
            $function($this, $return);
        }



        return $return;

    }

    //---------------------------------------------------------------------------
    // Функция возвращает список в виде <SELECT></SELECT>
    function GetListEx($table, $field, $arr, $selected_id=false, $parent=false)
    {
        global $MAIN;

        $ret = "";
        if(!$parent)
        {
            $ret .= '
<select id="' . $field . '" name="' . $field . '"';
            $ret .= '' . ($MAIN->GetTableFieldParamEx($table, $field, "params", "readonly") == "true"?' disabled="disabled"':'') . '>';

            $ret .= '
<option value="0">Не выбрано</option>';
        }

        if(is_array($arr) && count($arr))
        {
            foreach ($arr as $item)
            {
                $ret .= '
<option value="'.$item->id.'"';
                if($selected_id && $item->id==$selected_id)
                    $ret .= ' selected';
                elseif(!$selected_id && isset($GLOBALS[$field]) && $GLOBALS[$field] && $item->id == $GLOBALS[$field])
                    $ret .= ' selected';

                $ret .='>'.($item->GetLevel()?str_repeat(". ", $item->GetLevel()):"").$item->name.'</option>';

                if(is_array($item->childs) && count($item->childs))
                {
                    $ret .= $this->GetListEx($table, $field, $item->childs, $selected_id, $item->id);
                }
            }
        }

        if(!$parent)
        {
            $ret .= '
</select >';
            //			if($MAIN->GetTableFieldParamEx($table, $field, "params", "readonly") == "true")
            //				$ret .= ' <input type="hidden" name="'.$field.'" id="'.$field.'" value="'.$selected_id.'" />';
        }


        return $ret;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает список из переменной типа массив
    function GetSelectValuesListArray(
        $var = "",
        &$list
    )
    {
        global $MAIN;

        $list = new CList();
        if($var && isset($GLOBALS[$var]) && is_array($GLOBALS[$var]))
        {
            foreach($GLOBALS[$var] as $key => $value)
            {
                $item = new CListItem($key, $MAIN->GetCurrentArrayLang($value));
                $list->Add($item);
            }
        }
    }

    //---------------------------------------------------------------------------
    function GetSelectTableListArrayRecursion(
        $table,
        $field,
        &$list,
        $parent_id=false
    )
    {
        global $MAIN;

        $list_table = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "name");
        $list_field = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "field");
        $list_field_name = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "namefield");
        $list_field_order = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "orderfield");
        $field_parent = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "parentfield");
        $field_where = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "table", "where");
        $hierarchy = $MAIN->GetTableFieldParamEx($table, $field, "params", "select", "hierarchy");

        if(!$list)
        {
            $list = new CList();
        }

        $select = "SELECT {$list_table}_id, {$list_field}, {$list_field_name}";
        if($field_parent)
        {
            $select .= ", {$field_parent}";
        }
        else
        {
            $select .= ", '0'";
        }

        $from = "		
FROM ".DATABASE_PREFIX."{$list_table}";
        $where = "
WHERE NULL IS NULL";

        if($field_parent && $parent_id)
        {
            $where .= " AND {$field_parent}='{$parent_id}'";
        }
        elseif($field_parent && !$parent_id && $hierarchy == "true")
        {
            $where .= " AND ({$field_parent}='0' OR {$field_parent} IS NULL)";
        }
        if($field_where)
        {
            $where .= " AND  {$field_where}";
        }

        $order = "";
        if($list_field_order)
        {
            if(preg_match('/\b((ASC)|(DESC))\b/ims', $list_field_order))
            {
                $order .= "
ORDER BY {$list_field_order}";
            }
            else
            {
                $order .= "
ORDER BY {$list_field_order} ASC";
            }
        }

        $db = new CDatabase();
        $query = $select.$from.$where.$order;
        $query = $this->ViewEx(array("template"=>$query, "output"=>"false"));
        $db->Query($query);

        while($row = $db->NextRow())
        {
            $item = new CListItem($row[0], $MAIN->GetCurrentValueLang($row[2]));
            $list->Add($item, $row[3]);
            if($hierarchy=="true")
            {
                $this->GetSelectTableListArrayRecursion($table, $field, $list, $row[0]);
            }
        }
    }

    //---------------------------------------------------------------------------
    // Функция возвращает список в виде <SELECT></SELECT>
    function GetSelectTableListEx(
        $params = array(
            "table" => "",
            "field" => "",
            "current" => "",
            "where" => "",
            "parent_id" => "",
            "list_field_name" => "",
            "list_field_order" => "",
        )
    )
    {
        global $MAIN;

        $ret = "";
        if(!isset($params["table"]))
            $params["table"] = "";
        if(!isset($params["field"]))
            $params["field"] = "";
        if(!isset($params["current"]))
            $params["current"] = "";
        if(!isset($params["where"]))
            $params["where"] = "";
        if(!isset($params["parent_id"]))
            $params["parent_id"] = "";
        if(!isset($params["list_field_name"]))
        {
            $tableFields = array_keys($MAIN->GetTableFields($params["table"]));
            if(in_array($tableFields, $params["table"]."_name"))
                $params["list_field_name"] = $params["table"]."_name";
            else
                $params["list_field_name"] = $tableFields[0];
            unset($tableFields);
        }
        /*
    $query = "SELECT {$list_field} FROM " .DATABASE_PREFIX. $list_table;

    
    
    if($where)
    {
			if(preg_match("/\\[(([^\\:]+:[^\\]]+)|(entity_id)|(identity))\\]/ims", $where))
			{
				//var_dump($where);
				//echo "AAAAAAAAAAA";
				$old_view = $this->view;
				$old_template = $this->template;
				$this->template = $where;
				$this->view_entity(false, "");
				
				
				$where  = $this->view;
				$this->view = $old_view;
				$this->template = $old_template;
				unset($old_view);
				unset($old_template);
			}
			//if($where)
    	$query .= " WHERE NULL IS NOT NULL";
    	if($where)
    		$query .= " OR {$where}";
    }

    $query .= " ORDER BY {$list_field_name} ASC";
    //var_dump($query);
    $result = mysql_query($query)  or die(die_mysql_error_show($query));

//echo mysql_error();
    if($result)
    {
      //var_dump($result);
      $ret .= '
<select name="' . $element_name . '"';
      if( $style != "")
      {
        $ret .= ' style="' . $style . '"';
      }
      $ret .= '' . (isset($superadmin_keys) && in_array( $element_name, $superadmin_keys)?' disabled="disabled"':'') . '>';

      $ret .= '
<option value="0">Не выбрано</option>';

//var_dump($row[$list_field]);
//var_dump($out);
      while($row = mysql_fetch_assoc($result))
      {
        if( $row[$list_field] == $out )
        {
//echo "aaa";
          continue;
        }

        $list_element = new CEntityClass(
          array(           // массив параметров базы данных объекта
            "table"=>$list_table,                // таблица объекта
            "id"=>$row[$list_field],                  // идентификатор объекта в БД
            "headers_keys"=>array($list_field, $list_field_name),    // поля заголовков
            "nolang_keys"=>array($list_field),     // поля которые не участвуют в поддержке языков
            "index_suffix"=>substr($list_field,-2,2),        // суфикс для индексного поля
            "template"=>                    '
      <option value="[header:' . $list_field . ']"%selected%>[header:' . $list_field_name . ']</option>', // шаблон отображения объекта
          )
        );
        if(
          $list_element->entity_id == $selected
          || isset($_GET[$element_name]) && $_GET[$element_name] == $list_element->entity_id
        )
        {
          $list_element->template = str_replace("%selected%", " selected", $list_element->template);
        }
        else
        {
          $list_element->template = str_replace("%selected%", "", $list_element->template);
        }

        $list_element->view_entity(false);
        $ret .= $list_element->view;
      }


      $ret .= '
</select>';
    }
*/
        //var_dump($ret);
        return $ret;

    }

    //---------------------------------------------------------------------------
    function GetSelectArrayListEx()
    {

    }





    //---------------------------------------------------------------------------
    // функция возвращает дату из строкового значения формата "Y.m.d H:i:s"
    function GetDatetimeFromString($str)
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


    //---------------------------------------------------------------------------
    // Функция возвращает название для элемента, с учетом предопределенных значений
    // get_caption($element_name, $custom_captions)
    function GetCaption($element_name, $custom_captions=array())
    {
        global $MAIN;
        $ret = "";
        $tableFieldParams = $MAIN->GetTableFieldParams($this->table, $element_name);
        if(in_array($element_name, array_keys($custom_captions)))
        {
            $ret = $custom_captions[$element_name];
        }
        else
        {
            $ret = $MAIN->GetCurrentArrayLang($tableFieldParams["name"]);
        }
        return $ret;
    }


    //---------------------------------------------------------------------------
    // Функция замены значения списка в шаблоне
    // Формат шаблона для табличного значния: '[listitem:таблица:имя_поля:поле_сущности]' 
    // Формат шаблона для значния из переменной: '[listitem:_values_:имя_переменной:поле_сущности]' 
    function ListItemReplaceEx($matches)
    {
        global $MAIN;
        //var_dump($matches);
        //return print_r($matches,true);
        $ret = '';


        // Значение из списка
        if($matches[1] == "_values_")
        {
            // специальный список из значений в переменной
            if(isset($GLOBALS[$matches[2]]) && isset($GLOBALS[$matches[2]][$this->headers[$matches[3]]]) )
            {
                if(is_array($GLOBALS[$matches[2]][$this->headers[$matches[3]]]))
                {
                    $ret = $MAIN->GetCurrentArrayLang($GLOBALS[$matches[2]][$this->headers[$matches[3]]]);
                }
                else
                {
                    $ret = $GLOBALS[$matches[2]][$this->headers[$matches[3]]];
                }
                //return print_r($GLOBALS[$matches[2]][$this->headers[$matches[3]]],true);
            }
            return $ret;
        }


        //error_log(print_r($matches, true));
        //error_log(print_r($this->headers[$matches[3]], true));


        // Значение из таблицы
        $item = new CEntity(array("table"=>$matches[1], "id"=>$this->headers[$matches[3]]));

        //$ret = "none";
        $matches2 = null;
        if(preg_match("/^(\\w[\\w\\d]*)(\\+(\\w[\\w\\d]*))+$/",$matches[2],$matches2))
        {
            $ret = "";
            for($i=1;$i<count($matches2); $i+=2)
            {
                //$ret .= $matches2[$i] . " ";
                $ret .= $item->GetHeader($matches2[$i]) . " ";
            }
            //$ret = $item->GetHeader($matches[2]);
        }
        else
        {
            $ret = $item->GetHeader($matches[2]);
        }
        if(!$ret)
        {
            $ret = "none";
        }
        return $ret;

        return "[test]";
    }

}


//-----------------------------------------------------------------------------
// Класс сущности высокого уровня (оболочка)
class CEntity
{
    var $entity_id;     // индекс записи в БД
    var $identity;      // ID записи в БД
    var $table;         // имя таблицы где хранятся записи поля текстов, заголовков, идентификаторы картинок
    var $index_suffix;  // суфикс для индексного поля
    var $entity;        // экземпля сущности
    var $order; 				// значение для упорядочивания
    var $where; 				// условия для отбора сущности
    var $function;			// Функция кастомизации отображения func($this, &$template);


    var $entity_ex;

    //---------------------------------------------------------------------------
    function __toString()
    {
        if($this->identity)
            return "".$this->identity;

        return "";
    }

    //---------------------------------------------------------------------------
    // конструктор
    function CEntity(
        $params = array(
            "table"=>null,  // таблица
            "id"=>null,     // идентификатор
            "index_suffix"=>"id", // суффикс
            "keys"=>array(), // ключи для сущности
            "date_format" => "d.m.Y H:i:s", // формат отображения даты/времени
            "template" => "none",
            "where"=>"",
            "function"=>false,	// функция кастомизации отображения func($this, &$template);
        )
    )
    {
        global $MAIN;

        if(!isset($params["table"]))
        {
            die($MAIN->ErrorShow(array("message"=>"Не указана таблица!")));
        }
        if(!isset($params["id"]))
        {
            $params["id"] = null;
        }
        if(!isset($params["index_suffix"]))
        {
            $params["index_suffix"] = "id";
        }
        if(!isset($params["keys"]))
        {
            $params["keys"] = array();
        }
        if(!isset($params["date_format"]))
        {
            $params["date_format"] = "d.m.Y H:i:s";
        }
        if(!isset($params["template"]))
        {
            $params["template"] = "none";
        }
        if(!isset($params["where"]))
        {
            $params["where"] = "";
        }
        if(!isset($params["function"]))
        {
            $params["function"] = false;
        }


        $this->table = $params["table"];
        $this->index_suffix = $params["index_suffix"];
        $this->where = $params["where"];
        $this->function = $params["function"];


        //echo "<br /><br /><br /><br /><br />";
        //var_dump($db_params);
        //var_dump($params);
        //var_dump($db_params["sounds_keys"]);
        //var_dump($params1["date_format"]);


        $this->entity_ex = new CEntityEx($params);
        $this->identity = $this->entity_ex->identity;
        $this->entity_id = $this->entity_ex->id;
        $this->order = $this->entity_ex->order_value;
        //var_dump($this->entity_ex);

    }

    /**
     * Функция возвращает иерархию объекта и его потомков
     * @return Ambigous <string, CArrayList>
     */
    function GetHierarchyChildren()
    {
        global $MAIN;

        $ret = false;

        $hierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
        if($this->identity && is_array($hierarchy)
            && isset($hierarchy["hierarchy"]) && $hierarchy["hierarchy"] == "true"
            && isset($hierarchy["hierarchy_parent_field"]) && $hierarchy["hierarchy_parent_field"]
        )
        {
            $list = new CArrayList();
            $list->Add($this->identity, $this);
            $this->GetHierarchyChildrenRecursion($this, $list);
            $ret = $list;
            //var_dump($list);
        }

        return $ret;
    }

    // 


    /**
     * Функция (через параметр $list) возвращает иерархию объектов
     * для заданного родителя
     * @param CEntity $parent
     * @param CArrayList $list
     */
    function GetHierarchyChildrenRecursion($parent, &$list)
    {
        if(!is_a($parent, "CEntity") || !$parent || !$parent->identity
            || !is_a($list,"CArrayList") || !$list )
        {
            return;
        }
        foreach($parent->GetChildren() as $value)
        {
            //var_dump($value); echo "<br><br>";
            //error_log($key);
            //var_dump($value->identity); echo "<br><br>";
            $list->AddChild($value->identity, $value, $parent->identity);
            $this->GetHierarchyChildrenRecursion($value, $list);
        }
    }

    /**
     * Функция возвращает array("id"=>object) потомков объекта для иерархичных таблиц
     * @param unknown_type $params
     * @return multitype:CEntity
     */
    function GetChildren($params = array("where"=>""))
    {
        global $MAIN;
        $ret = array();

        if(!isset($params["where"]))
            $params["where"] = $this->where;

        $hierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
        if($this->identity && is_array($hierarchy)
            && isset($hierarchy["hierarchy"]) && $hierarchy["hierarchy"] == "true"
            && isset($hierarchy["hierarchy_parent_field"]) && $hierarchy["hierarchy_parent_field"]
        )
        {
            // только для иерархиичных таблиц
            $where = "`{$hierarchy["hierarchy_parent_field"]}` = '{$this->identity}'";
            if($params["where"])
            {
                $where .= " AND " . $params["where"];
            }
            $list = new CEntityList(
                array(
                    "table" => $this->table,
                    "where" => $where,
                )
            );

            $ret = $list->list;
            //var_dump($ret);
        }
        return $ret;
    }

    /**
     * @return Ambigous <string, unknown>
     */
    function GetParentTable()
    {
        global $MAIN;

        $ret = "";
        $hierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
        if($this->identity && is_array($hierarchy)
            && isset($hierarchy["hierarchy"]) && $hierarchy["hierarchy"] == "true"
            && isset($hierarchy["hierarchy_parent_field"]) && $hierarchy["hierarchy_parent_field"]
        )
        {
            $ret = $this->table;
        }
        elseif($this->identity && is_array($hierarchy)
            && isset($hierarchy["parent"]) && $hierarchy["parent"] == "true"
            && isset($hierarchy["parent_table"]) && $hierarchy["parent_table"]
            && isset($hierarchy["parent_field"]) && $hierarchy["parent_field"]
        )
        {
            $ret = $hierarchy["parent_table"];
        }
        return $ret;
    }

    /**
     * Функция возвращает identity родителя объекта
     * @return string
     */
    function GetParentId()
    {
        global $MAIN;

        $ret = "";
        $hierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
        if($this->identity && is_array($hierarchy)
            && isset($hierarchy["hierarchy"]) && $hierarchy["hierarchy"] == "true"
            && isset($hierarchy["hierarchy_parent_field"]) && $hierarchy["hierarchy_parent_field"]
        )
        {
            $parent_field = $hierarchy["hierarchy_parent_field"];
            $ret = $this->GetHeader($parent_field);
        }
        elseif($this->identity && is_array($hierarchy)
            && isset($hierarchy["parent"]) && $hierarchy["parent"] == "true"
            && isset($hierarchy["parent_table"]) && $hierarchy["parent_table"]
            && isset($hierarchy["parent_field"]) && $hierarchy["parent_field"]
        )
        {
            $parent_field = $hierarchy["parent_field"];
            $ret = $this->GetHeader($parent_field);
        }
        return $ret;
    }

    /**
     * Функция возвращает родителя текущего объекта
     * @return CEntity
     */
    function GetParent()
    {
        global $MAIN;
        $ret = false;

        $hierarchy = $MAIN->GetTableParam($this->table, "hierarchy");
        if($this->identity && is_array($hierarchy)
            && isset($hierarchy["hierarchy"]) && $hierarchy["hierarchy"] == "true"
            && isset($hierarchy["hierarchy_parent_field"]) && $hierarchy["hierarchy_parent_field"]
        )
        {
            $parent_field = $hierarchy["hierarchy_parent_field"];
            $parent_id = $this->GetHeader($parent_field);
            if($parent_id)
            {
                $parent = new CEntity(array("table"=>$this->table, "id"=>$parent_id));
                if($parent->identity)
                {
                    $ret = $parent;
                }
            }
        }
        elseif($this->identity && is_array($hierarchy)
            && isset($hierarchy["parent"]) && $hierarchy["parent"] == "true"
            && isset($hierarchy["parent_table"]) && $hierarchy["parent_table"]
            && isset($hierarchy["parent_field"]) && $hierarchy["parent_field"]
        )
        {
            $parent_field = $hierarchy["parent_field"];
            $parent_id = $this->GetHeader($parent_field);
            if($parent_id)
            {
                $parent = new CEntity(array("table"=>$hierarchy["parent_table"], "id"=>$parent_id));
                if($parent->identity)
                {
                    $ret = $parent;
                }
            }

        }

        unset($hierarchy);
        //var_dump($ret);
        //error_log("CEntity::GetParent ".var_export($ret?$ret->identity:$ret, true));
        return $ret;
    }



    /**
     * Функция возвращает иерархию объекта (всех родителей) в виде иерархичного списка CList
     * @param CEntity $current
     * @return CArrayList
     */
    function GetHierarchy()
    {
        global $MAIN;
        $ret = new CArrayList();

        //$current = $this;
        $ret->Add($this->identity,$this);
        $parent = $this->GetParent();

        //var_dump($parent);

        while($parent && $parent->identity)
        {

            $current = $parent;
            $ret->AddParent($current->identity,$current);
            $parent = $current->GetParent();
        }


        return $ret;
    }

//  /**
//   * @param CListObject $obj
//   * @param CEntity $current
//   */
//  function HierarchyItems(&$obj, $current_id)
//  {
//  	$current = new CEntity(array("table"=>$this->table, "id"=>$current_id));
//  	if($current->identity)
//  	{
//	  	$parent_id = $current->GetParentId();
//	  	if($parent_id)
//	  	{
//	  		$new_obj = new CListObject($current->identity, $current, false, false);
//	  		$parent = $this->HierarchyItems($new_obj, $parent_id);
//	  		$new_obj->AttachParent($parent);
//	  		if($obj)
//	  			$new_obj->AppendChild($obj);
//	  		//$new_obj->parent = $this->HierarchyItems($new_obj, $parent_id);
//	  	}
//	  	else
//	  	{
//	  		$new_obj = new CListObject($current->identity, $current, false, false);
//	  		if($obj)
//	  			$new_obj->AppendChild($obj);
//	  	}
//	  	if(!$obj)
//	  	{
//	  		$obj = $new_obj;
//	  	}
//	  	else 
//	  	{
//	  		//$new_obj->childs = array($obj);
//	  		$obj->AttachParent($new_obj);
//	  	}
//	  	//var_dump($new_obj);
//	  	return $new_obj;
//  	}
//  	return $obj;
//  }
//  
//  /**
//   * Функция возвращает иерархию объекта в виде иерархичного списка CList
//   * @param CEntity $current
//   * @return CList
//   */
//  function GetHierarchy($current = false)
//  {
//  	global $MAIN;
//  	$ret = new CList();
//  	
//  	$obj = false;
//  	$this->HierarchyItems($obj, $this);
//  	$ret->Add($obj);
//  	//error_log(print_r($ret, true));
//  	return $ret;
//  }


    /**
     * Функция возвращает значение поля для любого типа
     * @param string $field
     * @return Ambigous|bool|null|unknown
     */
    function GetField($field)
    {
        global $MAIN;
        $tableFieldParams = $MAIN->GetTableFieldParams($this->table, $field);

        switch($tableFieldParams["type"])
        {
            case "image":
                return $this->GetImage($field);
            case "file":
                return $this->GetFile($field);
            case "video":
                return $this->GetVideo($field);
            case "sound":
                return $this->GetSound($field);
            case "datetime":
                return $this->GetDate($field);
            case "text":
                return $this->GetText($field);
            default:
                return $this->GetHeader($field);
        }
        return NULL;
    }


    /**
     * Функция возвращает вид для значения поля для любого типа
     * @param string $field
     * @return Ambigous|bool|null|unknown
     */
    function GetFieldView($field)
    {
        global $MAIN;
        $MAIN->LoadLangMessages(__FILE__);
        $tableFieldParams = $MAIN->GetTableFieldParams($this->table, $field);

        switch($tableFieldParams["type"])
        {
            case "image":
                $template = <<<EOT
<img src="[image_file:$field]" />
EOT;
                $ret = $this->View(array("template"=>$template));
                return $ret;
            case "select":
                $template = <<<EOT
[header:{$field}]
EOT;
                if(isset($tableFieldParams["params"]["select"]["values"]))
                {
                    $template = '[listitem:_values_:'.$tableFieldParams["params"]["select"]["values"].':' . $field.']';
                }
                elseif(isset($tableFieldParams["params"]["select"]["table"])
                    && is_array($tableFieldParams["params"]["select"]["table"]))
                {
                    $template = '[listitem:'.$tableFieldParams["params"]["select"]["table"]["name"].':'.$tableFieldParams["params"]["select"]["table"]["namefield"].':' . $field.']';
                }

                $ret = $this->View(array("template"=>$template));
                return $ret;
            case "file":
                return $this->GetFile($field);
            case "video":
                return $this->GetVideo($field);
            case "sound":
                return $this->GetSound($field);
            case "datetime":
                return $this->GetDate($field);
            case "text":
                return $this->GetText($field);
            case "checkbox":
                return $this->GetHeader($field)=="1"?$MAIN->GetLangMessage("ENTITY_HEADER_VIEW_YES"):$MAIN->GetLangMessage("ENTITY_HEADER_VIEW_NO");
            case "custom":
                $ret = $this->View(array("template"=>"[custom:{$field}]"));
                return $ret;
            default:
                return $this->GetHeader($field);
        }
        return NULL;
    }


    /**
     * Фнукция проверки параметров объекта перед сохранением
     * @param array $values
     * @return Ambiguous
     */
    function SaveCheck($values = array())
    {
        global $MAIN;

        $ret = true;

        if($this->identity)
        {
            $tableHierarchy = $MAIN->GetTableParam($this->table, "hierarchy");

            // проверим иерархию
            if($tableHierarchy && isset($tableHierarchy["hierarchy"])
                && $tableHierarchy["hierarchy"]=="true"
                && isset($tableHierarchy["hierarchy_parent_field"])
                && $tableHierarchy["hierarchy_parent_field"] )
            {
                if(isset($values[$tableHierarchy["hierarchy_parent_field"]])
                    && $values[$tableHierarchy["hierarchy_parent_field"]])
                {
                    //					$hierarchy = $this->GetHierarchy();
                    //					if($hierarchy->Find($values[$tableHierarchy["hierarchy_parent_field"]]))
                    //						$ret = false;
                    // родитель не может быть текущим объектом 
                    // (сам себе родитель)
                    if($values[$tableHierarchy["hierarchy_parent_field"]] == $this->identity)
                        $ret = false;

                    // один из потомков не может быть родителем текущего объекта (сын не может быть папой отца)
                    $hierarchyChildren = $this->GetHierarchyChildren();
                    if($hierarchyChildren && $hierarchyChildren->Find($values[$tableHierarchy["hierarchy_parent_field"]]))
                    {
                        $ret = false;
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Функция сохранения сущности в БД
     * @param array(string => Ambiguous) $values
     * @return Ambiguous
     */
    function Save($values = array())
    {
        global $MAIN;


        if($this->SaveCheck($values))
        {
            $this->entity_ex->SaveEx($values);
            if($this->entity_ex->identity)
            {
                $this->entity_id = $this->entity_ex->id;
                $this->identity = $this->entity_ex->identity;
                return true;
            }
        }
        return false;
    }

    //---------------------------------------------------------------------------
    // Функция сохранения сущности из формы запрос _POST, _FILES,
    // через _GET[$action_name] === $action_value
    // параметры:
    //   $keys - массив ключей, значения дла которых должны быть получены из POST запроса
    //   $setvalues - значения передаются в ассоциативном массиве "key" => "value",
    //   если значение для заданного ключа есть, то значени из _POST заменяется на заданное
    function SavePostParams($keys, $setvalues=array(), $action_name="action", $action_value="edit")
    {
        global $MAIN;

        if(isset($MAIN))
        {
            if($MAIN->CheckPost($action_name, $action_value))
            {
                $values = array();

                // обарабатываем запрос
                foreach($keys as $key)
                {
                    $tableFieldParams = $MAIN->GetTableFieldParams($this->table, $key);
                    //var_dump($tableFieldParams["type"]);
                    //          if(
                    //          (
                    //            $tableFieldParams["type"] != "checkbox"
                    //            && $tableFieldParams["type"] != "image"
                    //            && $tableFieldParams["type"] != "video"
                    //            && $tableFieldParams["type"] != "sound"
                    //            && $tableFieldParams["type"] != "file"
                    //          )
                    //          && !isset($_POST[$key]))
                    //          {
                    //            // нет нужно ключа в POST запросе
                    //            die($MAIN->ErrorShow(array("message" => "Нет нужного ключа в POST запросе: " . $key)));
                    //          }
                    //          else
                    {
                        if(!$tableFieldParams)
                        {
                            die($MAIN->ErrorShow(array("message" => "Нет нужного ключа в таблице: " . $key)));
                        }
                        switch($tableFieldParams["type"])
                        {
                            case "text":
                            case "datetime":
                            case "string":
                            case "password":
                            case "number":
                            case "select":
                                $values[$key] = $_POST[$key];
                                continue;
                            case "checkbox":
                                $values[$key] = isset($_POST[$key])?$_POST[$key]:"0";
                                continue;
                            case "image":
                                $values = entity_pictures_array($key,$values);
                                continue;
                            case "sound":
                                $values = entity_sounds_array($key,$values);
                                continue;
                            case "video":
                                $values = entity_videos_array($key,$values);
                                continue;
                            case "file":
                                $values = entity_files_array($key,$values);
                                continue;
                            default:
                                continue;
                        }
                    }
                }
                foreach($setvalues as $key => $value)
                {
                    $values[$key] = $setvalues[$key];
                }

                //var_dump($values);
                //exit;
                return $this->Save($values);
            }
        }
        return false;
    }


    //---------------------------------------------------------------------------
    // функция сохранения сущности из формы с запросом по умолчанию
    // все параметры из настроек
    function SavePost()
    {
        global $MAIN;
        if(isset($MAIN))
        {

            if($MAIN->is_admin && $this->identity && !$MAIN->adminuser->CheckEntityAccess($this, "w"))
            {
                $MAIN->adminuser->AddError("Редактирование запрещено!");
                die("Редактирование запрещено!");
                return false;
            }
            if($MAIN->is_admin && !$this->identity && !$MAIN->adminuser->CheckEntityAccess($this, "a"))
            {
                $MAIN->adminuser->AddError("Добавление запрещено!");
                die("Добавление запрещено!");
                return false;
            }

            //var_dump($MAIN);
            $keys = array();
            $setvalues = array();

            $tableFieldsParams = $MAIN->GetTableFieldsParams($this->table);
            foreach($tableFieldsParams as $key => $value)
            {
                if(isset($value["params"]["value"]) && is_array($value["params"]["value"])
                    && count($value["params"]["value"]))
                {
                    if(isset($value["params"]["nolang"]) && $value["params"]["nolang"] === "true")
                    {
                        $setvalues[$key] = $MAIN->GetArrayLang($value["params"]["value"],LANGUAGE_DEFAULT);
                    }
                    else
                    {
                        $setvalues[$key] = $MAIN->GetCurrentArrayLang($value["params"]["value"]);
                    }

                    // специальные значения для типов
                    if($value["type"] == "datetime" && $setvalues[$key] === "NOW")
                    {
                        $setvalues[$key] = date("Y-m-d H:i:s");
                    }
                    else if($value["type"] == "datetime")
                    {
                        $setvalues[$key] = $_POST[$key];
                    }
                    continue;
                }
                if(isset($value["params"]["hidden"]) && $value["params"]["hidden"] === "true")
                {
                    continue;
                }
                array_push($keys, $key);

            }

            /*
      var_dump($keys);
      echo "<br /><br />";
      var_dump($setvalues);
      echo "<br /><br />";
      exit;
      */

            return $this->SavePostParams($keys, $setvalues);
        }
        return false;
    }


    //---------------------------------------------------------------------------
    // Функция удаления экземпляра сущности
    function Delete()
    {
        global $MAIN;
        if($MAIN->is_admin && $this->identity && !$MAIN->adminuser->CheckEntityAccess($this, "d"))
        {
            $MAIN->adminuser->AddError("Удаление запрещено!");
            die("Удаление запрещено!");
            return false;
        }

        // удаление экземпляра сущности
        $this->entity_ex->DeleteEx();
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | string
     */
    function GetHeader($key)
    {
        //global $MAIN;
        if(isset($this->entity_ex)
            && is_a($this->entity_ex,"CEntityEx")
            && isset($this->entity_ex->headers[$key]))
        {
            if(CMain::GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
            {
                return $this->GetHeaderEx($key);
            }
            return CMain::GetCurrentValueLang($this->GetHeaderEx($key));
        }
        return false;
    }


    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | string
     */
    function GetHeaderEx($key)
    {
        //var_dump($key);
        //var_dump($this->entity_ex);
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->headers[$key]))
        {
            return $this->entity_ex->headers[$key];
        }
        //echo "AAAAA";
        return false;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | string
     */
    function GetText($key)
    {
        $ret = false;
        if(CMain::GetTableFieldParamsParam($this->table, $key, "nolang") == "true")
        {
            $ret = $this->GetTextEx($key);
        }
        else
        {
            $ret = CMain::GetCurrentValueLang($this->GetTextEx($key));
        }
        return $ret;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | string
     */
    function GetTextEx($key)
    {
        $ret = false;
        if(isset($this->entity_ex)
            && is_a($this->entity_ex,"CEntityEx")
            && isset($this->entity_ex->texts[$key]))
        {

            $ret = $this->entity_ex->texts[$key];
        }

        return $ret;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | string
     */
    function GetDate($key)
    {
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->dates[$key]))
        {
            return $this->entity_ex->dates[$key];
        }

        return false;
    }



    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | ImageClass
     */
    function GetImage($key)
    {
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->images[$key]))
        {
            return $this->entity_ex->images[$key];
        }

        return false;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | SoundClass
     */
    function GetSound($key)
    {
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->sounds[$key]))
        {
            return $this->entity_ex->sounds[$key];
        }

        return false;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | VideoClass
     */
    function GetVideo($key)
    {
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->videos[$key]))
        {
            return $this->entity_ex->videos[$key];
        }

        return false;
    }

    /**
     * Функция возвращает значение по ключу
     * @param string $key
     * @return bool | FileClass
     */
    function GetFile($key)
    {
        if(isset($this->entity_ex) && is_a($this->entity_ex,"CEntityEx") && isset($this->entity_ex->files[$key]))
        {
            return $this->entity_ex->files[$key];
        }

        return false;
    }


    //---------------------------------------------------------------------------
    // Функция отображения сущности
    // параметры по умолчанию "template_name"=>"default", "template"=>"none", 
    // "function" => false // функция для изменения шаблона объекта при отображении  func($this, &$template)
    function View($params = array(
        "template_name"=>"default",
        "template"=>"none",
        "output"=>"false",
        "function"=>false,
    ))
    {
        global $MAIN;
        if(!isset($params["template_name"]))
        {
            $params["template_name"] = "default";
        }
        if(!isset($params["template"]))
        {
            if($this->entity_ex->template)
            {
                // если есть значение шаблона в entity то берем его
                // это значение по умолчанию
                $params["template"] = $this->entity_ex->template;
            }
            else
            {
                $params["template"] = "none";
            }
        }
        if(!isset($params["output"]))
            $params["output"] = false;
        if(!isset($params["function"]))
            $params["function"] = $this->function;

        if(isset($MAIN->config) && $MAIN->config
            && !$MAIN->is_admin && $MAIN->config->GetHeader("config_site_isclosed") == "1")
        {
            $ret = $MAIN->config->GetText("config_site_isclosed_message");
            if(function_exists("ob_clean"))
            {
                ob_clean();
            }
            echo $ret;
            exit;
        }

        if($this->function)
        {
            $params_function = $params["function"];
            $entity_ex_function = $this->entity_ex->function;

            $params["function"] = false;
            $this->entity_ex->function = false;
            $view = $this->entity_ex->ViewEx($params);
            if($this->function && is_callable($this->function))
            {
                $function = $this->function;
                $function($this, $view);
            }
            $params["function"] = $params_function;
            $this->entity_ex->function = $entity_ex_function;
            return $view;
        }
        return $this->entity_ex->ViewEx($params);


        //    global $MAIN;
        //
        //    $ret = "";
        //
        //    if(!isset($params["template_name"]))
        //    {
        //      $params["template_name"] = "default";
        //    }
        //    if(!isset($params["template"]))
        //    {
        //    	if($this->entity->template)
        //    	{
        //    		// если есть значение шаблона в entity то берем его
        //    		// это значение по умолчанию
        //      	$params["template"] = $this->entity->template;
        //    	}
        //    	else 
        //    	{
        //      	$params["template"] = "none";
        //    	}
        //    }
        //
        //    $old_template = $this->entity->template;
        //    $this->entity->template = $params["template"];
        //    $this->entity->view_entity(false);
        //    $ret = $this->entity->view;
        //    $this->entity->template = $old_template;
        //
        //    if($MAIN->config && !$MAIN->is_admin && $MAIN->config->GetHeader("config_site_isclosed") == "1")
        //    {
        //      $ret = $MAIN->config->GetText("config_site_isclosed_message");
        //      if(function_exists("ob_clean"))
        //      {
        //        ob_clean();
        //      }
        //      echo $ret;
        //      exit;
        //    }
        //
        //    return $ret;
    }

    //---------------------------------------------------------------------------
    // Функция отображения формы редактирования
    function ViewEdit($params = array(
        "template_name"=>"default",
        "template_type"=>"default",
        "template"=>"none",
        "function"=>false,
    ))
    {
        global $MAIN;
        //var_dump($MAIN->adminuser->CheckEntityAccess($this, "r"));
        //var_dump($MAIN->adminuser->CheckEntityAccess($this, "w"));

        $ret = false;
        if($MAIN->is_admin && $MAIN->adminuser->CheckEntityAccess($this, "r"))
        {
            if($MAIN->is_admin && !$MAIN->adminuser->CheckEntityAccess($this, "w"))
            {
                $MAIN->adminuser->AddMessage("Разрешен только просмотр.");
            }
        }
        elseif($MAIN->is_admin && !$this->identity && !$MAIN->adminuser->CheckEntityAccess($this, "a"))
        {
            $MAIN->adminuser->AddError("Добавление запрещено!");
        }
        elseif($MAIN->is_admin && !$MAIN->adminuser->CheckEntityAccess($this, "r"))
        {
            $MAIN->adminuser->AddError("Доступ закрыт!");
        }

        //var_dump($MAIN->adminuser->ErrorsCount());
        if(!$MAIN->adminuser->ErrorsCount())
            $ret = $this->entity_ex->ViewEditEx($params);

        return $ret;
    }


    /**
     * Функция отображения формы редактирования
     * @param $file
     * @param array $params
     * @return string
     */
    function ViewEditEx( $file,
                         $params = array(
                             "template"=>"default",
                             "function"=>false,
                         )
    )
    {
        global $MAIN;

        $MAIN->LoadLangMessages(__FILE__);
        //var_dump($MAIN->adminuser->CheckEntityAccess($this, "r"));
        //var_dump($MAIN->adminuser->CheckEntityAccess($this, "w"));

        $ret = "";

        if(!isset($params["template"]))
        {
            $params["template"] = "default";
        }
        if(!isset($params["function"]))
        {
            $params["function"] = false;
        }

        if($MAIN->is_admin && $MAIN->adminuser->CheckEntityAccess($this, "r"))
        {
            if($MAIN->is_admin && !$MAIN->adminuser->CheckEntityAccess($this, "w"))
            {
                $MAIN->adminuser->AddMessage("Разрешен только просмотр.");
            }
        }
        elseif($MAIN->is_admin && !$this->identity && !$MAIN->adminuser->CheckEntityAccess($this, "a"))
        {
            $MAIN->adminuser->AddError("Добавление запрещено!");
        }
        elseif($MAIN->is_admin && !$MAIN->adminuser->CheckEntityAccess($this, "r"))
        {
            $MAIN->adminuser->AddError("Доступ закрыт!");
        }

        if($MAIN->adminuser->ErrorsCount())
        {
            // есть ошибки, не выводим
            return $ret;
        }


        // собственно выводим шаблон редактирования
        $template_viewedit_tab_menu = $MAIN->GetTemplateContent($file, $params["template"], "page", "viewedit_tab_menu.php");
        $template_viewedit_tab = $MAIN->GetTemplateContent($file, $params["template"], "page", "viewedit_tab.php");
        $template_viewedit_item = $MAIN->GetTemplateContent($file, $params["template"], "page", "viewedit_item.php");

        global $viewedit_type, $viewedit_id, $viewedit_action, $view_edit_tab_menus, $view_edit_tabs, $viewedit_tabs_show_all, $viewedit_tabs_show_tabs;
        $viewedit_type = $this->table;
        $viewedit_id = $this->identity;

        $viewedit_action = $MAIN->QueryStringWithoutParams();

        $view_edit_tab_menus = "";
        $view_edit_tabs = "";

        $view_edit_tab_menus = $this->ViewEditTabMenusEx($file, $params["template"], "page", $template_viewedit_tab_menu);
        $view_edit_tabs = $this->ViewEditTabsEx($file, $params["template"], "page", $template_viewedit_tab);

        $viewedit_tabs_show_all = $MAIN->GetLangMessage("ENTITY_TABS_SHOW_ALL");
        $viewedit_tabs_show_tabs = $MAIN->GetLangMessage("ENTITY_TABS_SHOW_TABS");

        global $viewedit_delete_link_up, $viewedit_delete_link_down;
        $viewedit_delete_link_up = $viewedit_delete_link_down = "";
        if($this->identity)
        {
            $viewedit_delete_link_up = $viewedit_delete_link_down = $MAIN->GetAdminPageUrl($this->table, "delete", array("id"=>$this->identity), true, false);
        }


        $ret .= $MAIN->ShowTemplate($file, $params["template"], "page", "viewedit.php");

        if($params["function"] && is_callable($params["function"]))
        {
            $function = $params["function"];
            $function($this, $ret);
        }

        return $ret;
    }

    /**
     * Возвращает меню (список закладок) для редактирования сущности
     * @param $file
     * @param $template
     * @param $component
     * @return string
     */
    function ViewEditTabMenusEx($file, $template, $component)
    {
        global $MAIN;
        $ret = "";
        $tabs = $MAIN->GetTableParam($this->table, "tabs");
        if(!is_array($tabs))
        {
            $tabs = array(
                "main" => array($MAIN->GetLangMessage("ENTITY_TABS_MAIN")),
            );
        }

        foreach($tabs as $key=>$value)
        {
            global $view_edit_tab_id, $view_edit_tab_name;
            $view_edit_tab_id = $key;
            $view_edit_tab_name = $MAIN->GetCurrentArrayLang($value);
            $ret .= $MAIN->ShowTemplate($file, $template, $component, "viewedit_tab_menu.php");
        }

        return $ret;
    }

    /**
     * Возвращает содержимое закладок для редактирования сущности
     * @param $file
     * @param $template
     * @param $component
     * @return string
     */
    function ViewEditTabsEx($file, $template, $component)
    {
        global $MAIN;
        $ret = "";
        $arTabs = $MAIN->GetTableParam($this->table, "tabs");
        if(!is_array($arTabs))
        {
            $arTabs = array(
                "main" => array($MAIN->GetLangMessage("ENTITY_TABS_MAIN")),
            );
        }

        foreach($arTabs as $tabId=>$tabName)
        {
            global $view_edit_tab_id, $viewedit_items;
            $view_edit_tab_id = $tabId;

            $viewedit_items = "";
            $arFields = $MAIN->GetTableParam($this->table, "fields");
            foreach($arFields as $fieldId=>$fieldParams)
            {
                if(!isset($fieldParams["tab"]) || !$fieldParams["tab"])
                {
                    $fieldParams["tab"] = "main"; // defalt value
                }
                if($fieldParams["tab"] == $tabId)
                {
                    $viewedit_items .= $this->ViewEditTabItemEx($file, $template, $component, $arTabs, $tabId, $fieldId, $fieldParams);
                }
            }
            $ret .= $MAIN->ShowTemplate($file, $template, $component, "viewedit_tab.php");
        }

        return $ret;

    }


    /**
     * возвращает поле в закладке для редактирования сущности
     * @param $file
     * @param $template
     * @param $component
     * @param $arTabs
     * @param $tabId
     * @param $fieldId
     * @param $fieldParams
     * @return string
     */
    function ViewEditTabItemEx($file, $template, $component, $arTabs, $tabId, $fieldId, $fieldParams)
    {
        global $MAIN;
        $ret = "";

        if($MAIN->GetTableFieldParamsParam($this->table, $fieldId, "hidden") == "true")
        {
            return $ret;
        }

        global $view_edit_item_identity,
               $view_edit_item_id,
               $view_edit_item_name,
               $view_edit_item_help,
               $view_edit_item_value,
               $view_edit_item_size,
               $view_edit_item_cols,
               $view_edit_item_rows,
               $view_edit_item_disabled,
               $view_edit_item_checked,
               $view_edit_item_table;

        $view_edit_item_identity = $this->identity;
        $view_edit_item_id = $fieldId;
        $view_edit_item_name = $MAIN->GetCurrentArrayLang($fieldParams["name"]);
        $view_edit_item_help = $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam($this->table, $fieldId, "help"));
        $view_edit_item_value = $this->GetField($fieldId);
        $view_edit_item_size = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "length");
        $view_edit_item_cols = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "cols");
        $view_edit_item_rows = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "rows");
        $view_edit_item_disabled = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "readonly")=="true"?"disabled":"";
        $view_edit_item_checked = "";
        $view_edit_item_table = $this->table;

        $template_file = "";
        switch($fieldParams["type"])
        {
            // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file, varchar
            case "custom":
                $template_file = "viewedit_item_custom.php";
                break;
            case "string":
            case "number":
            case "varchar":
                $view_edit_item_value = $this->GetHeader($fieldId);
                $template_file = "viewedit_item_string.php";
                break;
            case "password":
                $view_edit_item_value = $this->GetHeader($fieldId);
                $template_file = "viewedit_item_password.php";
                break;
            case "text":
                if($MAIN->GetTableFieldParamsParam($this->table, $fieldId, "editor") == "true")
                {
                    $template_file = "viewedit_item_editor.php";
                }
                else
                {
                    $template_file = "viewedit_item_text.php";
                }
                $view_edit_item_value = $this->GetText($fieldId);
                break;
            case "datetime":
                $template_file = "viewedit_item_datetime.php";
                $view_edit_item_value = $this->GetDate($fieldId);
                break;
            case "checkbox":
                if($this->GetHeader($fieldId) == "1")
                {
                    $view_edit_item_checked = "checked";
                }
                else
                {
                    $view_edit_item_checked = "";
                }
                $view_edit_item_value = $this->GetHeader($fieldId);
                $template_file = "viewedit_item_checkbox.php";
                break;
            case "select":

                if($MAIN->GetTableFieldParamEx($this->table, $fieldId, "params", "select", "values"))
                {
                    // список из глобального массива
                    $list = false;
                    $this->entity_ex->GetSelectValuesListArray($MAIN->GetTableFieldParamEx($this->table, $fieldId, "params", "select", "values"), $list);
                    global $view_edit_item_select;
                    $view_edit_item_select = $this->entity_ex->GetListEx($this->table, $fieldId, $list->items, $this->GetHeader($fieldId));
                }
                elseif($MAIN->GetTableFieldParamEx($this->table, $fieldId, "params", "select", "table", "name"))
                {
                    // список из таблицы
                    $list = false;

                    $this->entity_ex->GetSelectTableListArrayRecursion($this->table, $fieldId, $list);
                    //error_log(print_r($list,true));
                    //$this->entity_ex->GetSelectValuesListArray($MAIN->GetTableFieldParamEx($this->table, $fieldId, "params", "select", "values"), $list);
                    global $view_edit_item_select;
                    $view_edit_item_select = $this->entity_ex->GetListEx($this->table, $fieldId, $list->items, $this->GetHeader($fieldId));
                }
                $template_file = "viewedit_item_select.php";
                $view_edit_item_value = $this->GetHeader($fieldId);
                break;
            case "image":
                global $VIEW_IMAGE,
                       $viewedit_item_image_sizex,
                       $viewedit_item_image_sizey,
                       $viewedit_item_image_filesize,
                       $viewedit_item_image_filename,
                       $viewedit_item_image_id,
                       $viewedit_item_image_delete;
                $VIEW_IMAGE = VIEW_IMAGE;
                $viewedit_item_image_sizex = $this->GetImage($fieldId)->GetSizeX();
                $viewedit_item_image_sizey = $this->GetImage($fieldId)->GetSizeY();
                $viewedit_item_image_filesize = $this->GetImage($fieldId)->image_filesize();
                $viewedit_item_image_filename = $this->GetImage($fieldId)->image_filename();
                $viewedit_item_image_id = $this->GetImage($fieldId)->image_id;
                if($viewedit_item_image_id)
                {
                    $viewedit_item_image_delete = "delete_image.php?id={$viewedit_item_image_id}&table={$this->table}&field={$fieldId}&backurl=".urlencode($_SERVER["REQUEST_URI"]);
                }
                else
                {
                    $viewedit_item_image_delete = "";
                }
                $template_file = "viewedit_item_image.php";
                break;
            case "sound":
                global $view_edit_item_sound_id,
                       $viewedit_item_sound_delete;
                $view_edit_item_sound_id = $this->GetSound($fieldId)->sound_id;
                if($view_edit_item_sound_id)
                {
                    $viewedit_item_sound_delete = "delete_sound.php?id={$view_edit_item_sound_id}&table={$this->table}&field={$fieldId}&backurl=".urlencode($_SERVER["REQUEST_URI"]);
                }
                else
                {
                    $viewedit_item_sound_delete = "";
                }
                $template_file = "viewedit_item_sound.php";
                break;
            case "video":
                global $view_edit_item_preview,
                       $view_edit_item_video_id,
                       $view_edit_item_video_width,
                       $view_edit_item_video_height,
                       $viewedit_item_video_delete;
                $view_edit_item_preview = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "preview_field");
                $view_edit_item_video_width = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "width");
                $view_edit_item_video_height = $MAIN->GetTableFieldParamsParam($this->table, $fieldId, "height");
                $view_edit_item_video_id = $this->GetVideo($fieldId)->video_id;
                //error_log(print_r($view_edit_item_video_id,true));
                if($view_edit_item_video_id)
                {
                    $viewedit_item_video_delete = "delete_video.php?id={$view_edit_item_video_id}&table={$this->table}&field={$fieldId}&backurl=".urlencode($_SERVER["REQUEST_URI"]);
                }
                else
                {
                    $viewedit_item_video_delete = "";
                }
                $template_file = "viewedit_item_video.php";
                break;
            case "file":
                global $viewedit_item_file_delete,
                       $viewedit_item_file_filesize,
                       $viewedit_item_file_filename,
                       $viewedit_item_file_fileext,
                       $viewedit_item_file_download,
                       $viewedit_item_file_open;
                $view_edit_item_file_id = $this->GetFile($fieldId)->file_id;
                if($view_edit_item_file_id)
                {
                    $viewedit_item_file_delete = "delete_file.php?id={$view_edit_item_file_id}&table={$this->table}&field={$fieldId}&backurl=".urlencode($_SERVER["REQUEST_URI"]);
                    $viewedit_item_file_download = "/file.php?id={$view_edit_item_file_id}&att=1";
                    $viewedit_item_file_open = "/file.php?id={$view_edit_item_file_id}";
                }
                else
                {
                    $viewedit_item_file_delete = "";
                }
                $viewedit_item_file_filesize = $this->GetFile($fieldId)->get_file_length();
                $viewedit_item_file_filename = $this->GetFile($fieldId)->file_filename();
                $viewedit_item_file_fileext = $this->GetFile($fieldId)->file_ext();

                $template_file = "viewedit_item_file.php";
                break;
        }
        if($template_file)
        {
            $ret .= $MAIN->ShowTemplate($file, $template, $component, $template_file);
        }

        if($MAIN->GetTableFieldParamEx($this->table, $fieldId, "params", "readonly") == "true")
        {
            $view_edit_item_value = htmlspecialchars($this->GetHeader($fieldId));
            $ret .= $MAIN->ShowTemplate($file, $template, $component, "view_edit_hidden.php");
        }

        return $ret;
    }

}


//-----------------------------------------------------------------------------
// Функция возвращает массив картинок
function entity_pictures_array($pictire_name, $ret = array())
{
    global $_FILES;
    if(isset($ret[$pictire_name]))
    {
        unset($ret[$pictire_name]);
    }
    $contents = "";
    //var_dump($_FILES);
    if(($_FILES[$pictire_name]["type"] == "image/gif"
        || $_FILES[$pictire_name]["type"] == "image/jpeg"
        || $_FILES[$pictire_name]["type"] == "image/pjpeg"
        || $_FILES[$pictire_name]["type"] == "image/png"
        || $_FILES[$pictire_name]["type"] == "image/x-png"
    )
        && $_FILES[$pictire_name]["size"] > 0
        && $_FILES[$pictire_name]["size"] <= (defined("IMAGE_MAXFILESIZE")?IMAGE_MAXFILESIZE:10*1024*1024) // 10Mb
        && file_exists($_FILES[$pictire_name]["tmp_name"]) )
    {

        $ret[$pictire_name] =  array (
            "tmp_name" => $_FILES[$pictire_name]["tmp_name"],
            "contenttype" => $_FILES[$pictire_name]["type"],
            "filename" => $_FILES[$pictire_name]["name"]
        );
    }
    return $ret;
}

//-----------------------------------------------------------------------------
// Функция возвращает массив картинок
function entity_pictures_array_key($picture_key, $pictire_name, $ret = array())
{
    global $_FILES;
    if(isset($ret[$picture_key]))
    {
        unset($ret[$picture_key]);
    }
    $contents = "";
    //var_dump($_FILES);
    if(($_FILES[$pictire_name]["type"] == "image/gif"
        || $_FILES[$pictire_name]["type"] == "image/jpeg"
        || $_FILES[$pictire_name]["type"] == "image/pjpeg"
        || $_FILES[$pictire_name]["type"] == "image/png"
        || $_FILES[$pictire_name]["type"] == "image/x-png"
    )
        && $_FILES[$pictire_name]["size"] > 0
        && $_FILES[$pictire_name]["size"] <= (defined("IMAGE_MAXFILESIZE")?IMAGE_MAXFILESIZE:10*1024*1024) // 10Mb
        && file_exists($_FILES[$pictire_name]["tmp_name"]) )
    {

        $ret[$picture_key] =  array (
            "tmp_name" => $_FILES[$pictire_name]["tmp_name"],
            "contenttype" => $_FILES[$pictire_name]["type"],
            "filename" => $_FILES[$pictire_name]["name"]
        );
    }
    return $ret;
}

//-----------------------------------------------------------------------------
// Функция возвращает массив звуков
function entity_sounds_array($sound_name, $ret = array())
{
    global $_FILES;

    //var_dump($_FILES);
    //exit;

    if(isset($ret[$sound_name]))
    {
        unset($ret[$sound_name]);
    }
    $contents = "";
    if(($_FILES[$sound_name]["type"] == "audio/mpeg"
        || $_FILES[$sound_name]["type"] == "application/octet-stream"
    )
        && $_FILES[$sound_name]["size"] > 0
        && $_FILES[$sound_name]["size"] <= (defined("SOUND_MAXFILESIZE")?SOUND_MAXFILESIZE:50*1024*1024) // 50Mb
        && file_exists($_FILES[$sound_name]["tmp_name"]) )
    {

        $ret[$sound_name] =  array (
            "tmp_name" => $_FILES[$sound_name]["tmp_name"],
            "contenttype" => $_FILES[$sound_name]["type"],
            "filename" => $_FILES[$sound_name]["name"]
        );
    }
    //var_dump($ret);
    //exit;
    return $ret;
}


//-----------------------------------------------------------------------------
// Функция возвращает массив картинок
function entity_videos_array($video_name, $ret = array())
{

    global $_FILES;
    if(isset($ret[$video_name]))
    {
        unset($ret[$video_name]);
    }
    $contents = "";
    //var_dump($_FILES);exit;
    if(($_FILES[$video_name]["type"] == "application/octet-stream"
        || $_FILES[$video_name]["type"] == "video/x-flv"
    )
        && $_FILES[$video_name]["size"] > 0
        && $_FILES[$video_name]["size"] <= (defined("VIDEO_MAXFILESIZE")?VIDEO_MAXFILESIZE:50*1024*1024) // 50Mb
        && file_exists($_FILES[$video_name]["tmp_name"]) )
    {

        $ret[$video_name] =  array (
            "tmp_name" => $_FILES[$video_name]["tmp_name"],
            "contenttype" => $_FILES[$video_name]["type"],
            "filename" => $_FILES[$video_name]["name"]
        );
    }
    return $ret;
}


//-----------------------------------------------------------------------------
// Функция возвращает массив файлов
function entity_files_array($file_name, $ret = array())
{
    global $_FILES;

    //var_dump($_FILES);
    //exit;

    if(isset($ret[$file_name]))
    {
        unset($ret[$file_name]);
    }
    $contents = "";
    if(isset($_FILES[$file_name])
        && $_FILES[$file_name]["size"] > 0
        && $_FILES[$file_name]["size"] <= (defined("FILE_MAXFILESIZE")?FILE_MAXFILESIZE:20*1024*1024) // 20Mb
        && file_exists($_FILES[$file_name]["tmp_name"]) )
    {

        $ret[$file_name] =  array (
            "tmp_name" => $_FILES[$file_name]["tmp_name"],
            "contenttype" => $_FILES[$file_name]["type"],
            "filename" => $_FILES[$file_name]["name"]
        );
    }
    //var_dump($ret);
    //exit;
    return $ret;
}

?>
