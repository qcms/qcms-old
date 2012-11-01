<?
//=============================================================================
// Определения классов и функций работы с изображениями

//-----------------------------------------------------------------------------
// Функция выводи в стандартный поток картинку для ошибки
function ErrorVideo()
{
  header("Content-Type: video/x-flv");
  $fp = fopen (VN_ERROR_VIDEO, "rb");
  fpassthru($fp);
  fclose ($fp);
}

//-----------------------------------------------------------------------------
// класс видеофайла
class VideoFileClass
{
  var $filename;
  var $video_dir;

  function VideoFileClass($filename)
  {
		$this->video_dir = VIDEO_DIR;
    $this->filename = $filename;
  }


	//---------------------------------------------------------------------------
	// Функция возвращает имя файла картинки
	function video_filename()
	{
	  //var_dump($this);
	//var_dump($this->video_ext($this->video_type));
		global $root;
		if(!$this->filename
		|| !$this->video_dir)
			return NULL;

		return (isset($root)?$root:"") . $this->video_dir . "/" .  $this->filename;
	}


  //---------------------------------------------------------------------------
  // Функция просмотра изображения
  function view_video($start=NULL)
  {
/*
HTTP/1.1 206 Partial Content
Status: 206 Partial Content
X-Powered-By: PHP/5.2.2
Accept-Ranges: bytes
Content-Length: 12345
Content-Range: bytes 0-12344
Content-Type: application/pdf
Content-Disposition: inline; filename="document.pdf"
*/


/*
    header ("HTTP/1.1 200 OK", true);
    header ("Status: 200 OK");
    header ("Content-Type: text/plain");

    var_dump($this->video_filename());
    var_dump(filesize($this->video_filename()));
    var_dump($size);
    exit;
*/
    //ob_end_clean();

    ob_start();

    $size = filesize($this->video_filename());

    header ("HTTP/1.1 200 OK", true);
    header ("Status: 200 OK");
    header ("Accept-Ranges: bytes");
    header ("Content-Length: " . $size);
    header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
    header ("Content-Type: video/x-flv");
    header ('Content-Disposition: inline; filename="' . $this->video_id  . '.flv"');


    ob_flush();

    if(ERROR_SHOW == 9)
    {
      //error_log("video_class.inc.php:view_video():size: " . $size);
      //error_log("video_class.inc.php:view_video():file: " . $this->video_filename());
    }

    //http_send_file($this->video_filename());

    //echo "\n\n";
    $handle = fopen($this->video_filename(), "rb");
    if($start > 0)
    {
      fseek($handle, $start);
    }
    while (!feof($handle)) {
      echo fread($handle, 8192);
      ob_flush();
    }
    //echo fread($handle, $size);
    //fpassthru($handle);
    fclose($handle);
    ob_end_flush();

    exit;
		//echo $this->get_video();
  }
}


//-----------------------------------------------------------------------------
// класс видео
class VideoClass
{
  //===========================================================================
  // данные члены
  var $video_id;				// id изображения в БД
  var $video_datetime;  // дата/время последнего изменения видео
  var $errors;					// массив сообщений об ошибках
  var $video_type;			// ContentType для изображения
  var $video_table;			// название связанной таблицы
  var $video_table_id;	// идентификатор в связанной таблице
	var $video_dir; 			// каталог в котором храняться картинки
	var $video_content; 	// содержимое картинки
	var $video_length; 	  // длинна видео в секундах
	var $video_file; 			// исходное имя файла изображения
  var $video_thumbnail; // превью картинка для видео файла


  //===========================================================================
  // функции члены

    /**
     * Возвращает строковое представление для изображения image_id
     * @return string
     */
    function __toString()
    {
        if($this->video_id)
            return "".$this->video_id;

        return "";
    }
  //---------------------------------------------------------------------------
  // конструктор
  function VideoClass($video_id = 0)
  {
    $sql = "
SELECT *
FROM ".DATABASE_PREFIX."videos
WHERE videos_id = '" . (integer)$video_id . "'
";

    $db = new CDatabase();
    $result = $db->Query($sql);
		$this->video_dir = VIDEO_DIR;
    if( $result && $row = $db->NextAssoc($result) )
    {
      $this->video_id = (integer)$video_id;
      $this->video_type = $row["videos_contenttype"];
      $this->video_table = $row["videos_table"];
      $this->video_table_id = $row["videos_table_id"];
      $db->Free();
    }
    else
    {
      $this->video_id = NULL;
      $this->video_type = NULL;
    }
  }


	//---------------------------------------------------------------------------
	// Функция возвращает контент изображения из файла
	function get_content($start=NULL)
	{
			$filename = $this->video_filename();

      if(ERROR_SHOW == 9)
      {
        //error_log("video_class.inc.php:get_content:" . print_r($filename, true));
      }

      $this->video_content = "";

      if($start && $start != 0)
      {
              $this->video_content .= "FLV";
              $this->video_content .= pack('C', 1 );
              $this->video_content .= pack('C', 1 );
              $this->video_content .= pack('N', 9 );
              $this->video_content .= pack('N', 9 );
      }
      else
      {
        $start = 0;
      }
      $fh = fopen($filename, "rb") or die("Cannot open file for reading: " . $filename );
      fseek($fh, $start);
      while (!feof($fh))
      {
        $this->video_content .= fread($fh, 1024) or die("Cannot read file: " . $filename );
      }
      fclose($fh) or die("Cannot close file: " . $filename );

      if(ERROR_SHOW == 9)
      {
        //error_log("video_class.inc.php:get_content:file is get" . print_r($filename, true));
      }
      /*
			$file = fopen($filename, "r") or die("Cannot open file for reading: " . $filename );
			$this->video_content = fread($file, filesize($filename)) or die("Cannot read file: " . $filename );
			fclose($file) or die("Cannot close file: " . $filename );
      */
	}

	//---------------------------------------------------------------------------
	// Функция пишет контент изображения в файл
	function set_content()
	{
			// запишем картинку на диск
			$filename = $this->video_filename();
			//var_dump($filename);
			$file = fopen($filename, "w") or die("Cannot open file for writing: " . $filename );
			fwrite($file, $this->video_content) or die("Cannot write file: " . $filename );
			fclose($file) or die("Cannot close file: " . $filename );
	}


	//---------------------------------------------------------------------------
	// Функция возвращает расширение для заданного типа картинок
	function video_ext($contenttype)
	{
		//if(preg_match("/(flv|x-flv)/i", $contenttype))
		{
			return "flv";
		}
		return NULL;
	}


	//---------------------------------------------------------------------------
	// Функция возвращает имя файла картинки
	function video_filename()
	{
	//var_dump($this);
	//var_dump($this->video_ext($this->video_type));
		global $root;
		if(!$this->video_id
		|| !$this->video_type
		|| !$this->video_ext($this->video_type)
		|| !$this->video_dir)
			return NULL;

		return (isset($root)?$root:"") . $this->video_dir . "/" . VIDEO_FILE_PREFIX . $this->video_id . "." . $this->video_ext($this->video_type);
	}

  //---------------------------------------------------------------------------
  // Функция вставки изображения в БД
  function insert_video($tmp_filename, $contenttype, $filename, $table = "", $table_id = 0)
  {
    global $connection;
    global $languages;

		$this->video_type = $contenttype;
		$this->video_file = $filename;
		$this->video_table = $table;
		$this->video_table_id = $table_id;
		//$this->video_content = $content;

    if( is_null($this->video_id) )
    {
//error_log("111111111111");
      $query = "
INSERT INTO
  ".DATABASE_PREFIX."videos(videos_datetime,
    videos_contenttype,
    videos_filename,
    videos_table,
    videos_table_id)
  VALUES('" . date("Y-m-d H:i:s") . "',
    '" . $this->video_type . "',
    '" . $this->video_file . "',
    '" . $this->video_table . "',
    '" . $this->video_table_id . "')";
      //$query = "INSERT INTO ".DATABASE_PREFIX."videos(videos_contenttype, videos_content, videos_filename, videos_table, videos_table_id) VALUES('" . $contenttype . "', '" . base64_encode($content) . "', '" . $filename . "', '" . $table . "', '" . $table_id . "')";
      mysql_query($query)  or die(die_mysql_error_show($query));

			// идентификатор картинки
      $this->video_id = mysql_insert_id();

			// запишем картинку на диск
      move_uploaded_file($tmp_filename, $this->video_filename());

      // получим контент из файла
			//$this->get_content();
    }
    else
    {
//error_log("2222222222");
      $query = "SELECT videos_contenttype
FROM ".DATABASE_PREFIX."videos
WHERE videos_id = '" . $this->video_id . "'
";

      $result = mysql_query($query) or die(die_mysql_error_show($query));
      if( $result && $row = mysql_fetch_assoc($result) )
      {
        $sql = "
UPDATE ".DATABASE_PREFIX."videos
SET
  videos_datetime='" . date("Y-m-d H:i:s") . "',
  videos_contenttype='" . $this->video_type . "',
  videos_filename='" . $this->video_file . "',
  videos_table='" . $this->video_table . "',
  videos_table_id='" . $table_id . "'
WHERE
  videos_id = '" . $this->video_id . "'";
        mysql_query($sql) or die(die_mysql_error_show($sql));


  			// запишем картинку на диск
        move_uploaded_file($tmp_filename, $this->video_filename());
				//$this->set_content();
      }
    }
  }

  //---------------------------------------------------------------------------
  // Функция просмотра изображения
  function view_video($start=NULL)
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

    $size = filesize($this->video_filename());

    header ("HTTP/1.1 200 OK", true);
    header ("Status: 200 OK");
    header ("Accept-Ranges: bytes");
    header ("Content-Length: " . $size);
    header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
    header ("Content-Type: video/x-flv");
    header ('Content-Disposition: inline; filename="' . $this->video_id  . '.flv"');


//    if(ERROR_SHOW == 9)
//    {
//      error_log("video_class.inc.php:view_video():size: " . $size);
//      error_log("video_class.inc.php:view_video():file: " . $this->video_filename());
//    }

    //http_send_file($this->video_filename());

    //echo "\n\n";
    $handle = fopen($this->video_filename(), "rb");
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

  //---------------------------------------------------------------------------
  // Функция возвращает изображение из БД
  function get_video($start=NULL)
  {
    global $connection;
    global $_SESSION;
    $sql = "
SELECT videos_contenttype
FROM ".DATABASE_PREFIX."videos
WHERE videos_id = '" . $this->video_id . "'
";
    $result = mysql_query($sql) or die(die_mysql_error_show($sql));
    if( $result && $row = mysql_fetch_assoc($result) )
    {
      mysql_free_result($result);

      $this->get_content($start);

//      if(ERROR_SHOW == 9)
//      {
//        error_log("video_class.inc.php:get_video:" . print_r($this, true));
//      }

      return $this->video_content;
      //return $ret;
    }
    else
    {
      return null;
    }
  }

  //---------------------------------------------------------------------------
  // Функция удаления картинки из БД
  function delete_video()
  {
    if(!$this->video_id){
      return;
    }

    // удалим файл
    @unlink($this->video_filename());

    // удалим ссылку из бд
    $sql = "
DELETE FROM ".DATABASE_PREFIX."videos
WHERE videos_id = '" . $this->video_id . "'
";
    $result = mysql_query($sql)  or die(die_mysql_error_show($sql));

  }


  //---------------------------------------------------------------------------
  // функция создания таблицы для класса
  function ClassCreateTable()
  {
    $query = "
DROP TABLE IF EXISTS `".DATABASE_PREFIX."videos`;
";
    mysql_query($query) or die(die_mysql_error_show($query));

    $query = "
CREATE TABLE  `".DATABASE_PREFIX."videos` (
  `videos_id` int(10) unsigned NOT NULL auto_increment,
  `videos_contenttype` varchar(45) NOT NULL,
  `videos_table` varchar(45) NOT NULL,
  `videos_table_id` int(10) unsigned default NULL,
  `videos_dir` varchar(100) default NULL,
  `videos_filename` varchar(200) default NULL,
  `videos_datetime` datetime NOT NULL,
  `videos_lenght` int(10) unsigned default NULL,
  PRIMARY KEY  (`videos_id`)
) ENGINE=MyISAM;
";
    mysql_query($query) or die(die_mysql_error_show($query));
  }

}

?>
