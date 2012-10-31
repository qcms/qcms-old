<?
/**
 * Класс для работы с базой данных
 */
class CDatabase
{
    /**
     * @var resource соединение с БД
     */
    var $connection;
    /**
     * @var string тип базы данных
     */
    var $type;
    /**
     * @var запрос к БД
     */
    var $query;
    /**
     * @var resource результат
     */
    var $result;
    /**
     * @var строка результата
     */
    var $row;
    /**
     * @var int код ошибки
     */
    var $error_no;
    /**
     * @var string текст ошибки
     */
    var $error;

    /**
     * Конструктор
     */
    function CDatabase()
    {
        global $database_connection;
        $this->type = DATABASE_TYPE;

        if(!isset($database_connection))
        {
            $database_connection = $this->Connect();
        }
        else
        {
            $this->connection = $database_connection;
        }
    }

    /**
     * Функция подключения к БД
     * @return resource
     */
    function Connect()
    {
        if(!$this->connection)
        {
            $this->connection = mysql_connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PW)
                or die("Could not connect to database");
            mysql_select_db(DATABASE_NAME);
            //echo "BBB";
            if(defined("DATABASE_AFTER_CONNECT"))
            {
                //echo "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";
                @mysql_query(DATABASE_AFTER_CONNECT, $this->connection) or die($this->die_mysql_error_show(DATABASE_AFTER_CONNECT));
            }
        }
        return $this->connection;
    }

    /**
     * Функция возвращает следующий результат запроса $result
     * @return null | array
     */
    function NextRow()
    {
        $this->row = null;
        if($this->result && $this->row = mysql_fetch_row($this->result))
        {
            return $this->row;
        }
        return null;
    }

    /**
     * Функция возвращает следующий результат запроса $result
     * @return null | array
     */
    function NextAssoc()
    {
        $this->row = null;
        if($this->result && $this->row = mysql_fetch_assoc($this->result))
        {
            return $this->row;
        }
        return null;
    }

    /**
     * Функция переходит на первую запись результата запроса
     * @return bool
     */
    function First()
    {
        if($this->result)
        {
            return mysql_data_seek ( $this->result, 0 );
        }
        return false;
    }

    /**
     * Функция переходит на заданную запись результата запроса
     * @param int $row_number
     * @return bool
     */
    function Seek($row_number)
    {
        if($this->result)
        {
            return mysql_data_seek ( $this->result, $row_number );
        }
        return false;
    }

    /**
     * Функция выполняет запрос
     * @param $query
     * @param bool $silent
     * @return resource
     */
    function Query($query, $silent = false)
    {
        $this->query = $query;
        if($silent)
        {
            $this->result = @mysql_query($this->query);
            if(!$this->connection || mysql_errno($this->connection))
            {
                $this->error_no = mysql_errno($this->connection);
                $this->error = mysql_error($this->connection);
            }

        }
        else
        {
            $this->result = @mysql_query($this->query) or die($this->die_mysql_error_show($this->query));
        }

        return $this->result;
    }

    /**
     * Функция возвращает количество строк результата запроса
     * @return int|null
     */
    function RowCount()
    {
        if($this->result)
        {
            return mysql_num_rows($this->result);
        }
        return null;
    }

    /**
     * Функция очищает результат запроса
     * @return bool
     */
    function Free()
    {
        if(isset($this->result) && $this->result)
        {
            return @mysql_free_result($this->result);
        }
        return false;
    }

    /**
     * Функция возвращает последний вставленный ID
     * @return bool|int
     */
    function InsertId()
    {
        if($this->result)
        {
            return mysql_insert_id();
        }
        return false;
    }


    /**
     * Функция проверки того что поле $field существует в таблице $table
     * @param string $table
     * @param string $field
     * @return bool
     */
    function FieldExists($table, $field)
    {
        $tableFields = mysql_list_fields(DATABASE_NAME, $table);
        $columns = mysql_num_fields($tableFields);
        for ($i = 0; $i < $columns; $i++)
        {
            if(mysql_field_name($tableFields, $i) == $field)
            {
                return true;
            }
        }
        return false;
    }


    /**
     * Check DB server connection
     * @param $link
     * @param $error
     * @return bool
     */
    function CheckConnection(&$link, &$error)
    {
        $link = false;
        $error = false;
        $link = mysql_connect(DATABASE_SERVER, DATABASE_USER, DATABASE_PW);
        if (!$link)
        {
            $this->error = mysql_error();
            $this->error_no = mysql_errno();
            $error = mysql_error();
            return false;
        }
        return true;
    }

    /**
     * Check database connection (selection of the DB)
     * @param $link
     * @param $db_selected
     * @param $error
     * @return bool
     */
    function CheckConnectionDb(&$link, &$db_selected, &$error)
    {
        $link = false;
        $db_selected = false;
        $error = false;
        if($this->CheckConnection($link, $error))
        {
            $db_selected = mysql_select_db(DATABASE_NAME, $link);
            if (!$db_selected)
            {
                $error = mysql_error();
                $this->error = mysql_error();
                $this->error_no = mysql_errno();
                return false;
            }
            return true;
        }
    }


    /**
     * Backup table from current Database to file
     * @param $filename
     * @param string $tables
     * @return string
     */
    function BackupTables($filename, $tables = '*')
    {
        $return = "";

        global $MAIN;
        if(!$MAIN->is_admin)
            return;

        $host = DATABASE_SERVER;
        $user = DATABASE_USER;
        $pass = DATABASE_PW;
        $name = DATABASE_NAME;

        $output_filename = $MAIN->root."database/{$filename}";

        //save file
        $handle = fopen($output_filename,'w+');

        $link = mysql_connect($host,$user,$pass);
        mysql_select_db($name,$link);

        //get all of the tables
        if($tables == '*')
        {
            $tables = array();
            $result = mysql_query('SHOW TABLES');
            while($row = mysql_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        //cycle through
        foreach($tables as $table)
        {
            $result = mysql_query('SELECT * FROM '.$table);
            $num_fields = mysql_num_fields($result);

            $return = "";
            $return.= "DROP TABLE IF EXISTS `{$table}`;\n\n";
            $row2 = mysql_fetch_row(mysql_query("SHOW CREATE TABLE `{$table}`"));
            $return.= $row2[1].";\n\n";
            fwrite($handle,$return);

            for ($i = 0; $i < $num_fields; $i++)
            {
                while($row = mysql_fetch_row($result))
                {
                    $return = "";
                    $return.= "INSERT INTO `{$table}` VALUES(";
                    for($j=0; $j<$num_fields; $j++)
                    {
                        $field_name  = mysql_field_name($result, $j);
                        $field_type  = mysql_field_type($result, $j);
                        $field_flags = mysql_field_flags($result, $j);
                        //var_dump($field_name);
                        //var_dump($field_type);
                        //var_dump($field_flags);
                        //echo "<br />";

                        if (isset($row[$j]))
                        {

                            if(is_null( $row[$j]))
                            {
                                //echo "NULL <br />";
                                $return .= " NULL";
                            }
                            else
                            {
                                //echo "value: "; var_dump($row[$j]); echo "<br />";
                                if(($field_type == 'int' || $field_type == 'float' || $field_type == 'double' || $field_type == 'datetime')
                                    && !preg_match('/\bnot_null\b/ims', $field_flags) && $row[$j]=='')
                                {

                                    //echo "NULL <br />";
                                    $return .= "NULL";
                                }
                                else
                                {
                                    $return.= "'".mysql_real_escape_string($row[$j])."'" ;
                                }
                            }
                        }
                        else
                        {
                            if(($field_type == 'int' || $field_type == 'float' || $field_type == 'double' || $field_type == 'datetime')
                                && !preg_match('/\bnot_null\b/ims', $field_flags) && $row[$j]=='')
                            {

                                //echo "NULL <br />";
                                $return .= "NULL";
                            }
                            else
                            {
                                $return.= "''";
                            }
                        }
                        /*
                          $row[$j] = addslashes($row[$j]);
                          $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                          if (isset($row[$j])) { $return.= "'".$row[$j]."'" ; }
                          else { $return.= "''"; }
                          */
                        if ($j<($num_fields-1)) { $return.= ','; }
                    }
                    $return.= ");\n\n";

                    fwrite($handle,$return);
                }
            }
            $return = "";
            $return.="\n\n\n";
            fwrite($handle,$return);
        }

        fclose($handle);
        $return = $output_filename;
        //exit;
        return $return;
    }

    /**
     * Функция показа ошибки при вызове die
     * в зависимости от текущих параметров отображения
     * @param null $query
     * @return string
     */
    function die_mysql_error_show($query=null)
    {

        $message = "Ошибка в SQL запросе!<br />
";

        $ret = "";
        if(ERROR_SHOW <= 0)
            return $ret;

        $ret .= $message . "<br />
";
        if(ERROR_SHOW <= 1)
            return;

        if(ERROR_SHOW <= 2)
            return $ret;

        $ret .= mysql_errno() . ": " . mysql_error(). "<br />
";

        if(ERROR_SHOW <= 3)
            return $ret;

        if($query)
        {
            $ret .= "QUERY: ". $query . "<br />
";
        }

        if(ERROR_SHOW <= 4)
            return $ret;

        if(ERROR_SHOW == 9)
        {
            //error_log($ret);

            $ret .= "<br />
BACKTRACE: " . print_r(debug_backtrace(), true);
        }

        return $ret;

    }


    /**
     * Функция возвращает условие LIKE для языкового значение поля типа checkbox
     * @param integer $value
     * @return string
     */
    function GetCheckboxLikeConditionCurrentLang($value)
    {
        $ret = "LIKE '". CMain::GetStringFromArrayLang(CMain::SetCurrentArrayLangRet(CMain::NewArrayLang("%"), ($value=="1"?"1":"0"), true)) . "'";
        return $ret;
    }



}

?>