<?php
//-----------------------------------------------------------------------------
// Инсталятор QCMS
//-----------------------------------------------------------------------------
$root = "../installer/";

/**
 * признак запуска инсталлятора
 */
define("QCMS_INSTALLER", "1");
/**
 * директория с архивом файлов для инсталляции
 */
define("DIRNAME_Z", "z");
/**
 * директория инсталлятора
 */
define("DIRNAME_INSTALLER", "installer");
/**
 * количество шагов инсталлятора
 */
define("STEP_MAX", 6);

include_once($root  ."/configuration/configuration.inc.php");
include_once($root  ."/includes/main_class.inc.php");

global $MAIN, $xajax;

$MAIN = new CMain($root,
    array(
        "is_admin"=>false,
        "need_session_db"=>false,
        "need_xajax" => true,
        "need_config" => false,
        "need_database" => false,
    )
);

$MAIN->HeaderIncludes();
$MAIN->Init();

$query_string = $MAIN->QueryStringWithoutParams();

$installator_steps_done = s_get_var($query_string,"installator_steps_done");
if(!is_array($installator_steps_done))
{
    $installator_steps_done = array();
    s_add_var($query_string,"installator_steps_done", $installator_steps_done);
}
$installator_step = s_get_var($query_string,"installator_step");
if(!$installator_step)
{
    $installator_step = "1";
    s_add_var($query_string,"installator_step", $installator_step);
}


// шаги инсталлятора
$steps = array(
    "1" => "license.php",
    "2" => "system.requirments.php",
    "3" => "unzip.files.php",
    "4" => "install.db.php",
    "5" => "delete.installer.php",
    "6" => "done.php",
);

$step_names = array(
    "1" => "Лицензионное соглашение",
    "2" => "Системные требования",
    "3" => "Распаковка",
    "4" => "Установка базы данных",
    "5" => "Удаление файлов инсталлятора",
    "6" => "Завершение установки",
);
$step_names_short = array(
    "1" => "Шаг 1",
    "2" => "Шаг 2",
    "3" => "Шаг 3",
    "4" => "Шаг 4",
    "5" => "Шаг 5",
    "6" => "Шаг 6",
);


// id шагов, которые можно вызвать отдельно
$step_ids = array(
    //"license" => $steps["1"],
    "system.requirments" => $steps["2"],
    "unzip.files" => $steps["3"],
    "install.db" => $steps["4"],
    //"delete.installer" => $steps["5"],
    //"done" => $steps["6"],
);

$admin_current_name_custom = "QCMS installer";
//s_add_message($query_string, "Тестовое сообщение"); // добавление сообщения
//s_add_error($query_string, "Тестовая ошибка"); // добавление ошибки

$xajax->processRequest();

global $xajax_js;
$xajax_js = $xajax->getJavascript('/xajax');
$MAIN->ShowTemplate(__FILE__, "", "installer", "page.php", true);
s_reset_messages($query_string);
s_reset_errors($query_string);


/**
 * Функция отображения страницы административного интерфейса
 * возвращает контекст страницы административного интерфейса
 * @return string
 */
function admin_show_content()
{
    global $MAIN, $entity, $installator_step, $xajax, $query_string;

    $ret = "";

    $ret .= <<<EOT
<div id="installer_content">Тут будет содержимое</div>
<script type="text/javascript">
    xajax_show_installer_step({$installator_step});
</script>

EOT;


    return $ret;
}

/**
 * @return string
 */
function admin_wizard_steps()
{
    global $steps,
           $step_names,
           $step_names_short,
           $step_ids,
           $installator_steps_done,
           $installator_step,
           $query_string;

    $ret = "";
    //$ret .= "Тут будет отображение списка шагов исталляции";
    $ret .= <<<EOT
<div class="f-buttons">
EOT;


    foreach($steps as $key=>$value)
    {
        if(in_array($key,$installator_steps_done) || $key == $installator_step)
        {
            $ret .= <<<EOT
    <span class="f-bu" href="{$query_string}?step={$key}" title="{$step_names[$key]}">{$step_names_short[$key]}</span>
EOT;
        }
        else
        {
            $ret .= <<<EOT
    <span class="f-bu" disabled="disabled" href="{$query_string}?step={$key}" title="{$step_names[$key]}">{$step_names_short[$key]}</span>
EOT;
        }

    }
    $ret .= <<<EOT
</div><!-- f-buttons -->
EOT;

    return $ret;
}

/**
 * @return string
 */
function admin_show_next_prev()
{
    $ret = "";

    global $steps, $installator_step;

    $ret .= <<<EOT
<p class="f-buttons">
EOT;



    if($installator_step < 2)
    {
        $ret .= <<<EOT
    <a class="f-bu" onclick="xajax_prev_step(); return false;" id="prev_step_id" disabled="true">Предыдущий шаг</a>
    <a class="f-bu" onclick="xajax_next_step(); return false;" id="next_step_id" disabled="true">Следующий шаг</a>
EOT;

    }
    $ret .= <<<EOT
</p>
<script type="text/javascript">
    xajax_next_prev_show();
</script>
EOT;

    return $ret;
}
/**
 * @return string
 */
function admin_show_footer()
{
    $version = CMain::Version();
    $year = date("Y");
    $ret = <<<EOT
<div>
    &copy; Information Ways - {$version} - {$year}
</div>
EOT;
    return $ret;
}

/**
 * @return string
 */
function admin_show_messages()
{
    $ret = "";
    global $MAIN, $query_string;

    $messages = s_get_messages_array($query_string);
    if(is_array($messages) && count($messages))
    {
        foreach($messages as $message)
        {
            $ret .= <<<EOT
        <div class="f-message">
            <span class="close"></span>
            {$message}
        </div>
EOT;

        }
    }

    $errors = s_get_errors_array($query_string);
    if(count($errors))
    {

        foreach ($errors as $message)
        {
            $ret .= <<<EOT
        <div class="f-message f-message-error">
            <span class="close"></span>
            {$message}
        </div>

EOT;
        }
    }

    return $ret;
}
?>