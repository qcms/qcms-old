<?
/*
 * Файл конфигурации сущности (объекта) базы данных
 */

///*
// * описание структуры сущности (объекта) в админке
// */
//$entity = array(
//    "Имя_таблицы_1" => array(
//        "name" => array("Название таблицы язык 0", "Название таблицы язык 1"),
//        "superadmin" => "true", // признак объекта который доступен только суперадмину
//        "admin" => array(
//            "all" => array(
//                "url_template" => "шаблон URL-а админки для полного списка элементов",
//                "file" => "файл админки для полного списка элементов",
//                "name" => array("Рус. название", "Англ. название"),
//            ),
//            "edit" => array(
//                "url_template" => "шаблон URL-а админки для элемента",
//                "file" => "файл админки для элемента",
//                "name" => array("Рус. название", "Англ. название"),
//            ),
//            "delete" => array(
//                "url_template" => "page_edit.php?id=[id]&action=delete",
//                "file" => "page_edit.php",
//                "name" => array("Удалить"),
//            ),
//            "list" => array(
//                "url_template" => "шаблон URL-а админки для списка элементов (специальный)",
//                "file" => "файл админки для списка элементов (специальный)",
//                "name" => array("Рус. название", "Англ. название")
//            ),
//            "action" => array(
//                "file" => "файл специальных действий, должен лежать в каталоге 'admin/boxes/'", // нужен для решения дополнительных задач (блог, и т.п.), обрабатывает POST запросы
//            )
//        ),
//        "hierarchy" => array( // параметры иерархии
//            "hierarchy" => "true", // признак иерархии в текущей таблице
//            "hierarchy_parent_field" => "field333", // поле текущей таблице для родителя иерархии
//            "parent" => "true", // признак подчиненности объекта
//            "parent_table" => "table11", // таблица родетеля
//            "parent_field" => "", // поле текущей таблицы для иерархии
//        ),
//        "tabs" => array( // список закладок для ViewEdit, если список пуст, то закладок не будет
//            "code1" => array("Название закладки 1", "Tab name 1"), // определение закладки
//            "code2" => array("Название закладки 2", "Tab name 2"), // определение закладки
//        ),
//        "fields" => array(
//            "field1" => array(
//                "name" => array("Название поля язык 0", "Название поля язык 1"),
//                "help" => array("Подсказка для поля на языке 0", "Подсказка для поля на языке 0"),
//                "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file, varchar
//                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
//                "params" => array( // параметры поля
//                    "default" => array("значение по умолчанию язык 0", "значение по умолчанию язык 1"),
//                    "editor" => "true", // признак того что для типа text нужен расширенный редактор
//                    "length" => "30", // длина поля для полей типа string, number
//                    "cols" => "50", // ширина поля (количество символов) для полей типа text
//                    "rows" => "20", // высота поля (количество строк) для полей типа text
//                    "hidden" => "true", // поле скрыто
//                    "nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
//                    "value" => array("1"), // Обязательное значение для данного поля, если type=="date" и значение == NOW, то устанавливается значение текущей даты
//                    "readonly" => "true", // неизменяемое значение
//                    "template" => "true", // при редактировании выводится шаблон поля
//                    "unique" => "true", // признак уникальности поля в таблице
//                    "dbtype" => "varchar(200)", // специальное описание типа для поля в БД
//                    "width" => "640", // ширина (размер плеера)
//                    "height" => "360", // высота (размер плеера)
//                    "preview_field" => "field_name", // поле для превью в плеере
//                    "select" => array( // параметры поля типа select
//                        "size" => "1", // size для поля типа select
//                        "multiselect" => "true", // возможность множественного выбора для поля типа select
//                        "values" => "user_sexs", // значения в переменной  из "массивы предопределенных значений"
//                        "hierarchy" => "true", // признак иерархии
//                        "table" => array(
//                            "name" => "tablename", // имя таблицы
//                            "field" => "fieldname", // имя поля для значений
//                            "parentfield" => "parentfieldname", // имя поля родителя в иерархии
//                            "namefield" => "fieldname_name", // имя поля для имен
//                            "where" => "somefield='somevalue'", // фильтрация
//                            "where" => "somefield = '[header:somefield]'", // фильтрация
//                            "orderfield" => "fieldname_order", // имя поля упорядочивания
//                        ),
//                    ),
//                ),
//            ),
//        ),
//        "order" => array("field_order1" => "ASC", "field_order2" => "DESC"), // поля упорядочивания в админке
//        "autoreorder" => "true", // автоматическое переупорядочивание при занесении новой записи
//    ),
//);

global $entity;

$entity = array(
    "config" => array(
        "name" => array("Конфигурация"),
        "admin" => array(
            "all" => array(
                "url_template" => "config_all.php",
                "file" => "config_all.php",
                "name" => array("Конфигурация")
            ),
        ),
        "superadmin" => "true",	// признак объекта который доступен только суперадмину

        "tabs" => array( // список закладок для ViewEdit, если список пуст, то закладок не будет
            "main" => array("Основные"), // определение закладки
            "site_is_closed" => array("Сайт закрыт"), // определение закладки
        ),

        "fields" => array(
            "config_id" => array(
                "name" => array("ID", "ID"),
                "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array("1"),
                    "value" => array("1"),
                    "nolang" => "true",
                    "hidden" => "true",
                    //"readonly" => "true",
                    //"editor" => "false", // признак того что для типа text нужен расширенный редактор
                    "length" => "30", // длина поля для полей типа string, number
                ),
            ),
            "config_site_name" => array(
                "name" => array("Название сайта"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array("Тут название сайта"),
                    //"editor" => "false", // признак того что для типа text нужен расширенный редактор
                    "length" => "30", // длина поля для полей типа string, number
                    //"nolang" => "true",
                ),
            ),
            "config_site_keywords" => array(
                "name" => array("Ключевые слова сайта"),
                "help" => array("отображаются на всех страницах"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "text", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array("Ключевые слова"),
                    "cols" => "50", // ширина поля (количество символов) для полей типа text
                    "rows" => "10", // высота поля (количество строк) для полей типа text
                    //"editor" => "false", // признак того что для типа text нужен расширенный редактор
                    //"length" => "30", // длина поля для полей типа string, number
                    //"nolang" => "true",
                ),
            ),
            "config_site_description" => array(
                "name" => array("Описание сайта"),
                "help" => array("отображается на всех страницах"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "text", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array("Описание сайта"),
                    "cols" => "50", // ширина поля (количество символов) для полей типа text
                    "rows" => "10", // высота поля (количество строк) для полей типа text
                    //"nolang" => "true",
                    //"editor" => "false", // признак того что для типа text нужен расширенный редактор
                    //"length" => "30", // длина поля для полей типа string, number
                ),
            ),
            "config_site_emailfrom" => array(
                "name" => array("Email FROM"),
                "help" => array("Email по умолчанию с которого сайт отправляет сообщения<br />
                (например: рассылки и т.п.)"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array(""),
                    "nolang" => "true",
                    "length" => "30", // длина поля для полей типа string, number
                ),
            ),
            "config_site_emailto" => array(
                "name" => array("Email TO"),
                "help" => array("Email по умолчанию на который сайт посылает сервисные сообщения<br />
                (например: форма обратной связи и т.п.)"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "string", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "params" => array( // параметры поля
                    "default" => array(""),
                    "nolang" => "true",
                    "length" => "30" // длина поля для полей типа string, number
                ),
            ),
            "config_site_isclosed" => array(
                "name" => array("Сайт временно не работает"),
                "type" => "checkbox", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "tab" => "site_is_closed", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "params" => array( // параметры поля
                    "default" => array(),
                    "nolang" => "true",
                    //"length" => "30", // длина поля для полей типа string, number
                ),
            ),
            "config_site_isclosed_message" => array(
                "name" => array("Сообщение о том что сайт не работет"),
                "type" => "text", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
                "tab" => "site_is_closed", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "params" => array( // параметры поля
                    "default" => array("Сайт временно не работает"),
                    "editor" => "true", // признак того что для типа text нужен расширенный редактор
                    "cols" => "50", // ширина поля (количество символов) для полей типа text
                    "rows" => "10", // высота поля (количество строк) для полей типа text
                    //"nolang" => "true",
                ),
            ),
            "config_datetime" => array(
                "name" => array("Дата/время изменения"),
                "tab" => "", // мнемокод закладки в которой должно быть отображено поле, пустое (неопределенное) отображается в первой закладке
                "type" => "datetime", // тип поля string, text, number, checkbox, select
                "params" => array( // параметры поля
                    "default" => array(),
                    "value" => array("NOW"),
                    "readonly" => "true", // неизменяемое значение
                    //"editor" => "false", // признак того что для типа text нужен расширенный редактор
                    //"length" => "30", // длина поля для полей типа string, number
                ),
            ),
        ),
        "order" => array() // поля упорядочивания в админке
    ),
);

?>