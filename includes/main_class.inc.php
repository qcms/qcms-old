<?
/**
 * main class CMain
 * @author Belyaev
 *
 */
class CMain
{
    /**
     * признак того что заголовочные файлы загружены
     * @var bool
     */
    var $is_header_includes;

    /**
     * administration mode
     * признак того что мы в админке
     * @var boolean
     */
    var $is_admin;

    /**
     * признак того что для данной страницы админки не требуется авторизация
     * @var boolean
     */
    var $is_admin_non_auth;

    /**
     * @var CAdminUser
     */
    var $adminuser;

    /**
     * признак необходимости использовать сессию
     * @var boolean
     */
    var $need_session;

    /**
     * признак необходимости использовать информацию о сессии из БД и сохранять сессию в БД
     * @var boolean
     */
    var $need_session_db;

    /**
     * требуется обновление последней позиции в сессии
     * @var boolean
     */
    var $session_last_query;

    /**
     * @var boolean
     */
    var $need_xajax;

    /**
     * нужна инициализация $this->config
     * @var boolean
     */
    var $need_config;

    /**
     * требуется буферизация вывода
     * @var boolean
     */
    var $output_buffering;

    /**
     * @var string
     */
    var $body_onload;

    var $modules;

//  var $lang_number;
//  var $lang_code;
//  var $lang_name;

    var $head_scripts;

    /**
     * Entity config
     * @var CEntity
     */
    var $config;

    /**
     * relative path to the root of the site
     * Относительный путь к корню сайта
     * @var string
     */
    var $root;

    /**
     * database object
     * Объект базы данных
     * @var CDatabase
     */
    var $database;

    /**
     * callback variables
     * @var array(string)
     */
    var $callback_vars;
    /**
     * callback functions
     * function function_name() {retrun "value";}
     * @var array(string)
     */
    var $callback_functions;
    /**
     * callback functions to parse buffer
     * function function_name($MAIN, &$buffer) { ... modify buffer }
     * @var array(string)
     */
    var $callback_buffer_functions;


    /**
     * конструктор
     * @param string $root относительный путь к корню сайта
     * @param array $params параметры конструктора
     */
    function CMain(
        $root = "",
        $params=array(
            "need_session"=>true, // требуется сессия
            "need_session_db"=>true, // требуется обновление сессии в БД
            "need_xajax"=>true, // требуется XAJAX
            "need_config"=>true, // нужна инициализация $this->config
            "need_database"=>true, // требуется подключение к БД
            "output_buffering"=>true, // требуется буферизация вывода
            "session_last_query"=>true, // требуется обновление последней позиции в сессии
            "is_admin"=>false, // признак админски (требуется авторизация)
            "is_admin_non_auth"=>false, // для админской страницы не требуется авторизация
        )
    )
    {
        $this->root = $root;
        $this->is_header_includes = false;
        $this->adminuser = false;

        if(!isset($params["need_session"]))
        {
            $params["need_session"] = true;
        }
        if(!isset($params["need_session_db"]))
        {
            $params["need_session_db"] = true;
        }
        if(!isset($params["need_xajax"]))
        {
            $params["need_xajax"] = true;
        }
        if(!isset($params["need_config"]))
        {
            $params["need_config"] = true;
        }
        if(!isset($params["need_database"]))
        {
            $params["need_database"] = true;
        }
        if(!isset($params["output_buffering"]))
        {
            $params["output_buffering"] = true;
        }
        if(!isset($params["session_last_query"]))
        {
            $params["session_last_query"] = true;
        }
        if(!isset($params["is_admin"]))
        {
            $params["is_admin"] = false;
        }
        if(!isset($params["is_admin_non_auth"]))
        {
            $params["is_admin_non_auth"] = false;
        }



        $this->need_session = $params["need_session"];
        $this->need_session_db = $params["need_session_db"];
        $this->need_xajax = $params["need_xajax"];
        $this->need_config = $params["need_config"];
        $this->output_buffering = $params["output_buffering"];
        $this->session_last_query = $params["session_last_query"];
        $this->body_onload = "";
        $this->head_scripts = "";
        $this->is_admin = $params["is_admin"];
        $this->is_admin_non_auth = $params["is_admin_non_auth"];
        $this->callback_vars = array();
        $this->callback_functions = array();
        $this->callback_buffer_functions = array();

        //error_log('$_SERVER["REQUEST_URI"]: '.$_SERVER["REQUEST_URI"]);
        //error_log('$MAIN->CMain(): '. print_r($this, true));
    }


    /**
     * Функция возвращает номер версии CMS
     * @return Ambiguous
     */
    function VersionNumber()
    {
        return base64_decode("MS4wLjM=");
    }

    /**
     * Функция возвращает название CMS
     * @return Ambiguous
     */
    function VersionName()
    {
        return base64_decode("UUNNUw==");
    }

    /**
     * Возвращает версию CMS
     * @return Ambiguous
     */
    function Version()
    {
        return CMain::VersionName() . " " . CMain::VersionNumber();
    }


    /**
     * @param string $template_content          - содержимое шаблона для отображения
     * @param bool $output
     * @return string
     */
    function ShowTemplateContent($template_content, $output = false)
    {
        $ret = "";

        $ret .= $template_content;

        // подстановка шаблонов
        CMain::ShowTemplateIf($ret); // {if:variable_name}{else:variable_name}{endif:variable_name}
        CMain::ShowTemplateFunction($ret); // {function:function_name}
        CMain::ShowTemplateFunctionParams($ret); // {function:function_name:params_array_name}
        CMain::ShowTemplateConstant($ret); // {constant:constant_name}
        CMain::ShowTemplateVariable($ret); // {variable:variable_name}
        CMain::ShowTemplateLangMessage($ret); // {lang_message:lang_message_name}

        // вывод
        if($output)
        {
            echo $ret;
        }

        return $ret;
    }

    /**
     * @param string $filename          - имя файла для которого ищем шаблон
     * @param string $template          - имя шаблона
     * @param string $component         - имя компонента шаблона
     * @param string $template_file     - файл шаблона (если не указан выбирается basename($filename))
     * @param bool $output
     * @return string
     */
    function ShowTemplate($filename, $template="", $component="", $template_file="", $output = false)
    {
        $ret = "";

        $template_file_name = CMAIN::GetTemplateFile($filename, $template, $component, $template_file);
        //error_log($template_file_name);

        if($template_file_name
            && file_exists($template_file_name))
        {
            $ret = file_get_contents($template_file_name);
        }

        // подстановка шаблонов
        CMain::ShowTemplateIf($ret); // {if:variable_name}{else:variable_name}{endif:variable_name}
        CMain::ShowTemplateFunction($ret); // {function:function_name}
        CMain::ShowTemplateFunctionParams($ret); // {function:function_name:params_array_name}
        CMain::ShowTemplateConstant($ret); // {constant:constant_name}
        CMain::ShowTemplateVariable($ret); // {variable:variable_name}
        CMain::ShowTemplateLangMessage($ret); // {lang_message:lang_message_name}


        // вывод
        if($output)
        {
            echo $ret;
        }

        return $ret;
    }


    /**
     * Подстановка шаблона констант
     * {constant:constant_name}
     * - заменяется значением константы с именем "constant_name"
     * @param $template_file_content
     */
    function ShowTemplateConstant(&$template_file_content)
    {
        $template_file_content = preg_replace_callback('/\{constant\:([^\}]+)\}/ims', array("CMain", "ShowTemplateConstantCallback"), $template_file_content);
    }

    /**
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateConstantCallback($matches)
    {
        $ret = "";
        if(defined($matches[1]))
        {
            $ret = constant($matches[1]);
        }
        return $ret;
    }

    /**
     * Подстановка шаблона функций
     * {function:function_name}
     * - вызов  функции "function_name", возвращаемое значение выводится в место шаблона функции
     * @param $template_file_content
     */
    function ShowTemplateFunction(&$template_file_content)
    {
        $template_file_content = preg_replace_callback('/\{function\:([^\}\:]+)\}/ims', array("CMain", "ShowTemplateFunctionCallback"), $template_file_content);
    }

    /**
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateFunctionCallback($matches)
    {
        $ret = "";
        if(function_exists($matches[1]))
        {
            $func = $matches[1];
            $ret = CMain::ShowTemplateContent($func());
        }
        return $ret;
    }


    /**
     * Подстановка шаблона функций с параметрами
     * {function:function_name:params_array_name}
     * - вызов функции "function_name" с параметрами в массиве с именем "params_array_name",
     * возвращаемое значение выводится в место шаблона функции
     * @param $template_file_content
     */
    function ShowTemplateFunctionParams(&$template_file_content)
    {
        $template_file_content = preg_replace_callback('/\{function\:([^\:]+)\:([^\}]+)\}/ims', array("CMain", "ShowTemplateFunctionParamsCallback"), $template_file_content);
    }

    /**
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateFunctionParamsCallback($matches)
    {
        $ret = "";
        if(function_exists($matches[1])
            && isset($GLOBALS[$matches[2]])
            && is_array($GLOBALS[$matches[2]]))
        {
            $func = $matches[1];
            $ret = CMain::ShowTemplateContent($func($GLOBALS[$matches[2]]));
        }
        return $ret;
    }

    /**
     * Подстановка шаблона переменной
     * {variable:variable_name}
     * - возвращается значение переменной "variable_name"
     * @param $template_file_content
     */
    function ShowTemplateVariable(&$template_file_content)
    {
        $template_file_content = preg_replace_callback('/\{variable\:([^\}]+)\}/ims', array("CMain", "ShowTemplateVariableCallback"), $template_file_content);
    }

    /**
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateVariableCallback($matches)
    {
        $ret = "";
        if(isset($GLOBALS[$matches[1]]))
        {
            $ret = $GLOBALS[$matches[1]];
        }
        return $ret;
    }

    /**
     * Подстановка условного шаблона
     * {if:variable_name}{else:variable_name}{endif:variable_name}
     * - возвращается значение переменной "variable_name"
     * @param $template_file_content
     */
    function ShowTemplateIf(&$template_file_content)
    {
        //error_log("TEST1");
        //error_log($template_file_content);
        $template_file_content = preg_replace_callback('/\{if\:(?P<condition>[^\}]+)\}(?P<statament>.*)(\{else\:(?P=condition)\})(?P<else_statament>.*)\{endif\:(?P=condition)\}/ims', array("CMain", "ShowTemplateIfElseEndIfCallback"), $template_file_content);
        $template_file_content = preg_replace_callback('/\{if\:(?P<condition>[^\}]+)\}(?P<statament>.*)\{endif\:(?P=condition)\}/ims', array("CMain", "ShowTemplateIfEndIfCallback"), $template_file_content);
        //$matches = false;
        //preg_match('/\{if\:(?P<condition>[^\}]+)\}(.*)((\{else\:\g{1}\})(.*))?\{endif\:\g{1}\}/ims', $template_file_content, $matches);
        //error_log(print_r($matches,true));
    }

    /**
     * {if:variable_name}{endif:variable_name}
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateIfEndIfCallback($matches)
    {
        $ret = "";
        //error_log(print_r($matches, true));
        //$ret = $matches[0];
        if(isset($GLOBALS[$matches["condition"]]))
        {
            if($GLOBALS[$matches["condition"]])
            {
                if(isset($matches["statament"]))
                {
                    $ret = $matches["statament"];
                }

            }
            else
            {
                if(isset($matches["else_statament"]))
                {
                    $ret = $matches["else_statament"];
                }
            }
        }
        return $ret;
    }
    /**
     * {if:variable_name}{else:variable_name}{endif:variable_name}
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateIfElseEndIfCallback($matches)
    {
        $ret = "";
        //error_log(print_r($matches, true));
        //$ret = $matches[0];
        if(isset($GLOBALS[$matches["condition"]]))
        {
            if($GLOBALS[$matches["condition"]])
            {
                if(isset($matches["statament"]))
                {
                    $ret = $matches["statament"];
                }

            }
            else
            {
                if(isset($matches["else_statament"]))
                {
                    $ret = $matches["else_statament"];
                }
            }
        }
        return $ret;
    }



    /**
     * Подстановка шаблона языкового сообщения
     * {lang_message:lang_message_name}
     * - возвращает значение из языковых сообщений с именем "lang_message_name"
     * @param $template_file_content
     */
    function ShowTemplateLangMessage(&$template_file_content)
    {
        $template_file_content = preg_replace_callback('/\{lang_message\:([^\}]+)\}/ims', array("CMain", "ShowTemplateLangMessageCallback"), $template_file_content);
    }

    /**
     * @param $matches
     * @return mixed|string
     */
    function ShowTemplateLangMessageCallback($matches)
    {
        $ret = "";
        $ret = CMain::GetLangMessage($matches[1]);
        return $ret;
    }


    /**
     * возвращает имя файла шаблона для файла $filename, имя шаблона $template
     * @param string $filename          - имя файла для которого ищем шаблон
     * @param string $template          - имя шаблона
     * @param string $component         - имя компонента шаблона
     * @param string $template_file     - файл шаблона (если не указан выбирается basename($filename))
     * @return string
     */
    function GetTemplateFile($filename, $template="", $component="", $template_file="")
    {
        $ret = "";

        if(!$template)
        {
            $template = "default"; // шаблон по умолчанию
        }

        if($filename)
        {
            $current_file = $filename;

        }
        else
        {
            $current_file = $_SERVER["REQUEST_URI"];
        }
        $current_file = preg_replace('/\?.*$/ims', "", $current_file);

        $current_file_path = dirname($current_file);
        if(basename($current_file_path) == "system") // удалим /system в конце имени каталога шаблона
        {
            //error_log("TEST 2");
            $current_file_path = preg_replace('/(\/|\\\)system$/', '', $current_file_path);
        }
        //error_log('CMain::GetTemplateFile: $current_file_path: '.$current_file_path);
        $current_file_name = basename($current_file);

//        if(!$filename)
//        {
//            $ret  .= $_SERVER["DOCUMENT_ROOT"];
//        }
        $ret .= $current_file_path
            . DIRECTORY_SEPARATOR
            . DIRNAME_TEMPLATES
            . DIRECTORY_SEPARATOR
            . $template;

        if(isset($this) && isset($this->is_admin) && $this->is_admin)
        {
            $ret .= DIRECTORY_SEPARATOR
                .DIRNAME_ADMIN;
        }

        if($component)
        {
            $ret .= DIRECTORY_SEPARATOR
                . $component;
        }

        if($template_file)
        {
            $ret .= DIRECTORY_SEPARATOR
                . $template_file;
        }
        else
        {
            $ret .= DIRECTORY_SEPARATOR
                . $current_file_name;
        }

        return $ret;
    }

    /**
     * возвращает содержимое шаблона для файла $filename, имя шаблона $template
     * @param string $filename          - имя файла для которого ищем шаблон
     * @param string $template          - имя шаблона
     * @param string $component         - имя компонента шаблона
     * @param string $template_file     - файл шаблона (если не указан выбирается basename($filename))
     * @return string
     */
    function GetTemplateContent($filename, $template="", $component="", $template_file="")
    {
        $ret = "";
        $template_filename = CMain::GetTemplateFile($filename, $template, $component,$template_file);

        if(file_exists($template_filename))
        {
            $ret = file_get_contents($template_filename);
        }

        return $ret;
    }

    /**
     * Функция загружает языковые сообщения
     * для текущего исполняемого файла из директории вида
     * /path_to_current_file/lang/lang_code/current_file_name.php
     * @param string $filename
     */
    function LoadLangMessages($filename="")
    {

        if($filename)
        {
            $current_file = $filename;

        }
        else
        {
            $current_file = $_SERVER["REQUEST_URI"];
        }
        $current_file = preg_replace('/\?.*$/ims', "", $current_file);

        $current_file_path = dirname($current_file);
        $current_file_name = basename($current_file);

        $lang_file = "";
        if(!$filename)
        {
            $lang_file  .= $_SERVER["DOCUMENT_ROOT"];
        }
        $lang_file .= $current_file_path
            . DIRECTORY_SEPARATOR
            . DIRNAME_LANG
            . DIRECTORY_SEPARATOR
            . strtolower(CMAIN::GetLangCode())
            . DIRECTORY_SEPARATOR
            . $current_file_name;

        $lang_file = str_replace("/", DIRECTORY_SEPARATOR, $lang_file);
        $lang_file = str_replace("\\", DIRECTORY_SEPARATOR, $lang_file);

        //error_log($lang_file);
        if(file_exists($lang_file))
        {
            //error_log('loaded: '.$lang_file);
            global $LangMessages;
            include($lang_file);

            if(isset($LangMessages)
                && is_array($LangMessages))
            {
                //error_log('$LangMessages: '.print_r($LangMessages, true));
                global $MainLangMessages;
                if(!isset($MainLangMessages) || !is_array($MainLangMessages))
                {
                    CMain::ClearLangMessages();
                }
                $MainLangMessages = array_merge($MainLangMessages, $LangMessages);
                //error_log('$MainLangMessages: '.print_r($MainLangMessages, true));
            }
        }
    }

    function ClearLangMessages()
    {
        global $MainLangMessages;
        $MainLangMessages = array();
    }

    /**
     * Функция возвращает сообщение для текущего языка
     * @param string $code мнемонический код сообщения
     * @return string
     */
    function GetLangMessage($code)
    {
        global $MainLangMessages;
        $ret = "";

        if(isset($MainLangMessages)
            && is_array($MainLangMessages)
            && isset($MainLangMessages[$code]))
        {
            $ret = $MainLangMessages[$code];
        }

        return $ret;
    }

    function QueryString()
    {
        return $_SERVER["REQUEST_URI"];
    }

    function QueryStringWithoutParams()
    {
        return CMain::QueryStringWithoutParamsEx($_SERVER["REQUEST_URI"]);
    }

    function QueryStringWithoutParamsEx($url)
    {
        return preg_replace('/\?.*$/ims', '', $url);
    }

    function Redirect($uri)
    {
        if(strlen($uri) <= 0)
        {
            return;
        }
        if(function_exists("ob_start")
            && function_exists("ob_get_length")
            && ob_get_length() > 0
            && function_exists("ob_end_clean")
        )
        {
            //ob_clean();
            ob_end_clean();
        }
        header("Location: " . $uri);
        exit;
    }

    /**
     * Функция подключает конфигурацию текущего сайта по его $_SERVER["SERVER_NAME"]
     */
    function IncludeCurrentSiteConfig()
    {
        if(defined("QCMS_INSTALLER") && QCMS_INSTALLER == "1")
        {
            $this->IncludeSiteConfig("default");
        }
        else
        {
            $this->IncludeSiteConfig($_SERVER["SERVER_NAME"]);
        }
    }


    /**
     * Функция подключает конфигурацию сайта по его имени
     * @param string $site
     */
    function IncludeSiteConfig($site)
    {
        $dirname = $this->root.DIRNAME_CONFIGURATION."/sites/{$site}";
        //var_dump($dirname."/configuration.inc.php");exit;


        if(file_exists($dirname)
            && is_dir($dirname)
            && file_exists($dirname."/configuration.xml")
        )
        {
            $this->IncludeSiteConfigFile($site,"configuration.inc.php");
            $this->IncludeSiteConfigFile($site,"configuration.database.inc.php");
        }
    }


    /**
     * Функция подключает заданный файл конфигруации для заданного сайта
     * @param string $site
     * @param string $file
     */
    function IncludeSiteConfigFile($site, $file)
    {

        $dirname = $this->root.DIRNAME_CONFIGURATION."/sites/{$site}";
        $filename = "{$dirname}/{$file}";
        if(file_exists($dirname)
            && is_dir($dirname)
            && file_exists($filename)
            && is_file($filename)
        )
        {
            include_once $filename;
        }
    }

    /**
     * Функция подключает заданный файл конфигруации
     * @param string $file
     */
    function IncludeConfigurationFile($file)
    {

        $dirname = $this->root.DIRNAME_CONFIGURATION;
        $filename = "{$dirname}/{$file}";
        if(file_exists($dirname)
            && is_dir($dirname)
            && file_exists($filename)
            && is_file($filename)
        )
        {
            include_once $filename;
        }
    }

    /**
     * Функция подключает заданный файл системной конфигруации
     * @param string $file
     */
    function IncludeConfigurationSystemFile($file)
    {

        $dirname = $this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM;
        $filename = "{$dirname}/{$file}";

        //error_log($dirname);
        //error_log($filename);

        if(file_exists($dirname)
            && is_dir($dirname)
            && file_exists($filename)
            && is_file($filename)
        )
        {
            include_once $filename;
        }
    }

    /**
     * Функция подключает заданный файл из каталога 'includes'
     * @param string $file
     */
    function IncludeIncludesFile($file)
    {

        $dirname = $this->root.DIRNAME_INCLUDES;
        $filename = "{$dirname}/{$file}";
        if(file_exists($dirname)
            && is_dir($dirname)
            && file_exists($filename)
            && is_file($filename)
        )
        {
            include_once $filename;
        }
    }


    /**
     * Инициализация XAjax
     */
    function InitXAjax()
    {
        $this->IncludeAllEntities();

        // подключение функций пользователя
        $this->IncludeConfigurationFile("functions.inc.php");


        if($this->need_xajax)
        {
            // подключение модуля XAJAX
            $this->IncludeIncludesFile("xajax.inc.php");
            // подключение функций пользователя XAJAX
            $this->IncludeConfigurationFile("xajax.functions.inc.php");
        }
    }

    /**
     * Инициализация Сессии
     */
    function InitSession()
    {
        $this->IncludeAllEntities();

        // подключение функций пользователя
        $this->IncludeConfigurationFile("functions.inc.php");

        //error_log('$this->need_session: '.print_r($this->need_session, true));
        if($this->need_session)
        {
            $this->IncludeIncludesFile("session.inc.php");
        }

    }

    /**
     * Функция инициализации параметров
     * @param multitype $table
     */
    function Init($table=false)
    {
        $this->IncludeAllEntities();

        //error_log('Init($table)');
        $this->InitSession();
        $this->InitXAjax();

        // подключение функций пользователя
        $this->IncludeConfigurationFile("functions.inc.php");



        if($this->need_config)
        {
            $this->config = new CEntity(array("table"=>"config","id"=>1));
        }
        else
        {
            $this->config = null;
        }



        $this->adminuser = null;
        if($this->is_admin)
        {
            $this->adminuser = new CAdminUser();
            $admin_login = false;
            $admin_password = false;
            $admin_hash = false;
            if(isset($_SESSION[ADMIN_SESSION_ID]) && strlen($_SESSION[ADMIN_SESSION_ID])>0)
            {
                $admin_hash = $_SESSION[ADMIN_SESSION_ID];
            }

            //error_log('$admin_hash: '.print_r($admin_hash, true));

            if($this->AdminAuth($table, $admin_login, $admin_password, $admin_hash) !== true)
            {
                // админ не авторизован
                // переходим на VN_ADMIN_WRONG
                $backurl = $_SERVER["REQUEST_URI"];
                if(isset($_REQUEST["backurl"]))
                {
                    $backurl = $_REQUEST["backurl"];
                }
                //error_log("CMain::Init(...)");
                //error_log(print_r(get_debug_print_backtrace(), true));
                header("Location: ". VN_ADMIN_WRONG . "?backurl=".urlencode($backurl));
                exit;
            }
        }

        if($this->output_buffering && function_exists("ob_start"))
        {
            if($this->is_admin)
            {
                $this->AdminContentStart();
            }
            else
            {
                $this->ContentStart();
            }
            //ob_start();
        }
    }

    /**
     * Регистрация функции вывода контента
     */
    function ContentStart()
    {
        ob_start(array($this, "ContentCallback"));
    }

    /**
     * Регистрация функции вывода контента врежиме администрирования
     */
    function AdminContentStart()
    {
        ob_start(array($this, "AdminContentCallback"));
    }

    /**
     * Функция вывода контента
     * @param string $buffer
     * @return string
     */
    function ContentCallback($buffer)
    {
        if($this->need_config && $this->config && is_a($this->config, "CEntity"))
        {
            if($this->config->GetHeader("config_site_isclosed") == "1")
            {
                $buffer = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <title>'.$this->config->GetHeader("config_site_name").'</title>
	<meta http-equiv="content-type" content="text/html; charset='.DEFAULT_CHARSET.'" />
  <meta name="description" content="'.$this->config->GetText("config_site_description").'" />
	<meta name="keywords" content="'.$this->config->GetText("config_site_keywords").'" />  
</head>

<body>
'.$this->config->GetText("config_site_isclosed_message").'
</body>

</html>';
                return $buffer;
            }
        }


        if(is_array($this->callback_functions) && count($this->callback_functions))
        {
            foreach ($this->callback_functions as $function)
            {
                if(is_callable($function))
                {
                    $function_ret = $function();
                    $buffer = preg_replace("/".preg_quote("[___main_callback_function_{$function}___]")."/ims", $function_ret, $buffer);
                }
            }
        }

        if(is_array($this->callback_vars) && count($this->callback_vars))
        {
            foreach ($this->callback_vars as $key => $value)
            {
                $buffer = preg_replace("/".preg_quote("[___main_callback_var_{$key}___]")."/ims", $value, $buffer);
            }
        }

        if(is_array($this->callback_buffer_functions) && count($this->callback_buffer_functions))
        {
            foreach ($this->callback_buffer_functions as $function)
            {
                if(is_callable($function))
                {
                    $function($this, $buffer);
                }
            }
        }

        //error_log($buffer);

        return $buffer;
    }

    /**
     * Функция вывода контента в режиме администрирования
     * @param string $buffer
     * @return string
     */
    function AdminContentCallback($buffer)
    {
        $buffer = preg_replace("/".preg_quote("[___main_adminuser_error_messages___]")."/", $this->AdminGetErrors(), $buffer);
        $buffer = preg_replace("/".preg_quote("[___main_adminuser_messages___]")."/", $this->AdminGetMessages(), $buffer);
        return $buffer;
    }


    /**
     * Функция добавления callback переменных
     * @param string $name
     * @param string $value
     */
    function AddCallbackVar($name, $value="")
    {
        $this->callback_vars[$name] = $value;
    }

    /**
     * Функция добавления callback функции
     * @param string $name
     */
    function AddCallbackFunction($name)
    {
        $this->callback_functions[] = $name;
    }

    /**
     * Функция добавления callback функции для буфера
     * @param string $name
     */
    function AddCallbackBufferFunction($name)
    {
        $this->callback_buffer_functions[] = $name;
    }


    /**
     * Функция установки callback переменных
     * @param string $name
     * @param string $value
     */
    function SetCallbackVar($name, $value="")
    {
        $this->AddCallbackVar($name, $value);
    }

    /**
     * Функция возвращает значение callback переменной
     * @param string $name
     */
    function GetCallbackVar($name)
    {
        if(isset($this->callback_vars[$name]))
            return $this->callback_vars[$name];
        return false;
    }


    /**
     * Функция удаления callback переменных
     * @param string $name
     */
    function RemoveCallbackVar($name)
    {
        unset($this->callback_vars[$name]);
    }

    /**
     * Функция удаления callback функции
     * @param string $name
     */
    function RemoveCallbackFunction($name)
    {
        $key = array_search($name, $this->callback_functions);
        if($key !== FALSE)
        {
            unset($this->callback_functions[$key]);
        }
    }

    /**
     * Функция удаления callback функции буфера
     * @param string $name
     */
    function RemoveCallbackBufferFunction($name)
    {
        $key = array_search($name, $this->callback_buffer_functions);
        if($key !== FALSE)
        {
            unset($this->callback_buffer_functions[$key]);
        }
    }

    /**
     * Функция вывода callback переменной
     * @param string $name
     */
    function ShowCallbackVar($name, $output=true)
    {
        $ret = "[___main_callback_var_{$name}___]";

        if($output)
        {
            echo $ret;
        }
        else
        {
            return $ret;
        }
    }


    /**
     * Функция вывода callback Функции
     * @param string $name
     */
    function ShowCallbackFunction($name, $output=true)
    {
        $ret = "[___main_callback_function_{$name}___]";

        if($output)
        {
            echo $ret;
        }
        else
        {
            return $ret;
        }
    }



    /**
     * @return string
     */
    function AdminGetErrors()
    {
        $ret = "";
        if(is_array($this->adminuser->errors) && count($this->adminuser->errors))
        {
            foreach($this->adminuser->errors as $message)
            {
                $ret .= '
<div class="error_message">'.$message.'</div>
';
            }
        }
        return $ret;
    }

    /**
     * @return string
     */
    function AdminGetMessages()
    {
        $ret = "";
        if(is_array($this->adminuser->messages) && count($this->adminuser->messages))
        {
            foreach($this->adminuser->messages as $message)
            {
                $ret .= '
<div class="message">'.$message.'</div>
';
            }
        }
        return $ret;
    }

    /**
     * Функция выводит сообщения админа (авторизации и запреты)
     */
    function AdminShowMessages()
    {
        ?>
    [___main_adminuser_error_messages___]
    [___main_adminuser_messages___]
    <?
    }


    /**
     * Функция возвращает true если админ авторизован
     * иначе false
     * @return bool
     */
    function AdminIsAuth()
    {
        $ret = false;
        if(isset($this->adminuser)
            && $this->adminuser
        )
        {
            if($this->adminuser->IsAuth())
            {
                $ret = true;
            }
            //            if($this->adminuser->IsSuperadmin())
            //            {
            //                $ret = true;
            //            }
            //            elseif(isset($_SESSION[ADMIN_SESSION_ID])
            //                && $this->adminuser->hash
            //                && $this->adminuser->hash === $_SESSION[ADMIN_SESSION_ID])
            //            {
            //                $ret = true;
            //            }

        }
        return $ret;
    }


    /**
     * Функция автризации пользователя в админке
     * @param string $table имя таблицы для которой проверить доступ
     * @param string $login логин пользователя
     * @param string $password пароль пользователя
     * @param string $hash хэш сессии админа
     * @return bool
     */
    function AdminAuth($table="", $login="", $password="", $hash="")
    {
        $ret = false;
        //error_log('$MAIN->AdminAuth(...): '.print_r(func_get_args(), true));
        //error_log('$this->is_admin: '.print_r($this->is_admin, true));
        //error_log('$this->is_admin_non_auth: '.print_r($this->is_admin_non_auth, true));

        if($this->is_admin === true
            && $this->is_admin_non_auth === true)
        {
            $ret = true;

        }
        else
        {
//            if(ADMIN_AUTH_METHOD == "basic")
//            {
//                //if($_SESSION["ADMIN_SESSION_ID"] != session_id())
//                // заголовки авторизации
//                if ( !isset($_SERVER["PHP_AUTH_USER"])
//                    || !isset($_SERVER["PHP_AUTH_PW"])
//                    || !$_SERVER["PHP_AUTH_USER"]
//                    || !$_SERVER["PHP_AUTH_PW"]
//                    //|| !isset($_SESSION["ADMIN_SESSION_ID"])
//                    //|| !$_SESSION["ADMIN_SESSION_ID"]
//                    //|| $_SESSION["ADMIN_SESSION_ID"] != session_id()
//                )
//                {
//                    error_log("1");
//                    error_log('$_SERVER["PHP_AUTH_USER"]: '. $_SERVER["PHP_AUTH_USER"]);
//                    error_log('$_SERVER["PHP_AUTH_PW"]: '. $_SERVER["PHP_AUTH_PW"]);
//                    //error_log('$_SESSION["ADMIN_SESSION_ID"]: '. $_SESSION["ADMIN_SESSION_ID"]);
//
//                    header("WWW-Authenticate: Basic realm=\"". VN_SERVER . "\"");
//                    header("HTTP/1.0 401 Unauthorized");
//                    //header("Location: ".VN_ADMIN_WRONG);
//                    echo "Need password...\n";
//                    exit;
//                }
//                elseif( isset($_SERVER['PHP_AUTH_USER'])
//                    && isset($_SERVER['PHP_AUTH_PW'])
//                    && $_SERVER['PHP_AUTH_USER']
//                    && $_SERVER['PHP_AUTH_PW']
//                    && (!isset($_SESSION["ADMIN_SESSION_ID"])
//                        || !$_SESSION["ADMIN_SESSION_ID"]
//                        || $_SESSION["ADMIN_SESSION_ID"] != session_id()
//                    )
//                    //&& $_SESSION["ADMIN_SESSION_ID"] == session_id()
//                )
//                {
//                    if(!session_id())
//                    {
//                        session_start();
//                        $_SESSION["ADMIN_SESSION_ID"] = session_id();
//                    }
//                    error_log("2");
//                    error_log('$_SERVER["PHP_AUTH_USER"]: '. $_SERVER["PHP_AUTH_USER"]);
//                    error_log('$_SERVER["PHP_AUTH_PW"]: '. $_SERVER["PHP_AUTH_PW"]);
//                    error_log('session_id(): '. var_export(session_id(), true));
//                    error_log('$_SESSION["ADMIN_SESSION_ID"]: '. var_export($_SESSION, true));
//
//
//                    $_SESSION["ADMIN_SESSION_ID"] = session_id();
//                    header("WWW-Authenticate: Basic realm=\"". VN_SERVER . "\"");
//                    header("HTTP/1.0 401 Unauthorized");
//                    //header("Location: ".VN_ADMIN_WRONG);
//                    echo "Need password...\n";
//                    exit;
//
//                    // проверка на админа
//                    $this->adminuser = new CAdminUser(
//                        array(
//                            "login"=>$_SERVER['PHP_AUTH_USER'],
//                            "password"=>$_SERVER['PHP_AUTH_PW'],
//                            //"session_id" => session_id()
//                        )
//                    );
//                    if($this->adminuser->IsAuth($table))
//                    {
//                        $ret = true;
//                        $_SESSION["ADMIN_SESSION_ID"] = session_id();
//                    }
//                }
//                elseif( isset($_SERVER['PHP_AUTH_USER'])
//                    && isset($_SERVER['PHP_AUTH_PW'])
//                    && $_SERVER['PHP_AUTH_USER']
//                    && $_SERVER['PHP_AUTH_PW']
//                    && isset($_SESSION["ADMIN_SESSION_ID"])
//                    && $_SESSION["ADMIN_SESSION_ID"]
//                    && $_SESSION["ADMIN_SESSION_ID"] === session_id()
//                    //&& $_SESSION["ADMIN_SESSION_ID"] == session_id()
//                )
//                {
//                    error_log("3");
//                    error_log('$_SERVER["PHP_AUTH_USER"]: '. $_SERVER["PHP_AUTH_USER"]);
//                    error_log('$_SERVER["PHP_AUTH_PW"]: '. $_SERVER["PHP_AUTH_PW"]);
//                    error_log('$_SESSION["ADMIN_SESSION_ID"]: '. $_SESSION["ADMIN_SESSION_ID"]);
//
//                    // проверка на админа
//                    $this->adminuser = new CAdminUser(
//                        array(
//                            "login"=>$_SERVER['PHP_AUTH_USER'],
//                            "password"=>$_SERVER['PHP_AUTH_PW'],
//                            //"session_id" => session_id()
//                        )
//                    );
//                    if($this->adminuser->IsAuth($table))
//                    {
//                        $ret = true;
//                        $_SESSION["ADMIN_SESSION_ID"] = session_id();
//                    }
//                }
//                if(!$ret)
//                {
//                    // не правильный пароль
//                    if(ADMIN_AUTH_METHOD == "basic")
//                    {
//                        header("WWW-Authenticate: Basic realm=\"" . VN_SERVER . "\"");
//                        header("HTTP/1.0 401 Unauthorized");
//                        //header("Location: ".VN_ADMIN_WRONG);
//                        echo "Wrong password...\n";
//                        exit;
//                    }
//                }
//            }
//            else
            if(ADMIN_AUTH_METHOD == "http")
            {
                if($login
                    && strlen($login) > 0
                    && $password
                    && strlen($password) > 0
                )
                {
                    // проверка на админа
                    $this->adminuser = new CAdminUser(
                        array(
                            "login"=>$login,
                            "password"=>$password,
                        )
                    );
                    if($this->adminuser->IsAuth($table))
                    {
                        $ret = true;
                    }

                }
                elseif(!$login
                    && !$password
                    && $hash
                    && strlen($hash) > 0
                )
                {
                    // проверка на админа
                    $this->adminuser = new CAdminUser(
                        array(
                            "hash"=>$hash,
                        )
                    );
                    if($this->adminuser->IsAuth($table))
                    {
                        $ret = true;
                    }
                }

            }

        }


        //error_log('$MAIN->AdminAuth(): $_SERVER["REQUEST_URI"]: '.print_r($_SERVER["REQUEST_URI"], true));
        //error_log('$MAIN->AdminAuth(): $ret: '.print_r($ret, true));
        return $ret;
    }


    /**
     * Функция возвращает все параметры настроек сайта
     * @return NULL|Ambigous <multitype>
     */
    function GetParams()
    {
        global $db_forms;
        if(!isset($db_forms) || !is_array($db_forms))
        {
            return null;
        }
        return $db_forms;
    }

    /**
     * Функция возвращает параметры таблицы
     * @param string $table
     * @return NULL|Ambigous <multitype>
     */
    function GetTableParams($table)
    {
        global $db_forms;
        if(!$table || !isset($db_forms) || !is_array($db_forms) || !isset($db_forms[$table]))
        {
            return null;
        }
        return $db_forms[$table];
    }


    /**
     * Функция возвращает параметры таблицы
     * @param string $table
     * @param string $param
     * @return bool | array | string
     */
    function GetTableParam($table, $param)
    {
        global $db_forms;
        if(!$table || !isset($db_forms) || !is_array($db_forms) || !isset($db_forms[$table]) || !isset($db_forms[$table][$param]))
        {
            return false;
        }
        return $db_forms[$table][$param];
    }

    /**
     * Функция возвращает параметры полей таблицы
     * @param unknown_type $table
     * @return array
     */
    function GetTableFieldsParams($table)
    {
        $ret = false;
        $table_params = CMain::GetTableParams($table);
        //var_dump($table_params);
        if($table_params && isset($table_params["fields"]) && is_array($table_params["fields"]))
        {
            $ret = $table_params["fields"];
        }
        unset($table_params);
        return $ret;
    }

    /**
     * Функция возвращает массив полей таблицы  заданного типа
     * @param string $table - table name
     * @param string $type - type of fields
     * @return array:
     */
    function GetTableFieldsTypeArray($table, $type)
    {
        $ret = array();
        $table_fields_params = $this->GetTableFieldsParams($table);
        if($type && $table_fields_params && is_array($table_fields_params) && count($table_fields_params))
        {
            foreach($table_fields_params as $key => $value)
            {
                if($value["type"] == $type)
                {
                    array_push($ret,$key);
                }
            }
        }
        unset($table_fields_params);
        return $ret;
    }


    /**
     * Функция возвращает массив полей таблицы  заданных типов
     * @param string $table - table name
     * @param array $types - types of fields
     * @return array:
     */
    function GetTableFieldsTypesArray($table, $types)
    {
        $ret = array();
        foreach ($types as $type)
        {
            $ret = array_merge($ret, $this->GetTableFieldsTypeArray($table, $type));
        }
        return $ret;
    }

    /**
     * Функция возвращает параметры поля таблицы
     * @param string $table
     * @param string $field
     * @return
     */
    function GetTableFieldParams($table, $field)
    {
        $ret = false;
        $table_fields_params = $this->GetTableFieldsParams($table);
        if($table_fields_params && is_array($table_fields_params) && isset($table_fields_params[$field]))
        {
            $ret = $table_fields_params[$field];
        }
        unset($table_fields_params);
        return $ret;
    }

    /**
     * Устанавливает параметры описания поля таблицы
     * @param string $table
     * @param string $field
     * @param string $params
     */
    function SetTableFieldParams($table, $field, $params)
    {
        global $db_forms;
        if(/*isset($db_forms[$table]["fields"][$field]) &&*/ is_array($params))
            $db_forms[$table]["fields"][$field] = $params;
    }

    /**
     * Убирает описание поля таблицы
     * @param string $table
     * @param string $field
     */
    function UnsetTableField($table, $field)
    {
        global $db_forms;
        if(isset($db_forms[$table]["fields"][$field]))
            unset($db_forms[$table]["fields"][$field]);
    }

    /**
     * добавляет параметры поля таблицы
     * @param string $table
     * @param string $field
     * @param array $params
     * @param string $after
     */
    function AddTableFieldParams($table, $field, $params, $after=false)
    {
        global $db_forms;
        if(/*isset($db_forms[$table]["fields"][$field]) &&*/ is_array($params))
        {

            if($after && (isset($db_forms[$table]["fields"][$after]) || $after=="FIRST"))
            {
                // добавление после заданного объекта
                $fields = $db_forms[$table]["fields"];
                $fields_new = array();

                if($after=="FIRST")
                {
                    $fields_new[$field] = $params;
                }

                foreach ($fields as $key=>$value)
                {
                    if($after!="FIRST" && $key == $after)
                    {
                        $fields_new[$key] = $value;
                        $fields_new[$field] = $params;
                    }
                    else
                    {
                        $fields_new[$key] = $value;
                    }
                }
                $db_forms[$table]["fields"] = $fields_new;
                unset($fields_new);
                unset($fields);
            }
            else
            {
                // простое добавление
                $db_forms[$table]["fields"][$field] = $params;
            }
        }
    }

    /**
     * Функция возвращает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @return boolean
     */
    function GetTableFieldParam($table, $field, $param)
    {
        return CMain::GetTableFieldParamEx($table, $field, $param);
    }


    /**
     * Функция возвращает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @return boolean
     */
    function GetTableFieldParamsParam($table, $field, $param)
    {
        return CMain::GetTableFieldParamEx($table, $field, "params", $param);
    }


    /**
     * Функция возвращает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @return boolean
     */
    function GetTableFieldParamEx($table, $field, $param, $param1=false, $param2=false, $param3=false)
    {
        $ret = false;

        $table_fields_params = CMain::GetTableFieldsParams($table);

        if($table_fields_params && is_array($table_fields_params) && isset($table_fields_params[$field][$param]))
        {
            if($param3 && isset($table_fields_params[$field][$param][$param1][$param2][$param3]))
                $ret = $table_fields_params[$field][$param][$param1][$param2][$param3];
            elseif(!$param3 && $param2 && isset($table_fields_params[$field][$param][$param1][$param2]))
                $ret = $table_fields_params[$field][$param][$param1][$param2];
            elseif(!$param2 && !$param3 && $param1 && isset($table_fields_params[$field][$param][$param1]))
                $ret = $table_fields_params[$field][$param][$param1];
            elseif(!$param1 && !$param2 && !$param3)
                $ret = $table_fields_params[$field][$param];
        }

        unset($table_fields_params);

        return $ret;
    }


    /**
     * Функция устанавливает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @param string $value
     */
    function SetTableFieldParam($table, $field, $param, $value=false)
    {
        $this->SetTableFieldParamEx($table, $field, $param, false, false, false, $value);
    }


    /**
     * Функция устанавливает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @param string $value
     */
    function SetTableFieldParamsParam($table, $field, $param, $value=false)
    {
        $this->SetTableFieldParamEx($table, $field, "params", $param, false, false, $value);
        //global $db_forms; var_dump($db_forms[$table]["fields"][$field]); exit;
    }



    /**
     * Функция устанавливает заданный параметр поля таблицы
     * @param string $table
     * @param string $field
     * @param string $param
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @param string $value
     */
    function SetTableFieldParamEx($table, $field, $param, $param1=false, $param2=false, $param3=false, $value=false)
    {
        global $db_forms;

        if(isset($db_forms[$table]["fields"][$field]))
        {
            if($value)
            {
                if($param3 && isset($db_forms[$table]["fields"][$field][$param][$param1][$param2]))
                    $db_forms[$table]["fields"][$field][$param][$param1][$param2][$param3] = $value;
                elseif(!$param3 && $param2 && isset($db_forms[$table]["fields"][$field][$param][$param1]))
                    $db_forms[$table]["fields"][$field][$param][$param1][$param2] = $value;
                elseif(!$param2 && !$param3 && $param1 && isset($db_forms[$table]["fields"][$field][$param]))
                    $db_forms[$table]["fields"][$field][$param][$param1] = $value;
                elseif(!$param1 && !$param2 && !$param3)
                    $db_forms[$table]["fields"][$field][$param] = $value;
            }
            else
            {
                if($param3 && isset($db_forms[$table]["fields"][$field][$param][$param1][$param2]))
                    unset($db_forms[$table]["fields"][$field][$param][$param1][$param2][$param3]);
                elseif(!$param3 && $param2 && isset($db_forms[$table]["fields"][$field][$param][$param1]))
                    unset($db_forms[$table]["fields"][$field][$param][$param1][$param2]);
                elseif(!$param2 && !$param3 && $param1 && isset($db_forms[$table]["fields"][$field][$param]))
                    unset($db_forms[$table]["fields"][$field][$param][$param1]);
                elseif(!$param1 && !$param2 && !$param3)
                    unset($db_forms[$table]["fields"][$field][$param]);
            }
        }
    }


    /**
     * Функция проверки того что загружен заголовок базовой функциональности сайта
     * @return boolean
     */
    function IsHeaderIncludes()
    {
        return $this->is_header_includes;
    }


    /**
     * Функция возвращает сущность для заданного языка
     * @param array $params
     * @return Ambigous <NULL, CEntity>
     */
    function GetEntityLang(
        $params=array(
            "table"=>"",
            "id"=>"",
            "index_suffix"=>null,
            "lang"=>""
        )
    )
    {
        global $languages;
        // проверим параметры
        if(!isset($params["table"]) || !$params["table"]
            || !isset($params["id"]) || !$params["id"]
            || !isset($params["lang"]) || !$params["lang"]
        )
        {
            echo "GetEntityLang: wrong params!";
            if(ERROR_SHOW == 9)
            {
                var_dump($params);
                debug_print_backtrace();
            }
            exit;
        }

        // проверим язык
        /*
          if(!in_array($params["lang"], $languages))
          {
              echo "GetEntityParamLang: wrong lang param!";
              if(ERROR_SHOW == 9)
              {
                  debug_print_backtrace();
              }
              exit;
          }
            */

        $ret = null;
        $lang_old = $_SESSION["lang"]; // сохраним язык
        $_SESSION["lang"] = $params["lang"];
        unset($params["lang"]);
        $entity = new CEntity($params);

        if($entity->identity)
        {
            $ret = $entity;
        }
        $_SESSION["lang"] = $lang_old; // вернем язык
        return $ret;
    }


    /**
     * Функция возвращает поле для заданного языка
     * @param array $params
     * @return Ambigous <NULL, boolean, multitype:>
     */
    function GetEntityFieldLang(
        $params=array(
            "table"=>"",
            "field"=>"",
            "id"=>"",
            "index_suffix"=>null,
            "lang"=>""
        )
    )
    {
        global $languages;
        // проверим параметры
        if(!isset($params["table"]) || !$params["table"]
            || !isset($params["field"]) || !$params["field"]
            || !isset($params["id"]) || !$params["id"]
            || !isset($params["lang"]) || !$params["lang"]
        )
        {
            echo "GetEntityParamLang: wrong params!";
            if(ERROR_SHOW == 9)
            {
                var_dump($params);
                debug_print_backtrace();
            }
            exit;
        }

        $ret = null;
        $entity = $this->GetEntityLang($params);
        if($entity && $entity->identity)
        {
            $ret = $entity->GetField($params["field"]);
        }

        return $ret;
        // проверим язык
        /*
          if(!in_array($params["lang"], $languages))
          {
              echo "GetEntityParamLang: wrong lang param!";
              if(ERROR_SHOW == 9)
              {
                  debug_print_backtrace();
              }
              exit;
          }
            */

        /*

          $ret = null;
          $lang_old = $_SESSION["lang"]; // сохраним язык
          $_SESSION["lang"] = $params["lang"];
          if(isset($params["index_suffix"]) && $params["index_suffix"])
          {
              $entity = new CEntity(
                  array(
                      "table"=>$params["table"],
                      "id"=>$params["id"],
                      "index_suffix"=>$params["index_suffix"]
                  )
              );
          }
          else
          {
              $entity = new CEntity(
                  array(
                      "table"=>$params["table"],
                      "id"=>$params["id"]
                  )
              );
          }
          if($entity->identity)
          {
              $ret = $entity->GetField($params["field"]);
          }
          $_SESSION["lang"] = $lang_old; // вернем язык
          return $ret;
            */
    }


    /**
     * Функция загрузки обязательных инклудов сайта
     */
    function HeaderIncludesRequired()
    {
        // подключение конфига текущего сайта
        $this->IncludeCurrentSiteConfig();

        // действительно необходимые конфигурации
        $this->IncludeConfigurationFile("configuration_ext.inc.php");

        if($this->is_admin)
        {
            $this->IncludeIncludesFile("admin.inc.php");
        }


        //    if(file_exists($this->root . DIRNAME_CONFIGURATION . "/configuration.database.inc.php"))
        //    	include_once($this->root . DIRNAME_CONFIGURATION . "/configuration.database.inc.php");
        //    if(file_exists($this->root . DIRNAME_CONFIGURATION . "/configuration.entity.inc.php"))
        //	    include_once($this->root . DIRNAME_CONFIGURATION . "/configuration.entity.inc.php");

        // обязательно нужные функции
        $this->IncludeIncludesFile("functions.inc.php");

        // обязательно нужные классы (база данных, сущность, изображение, навигация)
        $this->IncludeIncludesFile("database_class.inc.php");
        $this->IncludeIncludesFile("entity_class.inc.php");
        $this->IncludeIncludesFile("navigation_class.inc.php");


        $this->IncludeConfigurationSystemFile("image.inc.php");
        $this->IncludeIncludesFile("image_class.inc.php");

        $this->IncludeConfigurationSystemFile("user.inc.php");
        $this->IncludeIncludesFile("user_class.inc.php");
    }


    /**
     * Функция загрузки инклудов сайта
     */
    function HeaderIncludes()
    {
        //$this->HeaderIncludesRequired();
        // необходимые инклуды
        $this->HeaderIncludesRequired();

        // не обязательные классы (звук, видео и т.п.)
        $this->IncludeConfigurationSystemFile("sound.inc.php");
        $this->IncludeIncludesFile("sound_class.inc.php");

        $this->IncludeConfigurationSystemFile("video.inc.php");
        $this->IncludeIncludesFile("video_class.inc.php");

        $this->IncludeConfigurationSystemFile("file.inc.php");
        $this->IncludeIncludesFile("file_class.inc.php");

        $this->IncludeConfigurationSystemFile("user.inc.php");
        $this->IncludeIncludesFile("user_class.inc.php");



        if($this->need_session)
        {
            $this->IncludeConfigurationSystemFile("session.inc.php");
        }
        if($this->need_xajax)
        {
            $this->IncludeConfigurationSystemFile("xajax.inc.php");
        }


        $this->is_header_includes = true;
    }


    /**
     * Функция добавляет событие body.OnLoad
     * @param string $body_onload
     * @return boolean
     */
    function SetBodyOnLoad($body_onload)
    {
        if($this->body_onload)
        {
            if($body_onload)
                $this->body_onload .= "; " . $body_onload;
        }
        else
        {
            $this->body_onload = $body_onload;
        }
        return true;
    }

    /**
     * Функция возвращает событие body.OnLoad
     * @return Ambiguous
     */
    function GetBodyOnLoad()
    {
        return $this->body_onload;
    }

    /**
     * Функция инклудит статичный файл
     * @param string $file
     * @param boolean $admin
     * @param boolean $once
     */
    function IncludeFile($file, $admin=false, $once=false)
    {
        //global $root;
        $filename = $this->root;
        if($admin)
        {
            $filename .= DIRNAME_ADMIN . "/";
        }
        $filename .= $file;
        //var_dump($filename);
        if(file_exists($filename))
        {
            if($once)
            {
                include_once($filename);
            }
            else
            {
                include($filename);
            }
        }
        else
        {
            // system
            $filename = $this->root;
            if($admin)
            {
                $filename .= DIRNAME_ADMIN . "/";
            }
            $filename .= DIRNAME_SYSTEM . "/";
            $filename .= $file;
            //var_dump($filename);
            if(file_exists($filename))
            {
                if($once)
                {
                    include_once($filename);
                }
                else
                {
                    include($filename);
                }
            }
        }
    }


    /**
     * Функция инклудит статичный файл из каталога boxes
     * @param string $module_file
     * @param boolean $admin
     * @param boolean $once
     */
    function IncludeModule($module_file, $admin=false, $once=false)
    {
        $this->IncludeModuleEx($this->root, $module_file, $admin, $once);
    }


    /**
     * Функция проверяет что модуль существует
     * @param string $module_file
     * @param boolean $admin
     * @return boolean
     */
    function CheckModuleExists($module_file, $admin=false)
    {
        return $this->CheckModuleExistsEx($this->root, $module_file, $admin);
    }

    /**
     * Функция инклудит статичный файл из каталога boxes
     * @param string $root
     * @param string $module_file
     * @param boolean $admin
     * @param boolean $once
     */
    function IncludeModuleEx($root, $module_file, $admin=false, $once=false)
    {
        //global $root;

        $filename = $root;
        if($admin)
        {
            $filename .= DIRNAME_ADMIN . "/";
        }
        $filename .= DIRNAME_BOXES."/".$module_file;
        //var_dump($filename);
        if(file_exists($filename))
        {
            if($once)
            {
                include_once($filename);
            }
            else
            {
                include($filename);
            }
        }
        else
        {
            $filename = $root;
            if($admin)
            {
                $filename .= DIRNAME_ADMIN . "/";
            }
            $filename .= DIRNAME_BOXES . "/" . DIRNAME_SYSTEM  . "/" . $module_file;
            //var_dump($filename);
            if(file_exists($filename))
            {
                if($once)
                {
                    include_once($filename);
                }
                else
                {
                    include($filename);
                }
            }

        }
    }


    /**
     * Функция проверяет что модуль существует
     * @param string $root
     * @param string $module_file
     * @param boolean $admin
     * @return boolean
     */
    function CheckModuleExistsEx($root, $module_file, $admin=false)
    {
        $filename = $root;
        if($admin)
        {
            $filename .= DIRNAME_ADMIN . "/";
        }
        $filename .= DIRNAME_BOXES."/".$module_file;
        //var_dump($filename);
        if(file_exists($filename))
        {
            return true;
        }
        return false;
    }

    /**
     * Функция возвращает контент модуля по его мнемокоду
     * @param string $module_ln
     * @return string
     */
    function GetModuleText($module_ln)
    {
        $ret = '';
        $module = new CEntity(
            array(           // массив параметров базы данных объекта
                "table"=>"module",                // таблица объекта
                "id"=>$module_ln,                  // идентификатор объекта в БД
                "index_suffix"=>"ln",        // суфикс для индексного поля
            )
        );

        if($module->identity)
        {
            $ret = $module->GetText("module_text");
        }

        return $ret;
    }

    /**
     * Функция отображения модуля по его мнемокоду
     * @param unknown_type $module_ln
     * @return boolean
     */
    function ShowModule($module_ln)
    {
        $module = new CEntityEx(
            array(           // массив параметров базы данных объекта
                "table"=>"module",                // таблица объекта
                "id"=>$module_ln,                  // идентификатор объекта в БД
                "index_suffix"=>"ln",        // суфикс для индексного поля
                "template"=>"[text:module_text]",          // шаблон отображения объекта
            )
        );

        if($module->identity && $module->headers["module_isshow"] == "1")
        {
            if(isset($module->headers["module_link"])
                && $module->headers["module_link"]
                && file_exists($this->root . "boxes/" . $module->headers["module_link"])
            )
            {
                include($this->root . "boxes/" . $module->headers["module_link"]);
            }
            else
            {
                if($module->headers["module_isphp"] == "1")
                {
                    $ret = CMain::GetModuleText($module_ln);
                    eval($ret);
                }
                else
                {
                    echo CMAIN::GetModuleText($module_ln);
                }
            }
        }
        return true;
    }


    /**
     * функция возвращает текстовый контент для текущего языка
     * @param string $value
     */
    function GetCurrentValueLang($value)
    {
        return CMain::GetValueLang($value, CMain::GetLangCode());
    }

    /**
     * функция возвращает текстовый контент строго для текущего языка
     * @param string $value
     * @return Ambigous <string, unknown>
     */
    function GetCurrentStrictValueLang($value)
    {
        return CMain::GetStrictValueLang($value, CMain::GetLangCode());
    }

    /**
     * Устанавливает значение в языковом массиве
     * @param array $arr
     * @param string $new_value
     * @param string $lang_code
     */
    function SetArrayLang(&$arr, $new_value, $lang_code)
    {
        $arr[CMain::GetLangNumber($lang_code)] = $new_value;
    }

    /**
     * функция устанавливает текстовый контент для данного языка
     * возвращает результат
     * @param string $value
     * @param string $new_value
     * @param string $lang_code
     * @return boolean
     */
    function SetValueLang(&$value,$new_value,$lang_code)
    {
        global $languages;

        // количество языков
        $languages_count = count($languages);
        if(!$languages_count || $languages_count <= 0)
        {
            return false;
        }

        $lang_num = CMain::GetLangNumber($lang_code);
        $value_new = "";
        $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
        $values_count = count($values);
        for($i=0;$i<$languages_count;$i++)
        {
            if($i > 0)
            {
                $value_new .= LANGUAGE_SPLITTER;
            }
            if($i == $lang_num)
            {
                $value_new .= $new_value;
                continue;
            }
            if($i < $values_count && isset($values[$i]))
            {
                // если есть старое значение то следует установить его
                $value_new .= $values[$i];
            }
            else
            {
                $value_new .= "";
                //$value_new .= "_";
            }
        }

        $value = $value_new; // вернем через параметры результат
        //error_log(print_r($value,true));

        return true;
    }


    /**
     * функция устанавливает текстовый контент для текущего языка
     * @param string $value
     * @param string $new_value
     * @return boolean
     */
    function SetCurrentValueLang(&$value,$new_value)
    {
        return CMain::SetValueLang($value, $new_value, $_SESSION["lang"]);
    }

    /**
     * функция устанавливает значение в массив для текущего языка
     * @param array $arr
     * @param string $new_value
     * @return array
     */
    function SetCurrentArrayLang(&$arr,$new_value)
    {
        CMain::SetArrayLang($arr, $new_value, $_SESSION["lang"]);
        if($arr)
        {
            return $arr;
        }
    }


    /**
     * функция устанавливает значение в массив для текущего языка
     * @param array $arr
     * @param string $new_value
     * @return array
     */
    function SetCurrentArrayLangRet($arr,$new_value)
    {
        CMain::SetArrayLang($arr, $new_value, $_SESSION["lang"]);
        return $arr;
    }


    /**
     * Функция возвращает языковой массив заполненый заданным значение $value
     * @param string $value
     * @return multitype
     */
    function NewArrayLang($value="")
    {
        $ret = array();
        global $languages;
        foreach ($languages as $key=>$val)
        {
            $ret[$key] = $value;
        }
        return $ret;
    }


    /**
     * функция возвращает текстовый контент для заданного языка
     * @param string $value
     * @param string $lang_code
     * @return unknown|Ambigous <>
     */
    function GetValueLang($value,$lang_code)
    {
        global $languages;

        // количество языков
        $languages_count = count($languages);
        if(!$languages_count || $languages_count <= 0)
        {
            return $value;
        }

        $lang_num = CMain::GetLangNumber($lang_code);
        if(!preg_match("/" . preg_quote(LANGUAGE_SPLITTER) ."/", $value))
        {
            // нет разделителя вернем $value целиком
            return $value;
        }

        $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
        if($lang_num < count($values) && $values[$lang_num])
        {
            return $values[$lang_num];
        }
        else
        {
            return $values[0];
        }

    }

    /**
     * функция возвращает текстовый контент строго для заданного языка
     * если нет контента возвращает пустую строку
     * @param string $value
     * @param string $lang_code
     * @return unknown|Ambigous <>|Ambiguous
     */
    function GetStrictValueLang($value,$lang_code)
    {
        global $languages;

        // количество языков
        $languages_count = count($languages);
        if(!$languages_count || $languages_count <= 0)
        {
            return $value;
        }

        $lang_num = CMain::GetLangNumber($lang_code);
        if(!preg_match("/" . preg_quote(LANGUAGE_SPLITTER) ."/", $value))
        {
            // нет разделителя вернем $value целиком
            return $value;
        }

        $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
        if($lang_num < count($values) && $values[$lang_num])
        {
            return $values[$lang_num];
        }
        else
        {
            return "";
        }

    }

    /**
     * функция возвращает текстовый контент для текущего языка из массива
     * @param array $array
     * @return Ambigous <string, unknown>
     */
    function GetCurrentArrayLang($array)
    {
        return CMain::GetArrayLang($array, CMain::GetLangCode());
    }


    /**
     * функция возвращает текстовый контент для заданного языка из массива
     * @param array $array
     * @param string $lang_code
     * @return Ambiguous|unknown
     */
    function GetArrayLang($array,$lang_code)
    {
        global $languages;

        if(!is_array($array))
        {
            return "";
        }

        // количество языков
        $languages_count = count($languages);
        if(!$languages_count || $languages_count <= 0)
        {
            return $array[0];
        }

        $lang_num = CMain::GetLangNumber($lang_code);

        if($lang_num >= count($array))
        {
            return $array[0];
        }
        return $array[$lang_num];

        /*
        if(!preg_match("/" . preg_quote(LANGUAGE_SPLITTER) ."/", $value))
        {
          // нет разделителя вернем $value целиком
          return $value;
        }

        $values = preg_split("/" . preg_quote(LANGUAGE_SPLITTER) . "/",$value);
        if($lang_num < count($values) && $values[$lang_num])
        {
          return $values[$lang_num];
        }
        else
        {
          return $values[0];
        }
        */

    }


    /**
     * функция возвращает в виде массива строковую величину с разделителями
     * @param unknown_type $value
     * @return multitype:|multitype:Ambigous <unknown, Ambigous, string>
     */
    function GetArrayLangFromValue($value)
    {
        global $languages;

        $ret = array();

        if(!$value)
        {
            return $ret;
        }

        // количество языков
        $languages_count = count($languages);
        if(!$languages_count || $languages_count <= 0)
        {
            return $ret;
        }

        foreach ($languages as $l)
        {
            $ret[$l["num"]] = CMain::GetStrictValueLang($value, $l["code"]);
        }

        return $ret;
    }

    /**
     * Функция возвращает строки собранную из языкового массива
     * @param unknown_type $array
     * @return string
     */
    function GetStringFromArrayLang($array)
    {
        $ret = "";
        $ret = implode(LANGUAGE_SPLITTER, $array);
        return $ret;
    }


    /**
     * функция возвращает порядковый номер для языка
     * @param string $lang_code
     * @return number|unknown
     */
    function GetLangNumber($lang_code)
    {
        global $languages;
        if(!$lang_code || !is_array($languages))
        {
            return 0;
        }
        foreach($languages as $language)
        {
            if($language["code"] == $lang_code)
            {
                return $language["num"];
            }
        }

        return 0;
    }


    /**
     * Функция возвращает код текущего языка
     * @return unknown|Ambiguous
     */
    function GetLangCode()
    {
        if(session_id() && isset($_SESSION["lang"]))
        {
            return $_SESSION["lang"];
        }
        return LANGUAGE_DEFAULT;
    }


    /**
     * Функция возвращает имя текущего языка
     * @return string
     */
    function GetLangName()
    {
        global $languages;
        if(session_id()
            && isset($_SESSION["lang"])
            && isset($languages)
            && is_array($languages))
        {
            $num = CMain::GetLangNumber(CMain::GetLangCode());
            if(isset($languages[$num]))
            {
                return $languages[$num]["name"];
            }
        }
        return "";
    }


    /**
     * Функция показа ошибки при вызове die
     * в зависимости от текущих параметров отображения
     * @param array $params
     * @return void|Ambiguous
     */
    function ErrorShow($params = array())
    {
        $params_default = array(
            "message" => null,
            "query" => null
        );

        if(!isset($params["message"]))
        {
            $params["message"] = $params_default["message"];
        }
        if(!isset($params["query"]))
        {
            $params["query"] = $params_default["query"];
        }

        //$message = "Ошибка!";

        $ret = "";
        if(ERROR_SHOW <= 0)
            return $ret;

        if($params["message"])
        {
            $ret .= $params["message"] . "
  ";
        }

        if(ERROR_SHOW <= 1)
            return;

        //  $arr = error_get_last();
        //  $ret .= "type = " . $arr["type"] . "
        //";
        //  $ret .= "message = " . $arr["message"] . "
        //";
        //  $ret .= "file = " . $arr["file"] . "
        //";
        //  $ret .= "line = " . $arr["line"] . "
        //";
        //
        if(ERROR_SHOW <= 2)
            return $ret;

        if($params["query"])
        {
            $ret .= mysql_errno() . ": " . mysql_error(). "
  ";
        }

        if(ERROR_SHOW <= 3)
            return $ret;

        if($params["query"])
        {
            $ret .= $params["query"] . "
  ";
        }

        if(ERROR_SHOW <= 4)
            return $ret;

        if(ERROR_SHOW == 9)
        {
            //error_log($ret);

            $ret .= "
        " . print_r(debug_backtrace(), true);
        }
        //  debug_print_backtrace();

        return $ret;

    }


    /**
     * Функция возвращает путь к файлу шаблона
     * @param string $template - имя шаблона
     * @param string $element - элемент шаблона
     * @param string $element_item
     * @param boolean $is_admin - элемент административного интерфейса
     * @return Ambiguous
     */
    function GetTemplatePath($template, $element, $element_item, $is_admin=false)
    {
        $ret = "";

        $ret .= $this->root . DIRNAME_TEMPLATES ."/". $template;
        if($is_admin)
        {
            $ret .= "/" . DIRNAME_TEMPLATES_ADMIN;
        }
        $ret .= "/" . $element . "/" . $element_item .".inc.php";

        return $ret;
    }


    /**
     * Функция замены параметров шаблона значениями переменных из GET запроса
     * @param string $template
     * @param array $params
     * @param boolean $use_globals
     * @param boolean $use_get
     * @return Ambigous <unknown, mixed>
     */
    function ReplaceTemplateGetParams($template, $params=array(), $use_globals=true, $use_get=false)
    {
        $ret = $template;
        $matches = null;
        if(preg_match_all("/\\[\\w+\\]/", $template, $matches) && $matches)
        {
            //var_dump($matches[0]);
            foreach($matches[0] as $value)
            {
                //var_dump($value);
                $key = substr($value, 1, -1);

                if(isset($params[$key]))
                {
                    $ret = str_replace($value, $params[$key], $ret);
                }
                elseif($use_get && isset($_GET[$key]))
                {
                    $ret = str_replace($value, $_GET[$key], $ret);
                }
                elseif($use_globals && isset($GLOBALS[$key]))
                {
                    $ret = str_replace($value, $GLOBALS[$key], $ret);
                }
                else
                {
                    $ret = str_replace($value, "", $ret);
                }
            }
            //var_dump($matches);
        }
        return $ret;

    }


    /**
     * Функция проверки того что выполнен запрос на Save
     * @param string $action_name
     * @param string $action_value
     * @return boolean
     */
    function CheckPost($action_name="action", $action_value="edit")
    {
        if(isset($_GET[$action_name]) && $_GET[$action_name] === $action_value
            && isset($_POST) && is_array($_POST) && count($_POST))
        {
            return true;
        }

        return false;
    }


    /**
     * Функция проверки значения в POST запросе
     * @param string $name
     * @param string $value
     * @return boolean
     */
    function CheckPostValue($name, $value)
    {
        if(isset($_POST[$name]) && $_POST[$name] == $value)
            return true;
        return false;
    }

    /**
     * Функция проверки того что в POST запросе есть необходимый параметр
     * @param string $_name
     * @return boolean
     */
    function CheckPostExists($_name="action")
    {
        if(isset($_POST) && isset($_POST[$_name]) && $_POST[$_name])
        {
            return true;
        }

        return false;
    }

    /**
     * Функция проверки того что выполнен запрос на Save
     * @param string $action_name
     * @param string $action_value
     * @return boolean
     */
    function CheckGet($action_name="action", $action_value="edit")
    {
        if(isset($_GET) && isset($_GET[$action_name]) && $_GET[$action_name] === $action_value)
        {
            return true;
        }

        return false;
    }


    /**
     * Функция проверки того что в запросе есть необходимый параметр
     * @param string $_name
     * @return boolean
     */
    function CheckGetExists($_name="action")
    {
        if(isset($_GET) && isset($_GET[$_name]) && $_GET[$_name])
        {
            return true;
        }

        return false;
    }


    function GetAdminTablePage($url)
    {
        $ret = "";

        if(!$url)
        {
            return $ret;
        }


        $basename = basename(CMain::QueryStringWithoutParamsEx($url));
        //var_dump($basename);
        $tables = array_keys($this->GetParams());
        foreach($tables as $table)
        {
            //echo "A:";
            //var_dump($this->GetAdminPageFile($table, $key));

            $table_page_keys = array_keys($this->GetTableParam($table, "admin"));
            foreach($table_page_keys as $table_page_key)
            {
                if($this->GetAdminPageFile($table, $table_page_key) == $basename)
                {
                    //var_dump($this->GetAdminPageFile($table, $key));
                    $ret = $table;
                    return $ret;
                }
            }
        }

        return $ret;
    }

    /**
     * Функция возвращает имя файла страницы по ее коду в административном интерфейсе
     * @param string $table
     * @param string $key
     * @return Ambiguous
     */
    function GetAdminPageFile($table, $key)
    {
        $ret = "";
        $tableParams = $this->GetTableParams($table);
        if(isset($tableParams["admin"][$key]["file"]))
        {
            $ret = $tableParams["admin"][$key]["file"];
        }
        unset($tableParams);
        return $ret;
    }


    /**
     * Функция возвращает имя файла страницы по ее коду в административном интерфейсе
     * @param string $table
     * @param string $key
     * @param array $params
     * @param boolean $use_globals
     * @param boolean $use_get
     * @return string
     */
    function GetAdminPageUrl($table, $key, $params=false, $use_globals=false, $use_get=false)
    {
        $ret = "";
        $tableParams = $this->GetTableParams($table);
        if(isset($tableParams["admin"][$key]["url_template"]))
        {
            $ret = $tableParams["admin"][$key]["url_template"];
            //var_dump($ret);
            //if($params || $use_globals || $use_get)
            $ret = $this->ReplaceTemplateGetParams($ret, $params, $use_globals, $use_get);
        }
        unset($tableParams);
        return $ret;
    }


    /**
     * Функция возвращает имя страницы по ее коду в административном интерфейсе
     * @param string $table
     * @param string $key
     * @return string
     */
    function GetAdminPageName($table, $key)
    {
        $ret = "";
        $tableParams = $this->GetTableParams($table);
        if(isset($tableParams["admin"][$key]["name"]))
        {
            $ret = $this->GetCurrentArrayLang($tableParams["admin"][$key]["name"]);
        }
        unset($tableParams);
        return $ret;
    }


    /**
     * Функция возвращает имя объекта по его мнемокоду
     * @param string $table
     * @return string
     */
    function GetAdminEntityName($table)
    {
        $ret = "";
        $tableParams = $this->GetTableParams($table);
        //var_dump($tableParams["name"]);
        $ret = $this->GetCurrentArrayLang($tableParams["name"]);
        unset($tableParams);
        return $ret;
    }

    /**
     * Функция возвращает имя поля таблицы
     * @param string $table
     * @param string $field
     * @return string
     */
    function GetAdminEntityFieldName($table, $field)
    {
        $ret = "";
        $tableFieldParams = $this->GetTableFieldParams($table, $field);
        //var_dump($tableParams["name"]);
        $ret = $this->GetCurrentArrayLang($tableFieldParams["name"]);
        unset($tableFieldParams);
        return $ret;
    }

    /**
     * Функция вовзвращает список сущностей выбранных для заданных действий админки
     * действия админки $arAdminDo = array("all", "edit", "list"), пустой массив  - все
     * @param array $arAdminDo
     * @param boolean $bCheckFieldsExists
     * @param boolean $bSuperadmin
     * @return multitype:unknown
     */
    function GetAdminEntities($arAdminDo = false, $bCheckFieldsExists=false, $bSuperadmin=false)
    {
        $ret = array();

        $params = $this->GetParams();
        foreach($params as $table => $tableParams)
        {
            $bNeed = false;
            if(is_array($arAdminDo) && is_array($tableParams["admin"]))
            {
                foreach ($arAdminDo as $do)
                {
                    if(array_key_exists($do, $tableParams["admin"]))
                    {
                        $bNeed = true;
                        break;
                    }
                }
            }
            else
            {
                $bNeed = true;
            }
            if($bCheckFieldsExists && (!is_array($tableParams["fields"]) || !count($tableParams["fields"])))
                $bNeed = false;

            if(!$bSuperadmin && isset($tableParams["superadmin"]) && $tableParams["superadmin"]=="true")
                $bNeed = false;

            if($bNeed)
                $ret[] = $table;
        }
        unset($params);

        return $ret;
    }


    /**
     * Функция выполняет дополнительные действия для заданной таблице в админке
     * @param string $table
     */
    function AdminActions($table)
    {
        if(!$this->is_admin)
        {
            // если не режим админки то просто выходим
            return;
        }

        $tableParams = $this->GetTableParams($table);
        if(isset($tableParams["admin"]["actions"]["file"]))
        {
            $filename = "";
            if(file_exists($filename))
            {
                include($filename);
            }
        }
    }

    /**
     *
     * @return string
     */
    function GetSessionLastQuery()
    {
        if(isset($_SESSION["session_last_query"]))
            return $_SESSION["session_last_query"];
        return "";
    }


    /**
     * Include entity params
     * Подключает параметры заданной сущности
     * @param string $entity_name
     */
    function IncludeEntity($entity_name)
    {
        global $entity, $db_forms;

        if(!strlen($entity_name))
        {
            return;
        }

        $entity = false;

        if(!is_array($db_forms))
        {
            $db_forms = array();
        }

        // системные конфигурация
        if(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY))
        {
            include($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY);
            if(isset($entity) && is_array($entity))
            {
                $db_forms = array_merge($db_forms, $entity);
            }
        }
        // конфигурация для сайта
        elseif(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY))
        {
            include($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY);
            if(isset($entity) && is_array($entity))
            {
                $db_forms = array_merge($db_forms, $entity);
            }

        }
        // конфигурация общая
        elseif(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY))
        {
            include($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES."/{$entity_name}/".FILENAME_ENTITY);
            if(isset($entity) && is_array($entity))
            {
                $db_forms = array_merge($db_forms, $entity);
            }
        }
    }

    /**
     * Include all entities params
     * Подключает параметры всех сущностей в сответствии с сортировкой
     * (файл sort.xml в каталоге сущности,
     * если файла нет то сущность добавляется в конец списка)
     */
    function IncludeAllEntities()
    {
        global $db_forms;
        $entity_name_array = array();
        $entity_noload_name_array = array();

        $sort_none = 100000;

        if(!file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES)
            || !file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES) )
        {
            return;
        }

        // Системные настройки
        $d = dir($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES);
        while (false !== ($entry = $d->read()))
        {
            if($entry == "." || $entry == "..")
            {
                continue;
            }
            if(!in_array($entry, $entity_name_array))
            {
                $sort_value = false;
                if(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES."/{$entry}/sort.xml"))
                {
                    $xmlDoc = new DOMDocument();
                    $xmlDoc->load($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_CONFIGURATION_SYSTEM."/".DIRNAME_ENTITIES."/{$entry}/sort.xml");
                    $x=$xmlDoc->getElementsByTagName('sort');
                    if($x->length && $x->item(0)->nodeValue)
                    {
                        $sort_value = intval($x->item(0)->nodeValue);
                    }
                    unset($x);
                    unset($xmlDoc);

                }
                if($sort_value > 0)
                {
                    if(!in_array($sort_value, array_keys($entity_name_array)))
                    {
                        $entity_name_array[$sort_value] = $entry;
                    }
                }
                elseif($sort_value < 0)
                {
                    if(!in_array($entry, $entity_noload_name_array))
                    {
                        $entity_noload_name_array[] = $entry;
                    }

                }
                else
                {
                    if($sort_value == 0)
                    {
                        $entity_name_array[$sort_none++] = $entry;
                    }
                }
            }
        }
        $d->close();
        unset($d);

        // Настройки для сайта
        if(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES ."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES))
        {

            $d = dir($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES);
            while (false !== ($entry = $d->read()))
            {
                if($entry == "." || $entry == "..")
                {
                    continue;
                }
                if(!in_array($entry, $entity_name_array))
                {
                    $sort_value = false;
                    if(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES."/{$entry}/sort.xml"))
                    {
                        $xmlDoc = new DOMDocument();
                        $xmlDoc->load($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_SITES."/".$_SERVER["SERVER_NAME"]."/".DIRNAME_ENTITIES."/{$entry}/sort.xml");
                        $x=$xmlDoc->getElementsByTagName('sort');
                        if($x->length && $x->item(0)->nodeValue)
                        {
                            $sort_value = intval($x->item(0)->nodeValue);
                        }
                        unset($x);
                        unset($xmlDoc);

                    }
                    if($sort_value > 0)
                    {
                        if(!in_array($sort_value, array_keys($entity_name_array)))
                        {
                            $entity_name_array[$sort_value] = $entry;
                        }
                    }
                    elseif($sort_value < 0)
                    {
                        if(!in_array($entry, $entity_noload_name_array))
                        {
                            $entity_noload_name_array[] = $entry;
                        }

                    }
                    else
                    {
                        if($sort_value == 0)
                        {
                            $entity_name_array[$sort_none++] = $entry;
                        }
                    }

                }
            }
            $d->close();
            unset($d);        }


        // Общие настройки
        $d = dir($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES);
        while (false !== ($entry = $d->read()))
        {
            if($entry == "." || $entry == "..")
            {
                continue;
            }
            if(!in_array($entry, $entity_name_array))
            {
                $sort_value = false;
                if(file_exists($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES."/{$entry}/sort.xml"))
                {
                    $xmlDoc = new DOMDocument();
                    $xmlDoc->load($this->root.DIRNAME_CONFIGURATION."/".DIRNAME_ENTITIES."/{$entry}/sort.xml");
                    $x=$xmlDoc->getElementsByTagName('sort');
                    if($x->length && $x->item(0)->nodeValue)
                    {
                        $sort_value = intval($x->item(0)->nodeValue);
                    }
                    unset($x);
                    unset($xmlDoc);

                }
                if($sort_value > 0)
                {
                    if(!in_array($sort_value, array_keys($entity_name_array)))
                    {
                        $entity_name_array[$sort_value] = $entry;
                    }
                }
                elseif($sort_value < 0)
                {
                    if(!in_array($entry, $entity_noload_name_array))
                    {
                        $entity_noload_name_array[] = $entry;
                    }

                }
                else
                {
                    if($sort_value == 0)
                    {
                        $entity_name_array[$sort_none++] = $entry;
                    }
                }

            }
        }
        $d->close();
        unset($d);


        ksort($entity_name_array);
        //error_log("entity_name_array:".print_r($entity_name_array,true));

        $db_forms = array();

        //var_dump($entity_name_array);


        foreach ($entity_name_array as $entity_name)
        {
            if(!in_array($entity_name, $entity_noload_name_array))
            {
                $this->IncludeEntity($entity_name);
            }
        }

        //var_dump($db_forms);
    }


    function AdminShowBoxTemplate($filename, $template, $box_id_ex, $box_header_ex, $box_content_ex)
    {
        global $MAIN;
        $ret = "";

        global $box_id, $box_header, $box_content;
        $box_id = $box_id_ex;
        $box_header = $box_header_ex;
        $box_content = $box_content_ex;
        $ret = $MAIN->ShowTemplate($filename, $template, "page", "box.php");

        return $ret;
    }

    /**
     * Выводит бокс с заданным именем, заголовком и контентом
     * @param string $box_name
     * @param string $box_caption
     * @param string $box_content
     */
    function AdminShowBox($box_name, $box_caption, $box_content)
    {
        ?>
    <div class="box">
        <div class="box-header"><a onclick='xajax_show_hide_change("<?=$box_name?>")'><span id="<?=$box_name?>_status">-</span> <?=$box_caption?></a></div>
        <div id="<?=$box_name?>" class="box-content"><?=$box_content?></div>
    </div>
    <script type="text/javascript">
        <!--
        xajax_show_hide_current("<?=$box_name?>");
        //-->
    </script>
    <?
    }

    /**
     * Выводит бокс с заданным именем, заголовком, контент берется как инклуд из заданного файла
     * @param string $box_name
     * @param string $box_caption
     * @param string $box_include_file
     */
    function AdminShowBoxInclude($box_name, $box_caption, $box_include_file)
    {
        ?>
    <div class="box">
        <div class="box-header"><a onclick='xajax_show_hide_change("<?=$box_name?>")'><span id="<?=$box_name?>_status">-</span> <?=$box_caption?></a></div>
        <div id="<?=$box_name?>" class="box-content"><?CMain::IncludeFile($box_include_file, true);?></div>
    </div>
    <script type="text/javascript">
        <!--
        xajax_show_hide_current("<?=$box_name?>");
        //-->
    </script>
    <?
    }

}


?>