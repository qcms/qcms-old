<?php
//-----------------------------------------------------------------------------
// Инсталятор базы данны
//-----------------------------------------------------------------------------
// по структуре описанной в файле /includes/configation.entity.inc.php
// создает таблицы и поля базы данных
//-----------------------------------------------------------------------------
$root = "../";

// необходимые инклуды
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

global $MAIN;

$MAIN = new CMain($root,array(
    "need_session"=>true,
    "need_session_db"=>false,
    "need_xajax"=>false,
    "need_config"=>false,
    "need_database"=>true,
    "output_buffering"=>false,
    "session_last_query"=>false,
    //"is_admin" => true,
));

$MAIN->HeaderIncludes();
$MAIN->Init();

global $database_init;
global $database_init_query;
$MAIN->IncludeSiteConfigFile($_SERVER["SERVER_NAME"], "configuration.database.init.inc.php");


//var_dump($db_forms);


$db_required_fields = array(
    "id" => "int(10) unsigned NOT NULL auto_increment",
    "order" => "int(10) unsigned default NULL",
);

$show_comments = (isset($_GET["show_comments"])&&$_GET["show_comments"]=="1")?true:false;

if(isset($_POST["do"]) && $_POST["do"]=="1")
{
    // тип БД
    if(!defined("DATABASE_TYPE"))
        define("DATABASE_TYPE", "mysql");
    // сервер БД
    if(!defined("DATABASE_SERVER"))
        define("DATABASE_SERVER", $_POST["db_host"]);
    // имя БД
    if(!defined("DATABASE_NAME"))
        define("DATABASE_NAME", $_POST["db_name"]);
    // user
    if(!defined("DATABASE_USER"))
        define("DATABASE_USER", $_POST["db_user"]);
    // password
    if(!defined("DATABASE_PW"))
        define("DATABASE_PW", $_POST["db_password"]);
    // префикс таблиц в БД
    if(!defined("DATABASE_PREFIX"))
        define("DATABASE_PREFIX", "");

    if(!defined("DATABASE_CHARSET"))
        define("DATABASE_CHARSET", "utf8 collate utf8_general_ci");
    if(!defined("DATABASE_AFTER_CONNECT"))
        define("DATABASE_AFTER_CONNECT", "SET NAMES " . DATABASE_CHARSET);

    $DB = new CDatabase();

    if($MAIN->CheckPostValue("create_database", "1"))
    {
        $query = "CREATE SHEMA `".DATABASE_NAME."`";
        if($MAIN->CheckPostValue("show_sql", "1"))
        {
            echo $query . ";\n";
        }

        if(!$MAIN->CheckPostValue("notexecute_sql", "1"))
        {
            $DB->Query($query);
        }
    }

    echo "<pre>";

    if(isset($db_forms) && is_array($db_forms))
    {
        // Обработка объектов базы данных!
        if($show_comments)
            echo "Обработка объектов базы данных!
		  	
";

        foreach ($db_forms as $table=>$arr)
        {
            // у таблицы есть поля (fields)
            if(isset($arr["fields"]) && is_array($arr["fields"]))
            {
                $fields = $arr["fields"];
                if(!count($fields))
                    continue;

                if(isset($_POST["delete_tables"]) && $_POST["delete_tables"] == "1")
                {
                    // удаляем таблицу
                    $query = "DROP TABLE IF EXISTS `{$table}`";
                    if($MAIN->CheckPostValue("show_sql", "1"))
                    {
                        echo $query . ";\n\n";
                    }
                    if(!$MAIN->CheckPostValue("notexecute_sql", "1"))
                    {
                        $DB->Query($query);
                    }
                }


                if(isset($_POST["create_tables"]) && $_POST["create_tables"] == "1")
                {
                    // создаем таблицу
                    $query = "CREATE TABLE  `{$table}` (";
                    $added_fields = array();

                    foreach ($db_required_fields as $postfix => $params)
                    {
                        $query .= "  `{$table}_{$postfix}` {$params},
";
                        $added_fields[] = "{$table}_{$postfix}";
                    }

                    foreach ($fields as $field => $field_params)
                    {
                        if(in_array($field, $added_fields))
                            continue;

                        $type = "";

                        if(isset($field_params["params"]["dbtype"]) && $field_params["params"]["dbtype"])
                        {
                            $type = $field_params["params"]["dbtype"];
                        }
                        else
                        {
                            switch ($field_params["type"]) // тип поля string, password, text,
                                // number, datetime, checkbox,
                                // select, image, sound, video, file
                                // varchar
                            {
                                case "string":
                                    $type = "text";
                                    break;
                                case "text":
                                    $type = "longtext";
                                    break;
                                case "datetime":
                                    $type = "datetime";
                                    break;
                                case "number":
                                    if(isset($field_params["params"]["nolang"]) && $field_params["params"]["nolang"] == "true")
                                    {
                                        $type = "float default NULL";
                                    }
                                    else
                                    {
                                        $type = "text";
                                    }
                                    break;
                                case "checkbox":
                                    if(isset($field_params["params"]["nolang"]) && $field_params["params"]["nolang"] == "true")
                                    {
                                        $type = "int(11) default '0'";
                                    }
                                    else
                                    {
                                        $type = "varchar(200) default '0'";
                                    }
                                    break;
                                case "select":
                                case "image":
                                case "sound":
                                case "video":
                                case "file":
                                    if(isset($field_params["params"]["nolang"]) && $field_params["params"]["nolang"] == "true")
                                        $type = "int(11) default NULL";
                                    else
                                        $type = "varchar(200) default NULL";
                                    break;
                                default:
                                    $type = "text";
                                    break;
                            }
                        }


                        $query .= "`{$field}` {$type},
";
                        if(isset($field_params["params"]["unique"]) && $field_params["params"]["unique"] == "true")
                        {
                            //"unique" => "true", // признак уникальности поля в таблице
                            // UNIQUE [INDEX] [index_name] (index_col_name,...)
                            $query .= " UNIQUE `idx_{$field}` (`{$field}`),
";
                        }

                    }

                    $query .= "  PRIMARY KEY  (`{$table}_id`)
)";
                    if(defined("DATABASE_CHARSET"))
                    {
                        $query .= " DEFAULT CHARSET " .DATABASE_CHARSET;
                    }

                    //				echo $query."
                    //";


                    if($MAIN->CheckPostValue("show_sql", "1"))
                    {
                        echo $query . ";\n\n";
                    }
                    if(!$MAIN->CheckPostValue("notexecute_sql", "1"))
                    {
                        $DB->Query($query);
                    }
                    //$DB->Query($query);
                    if($show_comments)
                        echo "создана таблица {$table}
							 
";
                }
            }
        }

        if(isset($_POST["insert_data"]) && $_POST["insert_data"] == "1")
        {
            if($show_comments)
                echo "Инициализация значений базы данных!
  				
";
            //if(file_exists($root . "includes/configuration.database.init.inc.php"))
            {
                //include_once($root . "includes/configuration.database.init.inc.php");
                //global $database_init;
                if(isset($database_init) && is_array($database_init))
                {
                    foreach ($database_init as $table=>$rows)
                    {
                        //var_dump($rows);
                        foreach ($rows as $row)
                        {
                            $query = "INSERT INTO `{$table}` (";
                            $query_values = " VALUES (";

                            foreach ($row as $field => $value)
                            {
                                $query .= "`{$field}`, ";
                                $query_values .= "'{$value}', ";
                            }
                            $query = substr($query, 0, -2) . ")";
                            $query_values = substr($query_values, 0, -2) . ")";

                            $query .= $query_values;

                            //echo $query . "<br /><br />";
                            //$DB->Query($query);

                            if($MAIN->CheckPostValue("show_sql", "1"))
                            {
                                echo $query . ";\n\n";
                            }
                            if(!$MAIN->CheckPostValue("notexecute_sql", "1"))
                            {
                                $DB->Query($query);
                            }

                        }
                        if($show_comments)
                            echo "В таблицу `{$table}` добавлено ".count($rows)." записей.
		  					
";
                    }
                }
            }

            if($show_comments)
                echo "Инициализация значений базы данных завершена!

";
        }

        if(isset($_POST["install_special_tables"]) && $_POST["install_special_tables"] == "1")
        {
            if(isset($database_init_query) && $database_init_query)
            {
                $query_arr = preg_split("/\\;\\r?\\n/", $database_init_query);
                //print_r($query_arr);
                foreach ($query_arr as $query)
                {
                    if(trim($query))
                    {
                        //$DB->Query($query);

                        if($MAIN->CheckPostValue("show_sql", "1"))
                        {
                            echo $query . ";\n\n";
                        }
                        if(!$MAIN->CheckPostValue("notexecute_sql", "1"))
                        {
                            $DB->Query($query);
                        }
                    }
                }
                if($show_comments)
                    echo "Инициализация специальных таблиц выполнена!


";
            }
        }

        if($show_comments)
            echo "Обработка объектов базы данных завершена!
";
    }
    echo "</pre>";
}
else
{
    ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Install db!</title>
    <style>
        LABEL{ width: 190px; float:left; }
    </style>
</head>

<body>
<form action="<?=$MAIN->QueryStringWithoutParams() ?>" method="post">
    <input name="do" type="hidden" value="1" />
    <fieldset>
        <label>DB host:</label>
        <input name="db_host" type="text" value="" />
    </fieldset>
    <fieldset>
        <label>DB name:</label>
        <input name="db_name" type="text" value="" />
    </fieldset>
    <fieldset>
        <label>DB user:</label>
        <input name="db_user" type="text" value="" />
    </fieldset>
    <fieldset>
        <label>DB password:</label>
        <input name="db_password" type="text" value="" />
    </fieldset>

    <fieldset>
        <label>Создавать БД:</label>
        <input name="create_database" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Удалять таблицы:</label>
        <input name="delete_tables" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Создавать таблицы:</label>
        <input name="create_tables" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Инициализация специальных таблиц:</label>
        <input name="install_special_tables" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Инициализация значений:</label>
        <input name="insert_data" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Показать запрос SQL:</label>
        <input name="show_sql" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Выводить комментарии:</label>
        <input name="show_comments" type="checkbox" value="1" />
    </fieldset>
    <fieldset>
        <label>Не выполнять SQL:</label>
        <input name="notexecute_sql" type="checkbox" value="1" />
    </fieldset>

    <fieldset>
        <label>&nbsp;</label>
        <input type="reset" value="Очистить" />
        <input type="submit" value="Запустить" />
    </fieldset>
</form>

</body>

</html>
<?
}
?>