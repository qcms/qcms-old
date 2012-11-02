<?php
/*
 * Файл пользовательских функций XAJAX
 * подключается если в CMain параметр $MAIN->need_xajax==true
 * */

global $xajax;

//// регистрация XAJAX функции
// $xajax->register(XAJAX_FUNCTION, "some_function_name");
///**
// * Функция обработки XAJAX запроса
// * может быть вызвана в JavaScript как "xajax_some_function_name(some_pram)"
// * @param unknown_type $some_param
// * @return xajaxResponse
// */
//function some_function_name($some_param)
//{
//	$response = new xajaxResponse();
//	
//	return $response;
//}

// регистрация XAJAX функциий
$xajax->register(XAJAX_FUNCTION, "show_installer_step");
$xajax->register(XAJAX_FUNCTION, "prev_step");
$xajax->register(XAJAX_FUNCTION, "next_step");
$xajax->register(XAJAX_FUNCTION, "next_checkbox_change");

$xajax->register(XAJAX_FUNCTION, "next_prev_show");


/**
 * @return xajaxResponse
 */
function next_prev_show()
{
    global $installator_step;
    $response = new xajaxResponse();

    $script = "";

    if($installator_step == 1)
    {
        // enable next
        $script .= <<<EOT
        if($('#next_step_id').hasClass("disabled")){
            $('#next_step_id').removeClass("disabled");
            $('#next_step_id').removeAttr('disabled');
        }
EOT;
        // disable prev
        $script .= <<<EOT
        if(!$('#prev_step_id').hasClass("disabled")){
            $('#prev_step_id').attr("disabled", "true");
            $('#prev_step_id').addClass("disabled");
        }
EOT;
    }
    elseif($installator_step == STEP_MAX)
    {
        // disable next
        $script .= <<<EOT
        if(!$('#next_step_id').hasClass("disabled")){
            $('#next_step_id').attr("disabled", "true");
            $('#next_step_id').addClass("disabled");
        }
EOT;
        // enable prev
        $script .= <<<EOT
        if($('#prev_step_id').hasClass("disabled")){
            $('#prev_step_id').removeClass("disabled");
            $('#prev_step_id').removeAttr('disabled');
        }
EOT;

    }
    else
    {
        // enable next
        $script .= <<<EOT
        if($('#next_step_id').hasClass("disabled")){
            $('#next_step_id').removeClass("disabled");
            $('#next_step_id').removeAttr('disabled');
        }
EOT;
        // enable prev
        $script .= <<<EOT
        if($('#prev_step_id').hasClass("disabled")){
            $('#prev_step_id').removeClass("disabled");
            $('#prev_step_id').removeAttr('disabled');
        }
EOT;

    }

    if($script)
    {
        $response->script($script);
    }

    return $response;
}


/**
 * Реакция действия на next_checkbox
 * next_checkbox используется для пропуска или принятия текущего действия
 * @param xajaxResponse $response
 */
function next_checkbox_change()
{
    $response = new xajaxResponse();
    //$response->alert("next_checkbox_change");



    $script = <<<EOT
    //alert('aaa');
    //alert($('#next_checkbox_id').is(':checked'));
    if($('#next_checkbox_id').is(':checked')){
        //alert('aaa');
        if($('#next_step_id').hasClass("disabled")){
            $('#next_step_id').removeClass("disabled");
            $('#next_step_id').removeAttr('disabled');
        }
    }
    else{
        if(!$('#next_step_id').hasClass("disabled")){
            $('#next_step_id').attr("disabled", "true");
            $('#next_step_id').addClass("disabled");
        }
    }

EOT;

    $response->script($script);
    return $response;
}

/**
 * @return xajaxResponse
 */
function prev_step()
{
    $response = new xajaxResponse();
    $response->alert("prev_step");
    return $response;
}

/**
 * @return xajaxResponse
 */
function next_step()
{
    global $MAIN,$installator_step, $installator_steps_done, $query_string;
    $response = new xajaxResponse();
    //$response->alert("next_step");
    if(isset($installator_step) && $installator_step<STEP_MAX)
    {
        if(!in_array($installator_step, $installator_steps_done))
        {
            $installator_steps_done[] = $installator_step;
            s_add_var($query_string,"installator_steps_done", $installator_steps_done);
        }
        $installator_step++;
        s_add_var($query_string,"installator_step", $installator_step);
        $response->script("xajax_show_installer_step({$installator_step});");

    }
    return $response;
}



/**
 * Функция отображает модуль инсталлятора
 * @param xajaxResponse $response
 */
function show_installer_step($installator_step)
{
    global $MAIN, $steps;
    $response = new xajaxResponse();

    $response->alert('show_installer_step');
    if(isset($steps[$installator_step]))
    {
        $installator_module =$steps[$installator_step];

        $script = <<<EOT
$(window).ready(function(){
    $("#installer_content").load('{$installator_module}');
});
EOT;
        $response->script($script);
    }

    return $response;
}







?>