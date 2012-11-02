<?
//=============================================================================
// Определения классов и функций работы с файлами

//-----------------------------------------------------------------------------
// Функция выводи в стандартный поток картинку для ошибки
function ErrorFile()
{
    header("Content-Type: application/octet-stream");
    $fp = fopen (VN_FILE_SOUND, "rb");
    fpassthru($fp);
    fclose ($fp);
}

//-----------------------------------------------------------------------------
// класс файла файла
class FileFileClass
{
    var $filename;
    var $file_dir;

    function FileFileClass($filename)
    {
        $this->file_dir = FILE_DIR;
        $this->filename = $filename;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает имя файла
    function file_filename()
    {
        //var_dump($this);
        //var_dump($this->video_ext($this->video_type));
        global $root;
        if(!$this->filename
            || !$this->file_dir)
            return NULL;

        return (isset($root)?$root:"") . $this->file_dir . "/" .  $this->filename;
    }


    //---------------------------------------------------------------------------
    // Функция просмотра файла
    function view_file($start=NULL)
    {
        /*
        HTTP/1.1 206 Partial Content
        X-Powered-By: PHP/5.2.2
        Accept-Ranges: bytes
        Content-Length: 12345
        Content-Range: bytes 0-12344
        Content-Type: application/pdf
        Content-Disposition: inline; filename="document.pdf"
        */

        $size = filesize($this->file_filename());

        /*
            header ("HTTP/1.1 200 OK", true);
            header ("Content-Type: text/plain");

            var_dump($this->video_filename());
            var_dump(filesize($this->video_filename()));
            var_dump($size);
            exit;
        */
        header ("HTTP/1.1 200 OK", true);
        header ("Accept-Ranges: bytes");
        header ("Content-Length: " . $size);
        header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
        header ("Content-Type: application/octet-stream");
        header ('Content-Disposition: inline; filename="' . basename($this->filename)  . '"');


        if(ERROR_SHOW == 9)
        {
            //error_log("file_class.inc.php:view_sound():size: " . $size);
            //error_log("file_class.inc.php:view_sound():file: " . $this->file_filename());
        }

        //http_send_file($this->video_filename());

        //echo "\n\n";
        $handle = fopen($this->file_filename(), "rb");
        if($start > 0)
        {
            fseek($handle, $start);
        }
        echo fread($handle, $size);
        //fpassthru($handle);
        fclose($handle);

        exit;
        //echo $this->get_video();
    }
}


//-----------------------------------------------------------------------------
// класс Файла
class FileClass
{
    //===========================================================================
    // данные члены
    var $file_id;				  // id в БД
    var $file_datetime;   // дата/время последнего изменения видео
    var $errors;					// массив сообщений об ошибках
    var $file_type;			  // ContentType для файла
    var $file_table;			// название связанной таблицы
    var $file_table_id;	  // идентификатор в связанной таблице
    var $file_table_file_field;// поле в таблице которое привязано к файлу (нужно очищать когда файл удаляют)
    var $file_dir; 			  // каталог в котором храняться картинки
    var $file_content; 	  // содержимое файла
    var $file_length; 	  // длинна видео в секундах
    var $file_file; 			// исходное имя файла
    var $file_thumbnail;  // превью картинка для файла




    //===========================================================================
    // функции члены
    function __toString()
    {
        if($this->file_id)
            return "".$this->file_id;

        return "";
    }

    //---------------------------------------------------------------------------
    // конструктор
    function FileClass($file_id = 0)
    {
        $sql = "
SELECT *
FROM ".DATABASE_PREFIX."files
WHERE files_id = '" . (integer)$file_id . "'
";

        $db = new CDatabase();

        $result = $db->Query($sql);//  or die(die_mysql_error_show($sql));
        $this->file_dir = FILE_DIR;
        if( $result && $row = $db->NextAssoc() )
        {
            $this->file_id = (integer)$file_id;
            $this->file_type = $row["files_contenttype"];
            $this->file_table = $row["files_table"];
            $this->file_table_id = $row["files_table_id"];
            $this->file_table_file_field = $row["files_table_file_field"];
            $this->file_file = $row["files_file"];
            $db->Free();
        }
        else
        {
            $this->file_id = NULL;
            $this->file_type = NULL;
        }
    }

    function get_file_length()
    {
        $filename = $this->file_filename();
        return filesize($filename);
    }


    //---------------------------------------------------------------------------
    // Функция возвращает контент изображения из файла
    function get_content($start=NULL)
    {
        $filename = $this->file_filename();

        if(ERROR_SHOW == 9)
        {
            //error_log("file_class.inc.php:get_content:" . print_r($filename, true));
        }

        $this->file_content = "";

        /*
        if($start && $start != 0)
        {
                $this->sound_content .= "FLV";
                $this->sound_content .= pack('C', 1 );
                $this->sound_content .= pack('C', 1 );
                $this->sound_content .= pack('N', 9 );
                $this->sound_content .= pack('N', 9 );
        }
        else*/
        {
            $start = 0;
        }
        $fh = fopen($filename, "rb") or die("Cannot open file for reading: " . $filename );
        fseek($fh, $start);
        while (!feof($fh))
        {
            $this->file_content .= fread($fh, 1024) or die("Cannot read file: " . $filename );
        }
        fclose($fh) or die("Cannot close file: " . $filename );

        if(ERROR_SHOW == 9)
        {
            //error_log("file_class.inc.php:get_content:file is get" . print_r($filename, true));
        }
        /*
              $file = fopen($filename, "r") or die("Cannot open file for reading: " . $filename );
              $this->sound_content = fread($file, filesize($filename)) or die("Cannot read file: " . $filename );
              fclose($file) or die("Cannot close file: " . $filename );
        */
    }

    //---------------------------------------------------------------------------
    // Функция пишет контент изображения в файл
    function set_content()
    {
        // запишем картинку на диск
        $filename = $this->sound_filename();
        //var_dump($filename);
        $file = fopen($filename, "w") or die("Cannot open file for writing: " . $filename );
        fwrite($file, $this->file_content) or die("Cannot write file: " . $filename );
        fclose($file) or die("Cannot close file: " . $filename );
    }


    //---------------------------------------------------------------------------
    // Функция возвращает расширение для заданного типа
    function file_ext($contenttype=null)
    {
        if(!$contenttype)
            $contenttype = $this->file_type;
        if(preg_match("/(flv|x-flv)/i", $contenttype))
        {
            return "flv";
        }
        else if(preg_match("/(pdf|x-pdf)/i", $contenttype))
        {
            return "pdf";
        }
        return NULL;
    }


    //---------------------------------------------------------------------------
    // Функция возвращает имя файла
    function file_filename()
    {
        //var_dump($this);
        //var_dump($this->sound_ext($this->sound_type));
        global $root;
        if(!$this->file_id
            || !$this->file_type
            //|| !$this->file_ext($this->file_type)
            || !$this->file_dir)
            return NULL;

        return (isset($root)?$root:"") . $this->file_dir . "/" . FILE_FILE_PREFIX . $this->file_id . "." . $this->file_ext($this->file_type);
    }

    //---------------------------------------------------------------------------
    // Функция вставки изображения в БД
    function insert_file($tmp_filename, $contenttype, $filename, $table = "", $table_id = 0, $table_file_field="")
    {
        global $connection;
        global $languages;

        $this->file_type = $contenttype;
        $this->file_file = $filename;
        $this->file_table = $table;
        $this->file_table_id = $table_id;
        $this->file_table_file_field = $table_file_field;
        //$this->sound_content = $content;

        if( is_null($this->file_id) )
        {
            //error_log("111111111111");
            $query = "
INSERT INTO
  ".DATABASE_PREFIX."files
  	(files_datetime,
    files_contenttype,
    files_filename,
    files_table,
    files_table_id,
    files_table_file_field)
  VALUES('" . date("Y-m-d H:i:s") . "',
    '" . $this->file_type . "',
    '" . $this->file_file . "',
    '" . $this->file_table . "',
    '" . $this->file_table_id . "',
    '" . $this->file_table_file_field . "')";
            //$query = "INSERT INTO ".DATABASE_PREFIX."sounds(sounds_contenttype, sounds_content, sounds_filename, sounds_table, sounds_table_id) VALUES('" . $contenttype . "', '" . base64_encode($content) . "', '" . $filename . "', '" . $table . "', '" . $table_id . "')";
            $db = new CDatabase();
            $db->Query($query);

            // идентификатор картинки
            $this->file_id = $db->InsertId();

            // запишем картинку на диск
            move_uploaded_file($tmp_filename, $this->file_filename());

            chmod($this->file_filename(), 0755);
            // получим контент из файла
            //$this->get_content();

            $db->Free();
        }
        else
        {
            //error_log("2222222222");
            $query = "SELECT files_id
FROM ".DATABASE_PREFIX."files
WHERE files_id = '" . $this->file_id . "'
";
            $db = new CDatabase();
            $result = $db->Query($query);
            if( $result && $row = $db->NextAssoc() )
            {
                $sql = "
UPDATE ".DATABASE_PREFIX."files
SET
  files_datetime='" . date("Y-m-d H:i:s") . "',
  files_contenttype='" . $this->file_type . "',
  files_filename='" . $this->file_file . "',
  files_table='" . $this->file_table . "',
  files_table_id='" . $table_id . "'
WHERE
  files_id = '" . $this->file_id . "'";

                $db->Free();

                $db->Query($sql);// or die(die_mysql_error_show($sql));


                // запишем картинку на диск
                move_uploaded_file($tmp_filename, $this->file_filename());

                chmod($this->file_filename(), 0755);
                //$this->set_content();
                $db->Free();
            }
        }
    }

    //---------------------------------------------------------------------------
    // Функция просмотра файла
    function view_file($start=NULL, $attach=false)
    {
        /*
        HTTP/1.1 206 Partial Content
        X-Powered-By: PHP/5.2.2
        Accept-Ranges: bytes
        Content-Length: 12345
        Content-Range: bytes 0-12344
        Content-Type: application/pdf
        Content-Disposition: inline; filename="document.pdf"
        */

        $size = filesize($this->file_filename());

        header ("HTTP/1.1 200 OK", true);
        header ("Accept-Ranges: bytes");
        header ("Content-Length: " . $size);
        header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
        header ("Content-Type: " . $this->file_type);
        if($attach)
        {
            header('Content-Disposition: attachment; filename="'.basename($this->file_filename()).'"');
        }
        else
        {
            //header ('Content-Disposition: inline; filename="' . $this->file_id .'"' );
            header ('Content-Disposition: inline; filename="'.basename($this->file_filename()).'"' );
        }

        if(ERROR_SHOW == 9)
        {
            //error_log("file_class.inc.php:view_sound():size: " . $size);
            //error_log("file_class.inc.php:view_sound():file: " . $this->file_filename());
        }

        //http_send_file($this->sound_filename());


        //var_dump($this);
        //echo "\n\n";
        $handle = fopen($this->file_filename(), "rb");
        if($start > 0)
        {
            fseek($handle, $start);
        }
        echo fread($handle, $size);
        //fpassthru($handle);
        fclose($handle);

        exit;
        //echo $this->get_sound();
    }

    //---------------------------------------------------------------------------
    // Функция возвращает файл из БД
    function get_file($start=NULL)
    {
        global $connection;
        global $_SESSION;
        $sql = "
SELECT files_contenttype
FROM ".DATABASE_PREFIX."files
WHERE files_id = '" . $this->file_id . "'
";
        $db = new CDatabase();
        $result = $db->Query($sql);
        //$result = mysql_query($sql) or die(die_mysql_error_show($sql));
        if( $result && $row = $db->NextAssoc() )
        {
            $db->Free();

            $this->get_content($start);

            //      if(ERROR_SHOW == 9)
            //      {
            //        error_log("sound_class.inc.php:get_sound:" . print_r($this, true));
            //      }

            return $this->file_content;
            //return $ret;
        }
        else
        {
            return null;
        }
    }

    //---------------------------------------------------------------------------
    // Функция удаления файла из БД
    function delete_file()
    {
        if(!$this->file_id){
            return;
        }

        // удалим файл
        unlink($this->file_filename()) or die("Error deleting file: " . $this->file_filename());

        $db = new CDatabase();
        // изменим ссылку на файл в родительском объекте
        if($this->file_table && $this->file_table_id && $this->file_table_file_field)
        {
            $query = "UPDATE `".DATABASE_PREFIX."{$this->file_table}`
SET `{$this->file_table_file_field}` = NULL 
WHERE `{$this->file_table}_id` = '{$this->file_table_id}'";
            $result = $db->Query($query);
        }

        // удалим ссылку из бд
        $query = "
DELETE FROM ".DATABASE_PREFIX."files
WHERE files_id = '" . $this->file_id . "'
";
        $result = $db->Query($query);



        //var_dump($sql);
        //$result = mysql_query($sql)  or die(die_mysql_error_show($sql));

    }


    //---------------------------------------------------------------------------
    // функция создания таблицы для класса
    function ClassCreateTable()
    {
        $db = new CDatabase();
        $query = "
DROP TABLE IF EXISTS `".DATABASE_PREFIX."files`;
";

        $result = $db->Query($query);
        //mysql_query($query) or die(die_mysql_error_show($query));

        $query = "
CREATE TABLE  `".DATABASE_PREFIX."files` (
  `files_id` int(10) unsigned NOT NULL auto_increment,
  `files_contenttype` varchar(45) NOT NULL,
  `files_table` varchar(45) NOT NULL,
  `files_table_id` int(10) unsigned default NULL,
  `files_dir` varchar(100) default NULL,
  `files_filename` varchar(200) default NULL,
  `files_datetime` datetime NOT NULL,
  `files_lenght` int(10) unsigned default NULL,
  PRIMARY KEY  (`files_id`)
) ENGINE=MyISAM;
";
        $result = $db->Query($query);
        //mysql_query($query) or die(die_mysql_error_show($query));
        $db->Free();
    }

}

?>
