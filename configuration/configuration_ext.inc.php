<?
//=============================================================================
// Файл дополнительных настроек
//=============================================================================

// VN - Virtual Name
// FS - File System


// метод аутоинфикации
// http
// basic
if(!defined("ADMIN_AUTH_METHOD"))
    define("ADMIN_AUTH_METHOD", "http");

//-----------------------------------------------------------------------------
// Главная страница
if(!defined("VN_INDEX"))
    define("VN_INDEX", VN_SERVER . VN_DIR . "index.php");
	
//-----------------------------------------------------------------------------
// Обычная страница
if(!defined("VN_PAGE"))
    define("VN_PAGE", VN_SERVER . VN_DIR . "index.php");

//-----------------------------------------------------------------------------
// админка
if(!defined("VN_ADMIN"))
    define("VN_ADMIN", VN_DIR . DIRNAME_ADMIN );
if(!defined("VN_ADMIN_INDEX"))
    define("VN_ADMIN_INDEX", VN_ADMIN . "/index.php");
if(!defined("VN_ADMIN_SIGNIN"))
    define("VN_ADMIN_SIGNIN", VN_ADMIN . "/signin.php");
if(!defined("VN_ADMIN_SIGNOUT"))
    define("VN_ADMIN_SIGNOUT", VN_ADMIN . "/signout.php");
if(!defined("VN_ADMIN_WRONG"))
    define("VN_ADMIN_WRONG", VN_ADMIN . "/wrong.php");

//-----------------------------------------------------------------------------
// редактор
if(!defined("VN_EDITOR3"))
    define("VN_EDITOR3", VN_ADMIN . "/e");

if(!defined("VN_EDITOR3_FILES_FUNCTION"))
    define("VN_EDITOR3_FILES_FUNCTION", "
				function(callback) {
					$('<div id=\"myelfinder\" />').elfinder({
						url : '".VN_EDITOR3."/connectors/php/connector.php?[params]',
						lang : 'ru',
						dialog : { width : 900, modal : true, title : 'Files' },
						closeOnEditorCallback : true,
						editorCallback : callback
					})
				}
				"
    );

if(!defined("VN_EDITOR3_SETTINGS"))
    define("VN_EDITOR3_SETTINGS", "
			elRTE.prototype.options.panels.qcms1 = [
				'formatblock'
			];
			elRTE.prototype.options.panels.qcms2 = [
				'horizontalrule', 'blockquote', 'div', 'stopfloat', 'nbsp'
			];

			elRTE.prototype.options.toolbars.qcmsToolbar = ['save', 'copypaste', 
				'undoredo', 'style', 'lists', 'alignment', 'indent', 'qcms1',  
				'qcms2', 'links', 'images', 'elfinder', 'tables', 'fullscreen' ];
			                                                			
			
			editor_opts = {
				cssClass : 'el-rte',
				lang     : 'ru',
				width		 : 600,
				height   : 300,
				//toolbar  : 'maxi',
			  toolbar  : 'qcmsToolbar',
				absoluteURLs: false,
				allowSource: true,

				fmAllow: true,
				
				fmOpen : ".VN_EDITOR3_FILES_FUNCTION.",
					
				cssfiles : ['".VN_EDITOR3."/css/elrte-inner.css']
				
			}
			
			$(window).load(function () {  
				// run code
				$('form.entity_edit_form').submit(function() {
					submit_form_function();
				});
			});
			
			function submit_form_function()
			{
				$('textarea').each(function() {
					this.elrte !== void(0) && $(this).elrte('updateSource');
				});
				return true;
			}
			
if(typeof String.prototype.trim !== 'function') { 
  String.prototype.trim = function() { 
    return this.replace(/^\s+|\s+$/g, '');  
  } 
}
"
    );

if(!defined("VN_EDITOR3_SCRIPT"))
    define("VN_EDITOR3_SCRIPT", "
$('#[id]').elrte(editor_opts);
"
    );



//-----------------------------------------------------------------------------
// charset
if(!defined("DEFAULT_CHARSET"))
    define("DEFAULT_CHARSET","UTF-8");

//-----------------------------------------------------------------------------
// Управление отображением ошибок
// 0 - не показывать ошибки
// 1 - показывть сообщения об ошибках
// ...
// 9 - показывать все, что только возможно
if(!defined("ERROR_SHOW"))
    define("ERROR_SHOW", 9);

//-----------------------------------------------------------------------------
// site default email
if(!defined("EMIAL"))
    define("EMIAL", "mail@localhost");







//-----------------------------------------------------------------------------
// каталоги
if(!defined("DIR_INCLUDES"))
    define("DIR_INCLUDES", VN_DIR . DIRNAME_INCLUDES);    // каталог инклудов
if(!defined("DIR_BOXES"))
    define("DIR_BOXES", VN_DIR . DIRNAME_BOXES);          // каталог боксов

//-----------------------------------------------------------------------------
// css
if(!defined("DIR_CSS"))
    define("DIR_CSS", VN_SERVER . VN_DIR . DIRNAME_CSS);  // путь к каталогу css
if(!defined("FILE_CSS"))
    define("FILE_CSS", DIR_CSS. "/style.css");            // основной файл css
if(!defined("FILE_CSS_LANG_TEMPLATE"))
    define("FILE_CSS_LANG_TEMPLATE", DIR_CSS. "/style_[lang].css");            // основной файл css
if(!defined("FILENAME_TEMPLATES_FORM    "))
    define("FILENAME_TEMPLATES_FORM", "form.inc.php");    // каталог шаблонов


//-----------------------------------------------------------------------------
// изображения
if(!defined("DIR_IMAGES"))
    define("DIR_IMAGES", VN_DIR . DIRNAME_IMAGES);        // каталог картинок
if(!defined("VIEW_IMAGE"))
    define("VIEW_IMAGE", VN_SERVER . VN_DIR . "view_image.php"); // просмотр картинок
if(!defined("VN_IMAGE"))
    define("VN_IMAGE", VN_SERVER . VN_DIR . "image.php"); // страница для просмотра больших картинок
if(!defined("VN_PIXEL"))
    define("VN_PIXEL",  VN_SERVER . VN_DIR . "i/pixel.gif");
if(!defined("VN_ERROR_IMAGE"))
    define("VN_ERROR_IMAGE", VN_SERVER  . VN_DIR . "i/error_image.gif");

//-----------------------------------------------------------------------------
// настройки видео
if(!defined("DIR_VIDEOS"))
    define("DIR_VIDEOS", VN_DIR . DIRNAME_VIDEOS);                // каталог видео
if(!defined("VIEW_VIDEO"))
    define("VIEW_VIDEO", VN_SERVER . VN_DIR . "view_video.php");  // просмотр видео
if(!defined("VN_ERROR_VIDEO"))
    define("VN_ERROR_VIDEO", VN_SERVER . VN_DIR . "/videos/intro.flv");  // ошибка видео
if(!defined("VN_VIDEO"))
    define("VN_VIDEO", VN_SERVER . VN_DIR . "video.php");

//-----------------------------------------------------------------------------
// языки
if(!defined("LANGUAGE_DEFAULT"))
	define("LANGUAGE_DEFAULT", "RU");

global $languages;
$languages = array(); // массив описаний языков
$languages[0] = array("name" => "Рус", "code" => "RU", "num" => 0); // язык по умолчанию
$languages[1] = array("name" => "Eng", "code" => "EN", "num" => 1);
//$languages[2] = array("name" => "Ita", "code" => "IT", "num" => 2);
//$languages[3] = array("name" => "Deu", "code" => "DE", "num" => 3);



?>
