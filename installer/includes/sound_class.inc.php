<?
//=============================================================================
// Определения классов и функций работы с изображениями

//-----------------------------------------------------------------------------
// Функция выводи в стандартный поток картинку для ошибки
function ErrorSound()
{
  header("Content-Type: audio/mpeg");
  $fp = fopen (VN_ERROR_SOUND, "rb");
  fpassthru($fp);
  fclose ($fp);
}

//-----------------------------------------------------------------------------
// класс звукового файла
class SoundFileClass
{
  var $filename;
  var $sound_dir;

  function SoundFileClass($filename)
  {
		$this->sound_dir = SOUND_DIR;
    $this->filename = $filename;
  }


	//---------------------------------------------------------------------------
	// Функция возвращает имя файла картинки
	function sound_filename()
	{
	  //var_dump($this);
	//var_dump($this->video_ext($this->video_type));
		global $root;
		if(!$this->filename
		|| !$this->sound_dir)
			return NULL;

		return (isset($root)?$root:"") . $this->sound_dir . "/" .  $this->filename;
	}


  //---------------------------------------------------------------------------
  // Функция просмотра изображения
  function view_sound($start=NULL)
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

    $size = filesize($this->sound_filename());

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
    header ("Content-Type: audio/mpeg");
    header ('Content-Disposition: inline; filename="' . basename($this->filename)  . '"');


    if(ERROR_SHOW == 9)
    {
      //error_log("sound_class.inc.php:view_sound():size: " . $size);
      //error_log("sound_class.inc.php:view_sound():file: " . $this->sound_filename());
    }

    //http_send_file($this->video_filename());

    //echo "\n\n";
    $handle = fopen($this->sound_filename(), "rb");
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
// класс звука
class SoundClass
{
  //===========================================================================
  // данные члены
  var $sound_id;				// id изображения в БД
  var $sound_datetime;  // дата/время последнего изменения видео
  var $errors;					// массив сообщений об ошибках
  var $sound_type;			// ContentType для изображения
  var $sound_table;			// название связанной таблицы
  var $sound_table_id;	// идентификатор в связанной таблице
	var $sound_dir; 			// каталог в котором храняться картинки
	var $sound_content; 	// содержимое картинки
	var $sound_length; 	  // длинна видео в секундах
	var $sound_file; 			// исходное имя файла изображения
  var $sound_thumbnail; // превью картинка для видео файла


  //===========================================================================
  // функции члены
	function __toString()
	{
		if($this->sound_id)
			return "".$this->sound_id;
			
		return "";
	}  
  //---------------------------------------------------------------------------
  // конструктор
  function SoundClass($sound_id = 0)
  {
    $sql = "
SELECT *
FROM ".DATABASE_PREFIX."sounds
WHERE sounds_id = '" . (integer)$sound_id . "'
";

    $db = new CDatabase();

    $result = $db->Query($sql);//  or die(die_mysql_error_show($sql));
		$this->sound_dir = SOUND_DIR;
    if( $result && $row = $db->NextAssoc() )
    {
      $this->sound_id = (integer)$sound_id;
      $this->sound_type = $row["sounds_contenttype"];
      $this->sound_table = $row["sounds_table"];
      $this->sound_table_id = $row["sounds_table_id"];
      $db->Free();
    }
    else
    {
      $this->sound_id = NULL;
      $this->sound_type = NULL;
    }
  }


	//---------------------------------------------------------------------------
	// Функция возвращает контент изображения из файла
	function get_content($start=NULL)
	{
			$filename = $this->sound_filename();

      if(ERROR_SHOW == 9)
      {
        //error_log("sound_class.inc.php:get_content:" . print_r($filename, true));
      }

      $this->sound_content = "";

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
        $this->sound_content .= fread($fh, 1024) or die("Cannot read file: " . $filename );
      }
      fclose($fh) or die("Cannot close file: " . $filename );

      if(ERROR_SHOW == 9)
      {
        //error_log("sound_class.inc.php:get_content:file is get" . print_r($filename, true));
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
			fwrite($file, $this->sound_content) or die("Cannot write file: " . $filename );
			fclose($file) or die("Cannot close file: " . $filename );
	}


	//---------------------------------------------------------------------------
	// Функция возвращает расширение для заданного типа картинок
	function sound_ext($contenttype)
	{
		//if(preg_match("/(flv|x-flv)/i", $contenttype))
		{
			return "mp3";
		}
		return NULL;
	}


	//---------------------------------------------------------------------------
	// Функция возвращает имя файла картинки
	function sound_filename()
	{
	//var_dump($this);
	//var_dump($this->sound_ext($this->sound_type));
		global $root;
		if(!$this->sound_id
		|| !$this->sound_type
		|| !$this->sound_ext($this->sound_type)
		|| !$this->sound_dir)
			return NULL;

		return (isset($root)?$root:"") . $this->sound_dir . "/" . SOUND_FILE_PREFIX . $this->sound_id . "." . $this->sound_ext($this->sound_type);
	}

  //---------------------------------------------------------------------------
  // Функция вставки изображения в БД
  function insert_sound($tmp_filename, $contenttype, $filename, $table = "", $table_id = 0)
  {
    global $connection;
    global $languages;

		$this->sound_type = $contenttype;
		$this->sound_file = $filename;
		$this->sound_table = $table;
		$this->sound_table_id = $table_id;
		//$this->sound_content = $content;

    if( is_null($this->sound_id) )
    {
//error_log("111111111111");
      $query = "
INSERT INTO
  ".DATABASE_PREFIX."sounds(sounds_datetime,
    sounds_contenttype,
    sounds_filename,
    sounds_table,
    sounds_table_id)
  VALUES('" . date("Y-m-d H:i:s") . "',
    '" . $this->sound_type . "',
    '" . $this->sound_file . "',
    '" . $this->sound_table . "',
    '" . $this->sound_table_id . "')";
      //$query = "INSERT INTO ".DATABASE_PREFIX."sounds(sounds_contenttype, sounds_content, sounds_filename, sounds_table, sounds_table_id) VALUES('" . $contenttype . "', '" . base64_encode($content) . "', '" . $filename . "', '" . $table . "', '" . $table_id . "')";
      mysql_query($query)  or die(die_mysql_error_show($query));

			// идентификатор картинки
      $this->sound_id = mysql_insert_id();

			// запишем картинку на диск
      move_uploaded_file($tmp_filename, $this->sound_filename());

      // получим контент из файла
			//$this->get_content();
    }
    else
    {
//error_log("2222222222");
      $query = "SELECT sounds_contenttype
FROM ".DATABASE_PREFIX."sounds
WHERE sounds_id = '" . $this->sound_id . "'
";

      $result = mysql_query($query) or die(die_mysql_error_show($query));
      if( $result && $row = mysql_fetch_assoc($result) )
      {
        $sql = "
UPDATE ".DATABASE_PREFIX."sounds
SET
  sounds_datetime='" . date("Y-m-d H:i:s") . "',
  sounds_contenttype='" . $this->sound_type . "',
  sounds_filename='" . $this->sound_file . "',
  sounds_table='" . $this->sound_table . "',
  sounds_table_id='" . $table_id . "'
WHERE
  sounds_id = '" . $this->sound_id . "'";
        mysql_query($sql) or die(die_mysql_error_show($sql));


  			// запишем картинку на диск
        move_uploaded_file($tmp_filename, $this->sound_filename());
				//$this->set_content();
      }
    }
  }

  //---------------------------------------------------------------------------
  // Функция просмотра изображения
  function view_sound($start=NULL)
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

    $size = filesize($this->sound_filename());

    header ("HTTP/1.1 200 OK", true);
    header ("Accept-Ranges: bytes");
    header ("Content-Length: " . $size);
    header ("Content-Range: bytes " . $start?$start:"0" . "-" . $size);
    header ("Content-Type: audio/mpeg");
    header ('Content-Disposition: inline; filename="' . $this->sound_id  . '.mp3"');


    if(ERROR_SHOW == 9)
    {
      //error_log("sound_class.inc.php:view_sound():size: " . $size);
      //error_log("sound_class.inc.php:view_sound():file: " . $this->sound_filename());
    }

    //http_send_file($this->sound_filename());

    //echo "\n\n";
    $handle = fopen($this->sound_filename(), "rb");
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
  // Функция возвращает изображение из БД
  function get_sound($start=NULL)
  {
    global $connection;
    global $_SESSION;
    $sql = "
SELECT sounds_contenttype
FROM ".DATABASE_PREFIX."sounds
WHERE sounds_id = '" . $this->sound_id . "'
";
    $result = mysql_query($sql) or die(die_mysql_error_show($sql));
    if( $result && $row = mysql_fetch_assoc($result) )
    {
      mysql_free_result($result);

      $this->get_content($start);

//      if(ERROR_SHOW == 9)
//      {
//        error_log("sound_class.inc.php:get_sound:" . print_r($this, true));
//      }

      return $this->sound_content;
      //return $ret;
    }
    else
    {
      return null;
    }
  }

  //---------------------------------------------------------------------------
  // Функция удаления картинки из БД
  function delete_sound()
  {
    if(!$this->sound_id){
      return;
    }

    // удалим файл
    @unlink($this->sound_filename());

    // удалим ссылку из бд
    $sql = "
DELETE FROM ".DATABASE_PREFIX."sounds
WHERE sounds_id = '" . $this->sound_id . "'
";
    $result = mysql_query($sql)  or die(die_mysql_error_show($sql));

  }


  //---------------------------------------------------------------------------
  // функция создания таблицы для класса
  function ClassCreateTable()
  {
    $query = "
DROP TABLE IF EXISTS `".DATABASE_PREFIX."sounds`;
";
    mysql_query($query) or die(die_mysql_error_show($query));

    $query = "
CREATE TABLE  `".DATABASE_PREFIX."sounds` (
  `sounds_id` int(10) unsigned NOT NULL auto_increment,
  `sounds_contenttype` varchar(45) NOT NULL,
  `sounds_table` varchar(45) NOT NULL,
  `sounds_table_id` int(10) unsigned default NULL,
  `sounds_dir` varchar(100) default NULL,
  `sounds_filename` varchar(200) default NULL,
  `sounds_datetime` datetime NOT NULL,
  `sounds_lenght` int(10) unsigned default NULL,
  PRIMARY KEY  (`sounds_id`)
) ENGINE=MyISAM;
";
    mysql_query($query) or die(die_mysql_error_show($query));
  }

}

?>
