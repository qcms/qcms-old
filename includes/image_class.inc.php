<?
/**
 * класс изображений
 */
class ImageClass
{
    /**
     * id изображения в БД
     * @var int|null
     */
    var $image_id;
    /**
     * массив сообщений об ошибках
     * @var
     */
    var $errors;
    /**
     * ContentType для изображения
     * @var null|string
     */
    var $image_type;
    /**
     * название связанной таблицы
     * @var string
     */
    var $image_table;
    /**
     * идентификатор в связанной таблице
     * @var string
     */
    var $image_table_id;
    /**
     * поле в связанной таблице
     * @var string
     */
    var $image_table_field;
    /**
     * каталог в котором храняться картинки
     * @var string
     */
    var $image_dir;
    /**
     * содержимое картинки
     * @var string
     */
    var $image_content;
    /**
     * исходное имя файла изображения
     * @var string
     */
    var $image_file;
    /**
     * resource изображения
     * @var resource
     */
    var $resource;

    /**
     * Возвращает строковое представление для изображения image_id
     * @return string
     */
    function __toString()
    {
        if($this->image_id)
            return "".$this->image_id;

        return "";
    }

    /**
     * конструктор
     * @param int $image_id
     */
    function ImageClass($image_id = 0)
    {
        $sql = "
SELECT *
FROM ".DATABASE_PREFIX."images
WHERE images_id = '" . (integer)$image_id . "'
";

        $db = new CDatabase();

        $result = $db->Query($sql);
        $this->image_dir = IMAGE_DIR;
        if( $result && $row = $db->NextAssoc() )
        {
            $this->image_id = (integer)$image_id;
            $this->image_type = $row["images_contenttype"];

            if( strtolower($this->image_type) == "image/pjpeg" )
            {
                $this->image_type = "image/jpeg";
            }
            if( strtolower($this->image_type) == "image/x-png" )
            {
                $this->image_type = "image/png";
            }
            $this->image_file = $row["images_filename"];
            $this->image_table = $row["images_table"];
            $this->image_table_id = $row["images_table_id"];
            $db->Free();
        }
        else
        {
            $this->image_id = NULL;
            $this->image_type = NULL;
        }
    }

    /**
     * @return resource | boolean
     */
    function GetImageResource()
    {
        $ret = false;
        if($this->image_id)
        {
            if($this->resource)
            {
                $ret = $this->resource;
            }
            else
            {
                $filename = $this->image_filename();
                switch($this->image_ext($this->image_type))
                {
                    case "jpg":
                        $ret = imagecreatefromjpeg($filename);
                        break;
                    case "gif":
                        $ret = imagecreatefromgif($filename);
                        break;
                    case "png":
                        $ret = imagecreatefrompng($filename);
                        break;
                }
            }
        }
        $this->resource = $ret;
        return $ret;
    }

    /**
     * Функция возвращает размер изображения по X
     * @return number
     */
    function GetSizeX()
    {
        $ret = 0;
        $res = $this->GetImageResource();
        if($res)
            $ret = imagesx($res);
        unset($res);
        return $ret;
    }

    /**
     * Функция возвращает размер изображения по Y
     * @return number
     */
    function GetSizeY()
    {
        $ret = 0;
        $res = $this->GetImageResource();
        if($res)
            $ret = imagesy($res);
        unset($res);
        return $ret;
    }

    /**
     * Функция возвращает контент изображения из файла
     */
    function get_content()
    {
        $filename = $this->image_filename();
        $file = fopen($filename, "r") or die("Cannot open file for reading: " . $filename );
        $this->image_content = fread($file, filesize($filename)) or die("Cannot read file: " . $filename );
        fclose($file) or die("Cannot close file: " . $filename );
    }

    /**
     * Функция пишет контент изображения в файл
     */
    function set_content()
    {
        // запишем картинку на диск
        $filename = $this->image_filename();
        //var_dump($filename);
        $file = fopen($filename, "w") or die("Cannot open file for writing: " . $filename );
        fwrite($file, $this->image_content) or die("Cannot write file: " . $filename );
        fclose($file) or die("Cannot close file: " . $filename );
    }

    /**
     * Функция возвращает расширение для заданного типа картинок
     * @param $contenttype
     * @return null|string
     */
    function image_ext($contenttype)
    {
        if(preg_match("/(jpg|jpeg)/i", $contenttype))
        {
            return "jpg";
        }
        else if(preg_match("/(gif)/i", $contenttype))
        {
            return "gif";
        }
        else if(preg_match("/(png)/i", $contenttype))
        {
            return "png";
        }
        return NULL;
    }

    /**
     * Функция возвращает имя файла картинки
     * @return null|string
     */
    function image_filename()
    {
        //var_dump($this);
        //var_dump($this->image_ext($this->image_type));
        global $root;
        if(!$this->image_id
            || !$this->image_type
            || !$this->image_ext($this->image_type)
            || !$this->image_dir)
            return NULL;

        return (isset($root)?$root:"") . $this->image_dir . "/" . IMAGE_FILE_PREFIX . $this->image_id . "." . $this->image_ext($this->image_type);
    }

    /**
     * Функция вставки изображения в БД, сохраняет файл в каталоге для изображений
     * @param $tmp_name
     * @param $contenttype
     * @param $filename
     * @param string $table
     * @param int $table_id
     */
    function insert_image($tmp_name, $contenttype, $filename, $table = "", $table_id = 0)
    {
        //global $connection;
        //global $languages;

        $this->image_type = $contenttype;
        $this->image_file = $filename;
        $this->image_table = $table;
        $this->image_table_id = $table_id;
        //$this->image_content = $content;

        if( is_null($this->image_id) )
        {
            $query = "INSERT INTO ".DATABASE_PREFIX."images(images_contenttype,  images_filename, images_table, images_table_id) VALUES('" . $this->image_type . "', '" . $this->image_file . "', '" . $this->image_table . "', '" . $this->image_table_id . "')";
            //var_dump($query);
            //exit;

            //$query = "INSERT INTO images(images_contenttype, images_content, images_filename, images_table, images_table_id) VALUES('" . $contenttype . "', '" . base64_encode($content) . "', '" . $filename . "', '" . $table . "', '" . $table_id . "')";
            $db = new CDatabase();

            $db->Query($query);
            //mysql_query($query)  or die(die_mysql_error_show($query));

            // идентификатор картинки
            $this->image_id = $db->InsertId();
            // запишем картинку на диск
            move_uploaded_file($tmp_name, $this->image_filename());
            //$this->set_content();
        }
        else
        {

            $query = "SELECT images_contenttype
FROM ".DATABASE_PREFIX."images
WHERE images_id = '" . $this->image_id . "'
";

            //$result = mysql_query($query) or die(die_mysql_error_show($query));
            $db = new CDatabase();
            $result = $db->Query($query);

            if( $result && $row = $db->NextAssoc() )
            {
                $db->Free();

                $sql = "
UPDATE ".DATABASE_PREFIX."images
SET
images_contenttype='" . $this->image_type . "',
images_filename='" . $this->image_file . "',
images_table='" . $this->image_table . "',
images_table_id='" . $table_id . "'
WHERE
images_id = '" . $this->image_id . "'";

                $db->Query($sql);
                //mysql_query($sql) or die(die_mysql_error_show($sql));

                // запишем картинку на диск
                move_uploaded_file($tmp_name, $this->image_filename());
                //$this->set_content();
            }
        }
    }

    /**
     * Функция возвращает размер файла изображения
     * @return int
     */
    function image_filesize()
    {
        return filesize($this->image_filename());
    }

    /**
     * Функция просмотра изображения
     */
    function view_image()
    {
        //global $connection;
        global $_SESSION;
        /*
        HTTP/1.1 206 Partial Content
        X-Powered-By: PHP/5.2.2
        Accept-Ranges: bytes
        Content-Length: 12345
        Content-Range: bytes 0-12344
        Content-Type: application/pdf
        Content-Disposition: inline; filename="document.pdf"
        */
        //var_dump($this->image_type);
        //exit;

        header ("HTTP/1.0 200 OK", true);
        header ( "Content-type: " . $this->image_type, true);
        header ( 'Content-Disposition: inline; filename="' . $this->image_file  . '"', true);
        header ( 'Accept-Ranges: bytes', true);
        header ( 'Content-Length: ' . $this->image_filesize(), true);


        echo $this->get_image();
    }

    /**
     * Функция возвращает изображение из БД
     * @return null
     */
    function get_image()
    {
        //global $connection;
        global $_SESSION;
        $sql = "
SELECT images_contenttype
FROM ".DATABASE_PREFIX."images
WHERE images_id = '" . $this->image_id . "'
";
        $result = mysql_query($sql) or die(die_mysql_error_show($sql));
        if( $result && $row = mysql_fetch_assoc($result) )
        {
            $this->get_content();
            $ret = $this->image_content;
            mysql_free_result($result);
            return $ret;
        }
        else
        {
            return null;
        }
    }

    /**
     * Функция удаления картинки из БД
     * @return mixed
     */
    function delete_image()
    {
        if(!$this->image_id){
            return;
        }

        @unlink($this->image_filename());

        $sql = "
DELETE FROM ".DATABASE_PREFIX."images
WHERE images_id = '" . $this->image_id . "'
";
        $result = mysql_query($sql)  or die(die_mysql_error_show($sql));

    }

    /**
     * Функция выводит в стандартный поток картинку для ошибки
     */
    static function ErrorImage()
    {
        header ("Content-type: image/gif");
        $fp = fopen (VN_ERROR_IMAGE, "rb");
        fpassthru($fp);
        fclose ($fp);
    }

    /**
     * Функция выводит в стандартный поток пустую картинку
     */
    static function EmptyImage()
    {
        header ("Content-type: image/gif");
        $fp = fopen (VN_PIXEL, "rb");
        fpassthru($fp);
        fclose ($fp);
    }
}

?>