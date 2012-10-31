<?
//-----------------------------------------------------------------------------
// Файл конфигурации сущности базы данных
//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------
// описание структуры базы в админке
// массив
// array(
//    "Имя_таблицы_1" => array(
//      "name" => array("Название таблицы язык 0", "Название таблицы язык 1"),
//			"superadmin" => "true",	// признак объекта который доступен только суперадмину
//      "admin" => array(
//        "all" => array(
//          "url_template" =>"шаблон URL-а админки для полного списка элементов",
//          "file"=>"файл админки для полного списка элементов"
//          "name"=> array("Рус. название", "Англ. название")
//        ),
//        "edit" => array(
//          "url_template" =>"шаблон URL-а админки для элемента",
//          "file"=>"файл админки для элемента",
//          "name" => array("Рус. название", "Англ. название")
//        ),
//      	"delete" => array(
//        	"url_template" => "page_edit.php?id=[id]&action=delete",
//        	"file" => "page_edit.php",
//        	"name" => array("Удалить"),
//      	),
//        "list" => array(
//          "url_template" =>"шаблон URL-а админки для списка элементов (специальный)",
//          "file" => "файл админки для списка элементов (специальный)",
//          "name" => array("Рус. название", "Англ. название")
//        ),
//        "action" => array(
//          "file" => "файл специальных действий, должен лежать в каталоге 'admin/boxes/'", // нужен для решения дополнительных задач (блог, и т.п.), обрабатывает POST запросы
//        )
//      ),
//			"hierarchy" => array( // параметры иерархии
//				"hierarchy" => "true",	// признак иерархии в текущей таблице
//				"hierarchy_parent_field" => "field333", // поле текущей таблице для родителя иерархии
//    		"parent" => "true",	// признак подчиненности объекта
//				"parent_table" => "table11",	// таблица родетеля 
//				"parent_field"	=> "",	// поле текущей таблицы для иерархии
//			),
//      "fields" => array(
//        "field1" => array(
//          "name" => array("Название поля язык 0", "Название поля язык 1"),
//          "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file, varchar
//          "params" => array( // параметры поля
//            "default" => array("значение по умолчанию язык 0", "значение по умолчанию язык 1"),
//            "editor" => "true", // признак того что для типа text нужен расширенный редактор
//            "length" => "30", // длина поля для полей типа string, number
//            "hidden" => "true", // поле скрыто
//            "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
//            "value" => array("1"),     // Обязательное значение для данного поля, если type=="date" и значение == NOW, то устанавливается значение текущей даты
//            "readonly" => "true",   // неизменяемое значение
//            "template" => "true",   // при редактировании выводится шаблон поля
//      			"unique" => "true",			// признак уникальности поля в таблице
//						"dbtype" => "varchar(200)", // специальное описание типа для поля в БД
//            "width" => "640",   // ширина (размер плеера)
//            "height" => "360",   // высота (размер плеера)
//            "preview_field" => "field_name",   // поле для превью в плеере
//            "select" => array( // параметры поля типа select
//              "size" => "1", // size для поля типа select
//              "multiselect" => "true", // возможность множественного выбора для поля типа select
//              "values" => "user_sexs", // значения в переменной  из "массивы предопределенных значений"
//            	"hierarchy" => "true", // признак иерархии
//              "table" => array(
//                "name" => "tablename", // имя таблицы
//                "field" => "fieldname", // имя поля для значений
//                "parentfield" => "parentfieldname", // имя поля родителя в иерархии
//                "namefield"=>"fieldname_name", // имя поля для имен
//	      				"where" => "somefield='somevalue'",		// фильтрация
//      					"where" => "somefield = '[header:somefield]'",		// фильтрация    
//                "orderfield"=>"fieldname_order" // имя поля упорядочивания
//              ),
//            ),
//          ),
//        ),
//      ),
//      "order" => array("field_order1"=>"ASC","field_order2"=>"DESC"), // поля упорядочивания в админке
//      "autoreorder" => "true", // автоматическое переупорядочивание при занесении новой записи
//    ),
//    "Имя_таблицы_2" => array(),
//
// );

global $entity;
$entity = array(
  "session" => array(
    "name" => array("Сессии"),
	  "superadmin" => "true",	// признак объекта который доступен только суперадмину
  	"admin" => array(),
    "fields" => array(
      "session_datetime"=> array(
        "name" => array("Дата/время изменения"),
        "type" => "datetime", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "default" => array(),
          "value" => array("NOW")
        )
      ),
      "session_ln" => array(
        "name" => array("Идентификатор сессии"),
        "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "length" => "30", // длина поля для полей типа string, number
          "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
          "readonly" => "true",   // неизменяемое значение
      		"dbtype" => "varchar(200)", // специальное описание типа для поля в БД
      	)
      ),
   		"session_last_query" => array(
        "name" => array("Последняя запрос"),
        "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "length" => "30", // длина поля для полей типа string, number
          "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
          "readonly" => "true",   // неизменяемое значение
      	)
      ), 
   		"session_isremember" => array(
        "name" => array("Запомнить пользователя"),
        "type" => "checkbox", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "default" => array(),
          "nolang" => "true",
          "readonly" => "true",   // неизменяемое значение
          //"length" => "30", // длина поля для полей типа string, number
        )
      ), 
   		"session_lang" => array(
        "name" => array("Язык"),
        "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "length" => "30", // длина поля для полей типа string, number
          "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
          "readonly" => "true",   // неизменяемое значение
      	)
      ), 
   		"user_id" => array(
        "name" => array("Пользователь"),
        "type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
        "params" => array( // параметры поля
          "readonly" => "true",   // неизменяемое значение
      		"length" => "30", // длина поля для полей типа string, number
          "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
          "select" => array( // параметры поля типа select
            "size" => "1", // size для поля типа select
            "multiselect" => "false", // возможность множественного выбора для поля типа select
            "table" => array(
              "name" => "user", // имя таблицы
              "field" => "user_id", // имя поля для значений
              "namefield"=>"user_email", // имя поля для имен
              "orderfield"=>"user_email", // имя поля упорядочивания
							"where" => "",		// фильтрация
      			)
          )
        )
     	),
  
  	),
    "order" => array("session_datetime"=>"DESC"), // поля упорядочивания в админке
		"autoreorder" => "false", // автоматическое переупорядочивание при занесении новой записи
  ),

);

//var_dump($db_forms);
?>