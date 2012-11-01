<?
/*
 * Файл основной функциональности административного интерфейса
 * */



/**
 * Функция возвращает панель выбора языков в админке
 * @return string
 */
function admin_show_lang()
{
    global $MAIN;

    $ret = "";

    $ret .= <<<EOT
            <div class="g-row p5px f-buttons">
EOT;

    global $languages;
    if(is_array($languages) && count($languages)>1)
    {

        foreach($languages as $lang)
        {
            $backurl = urlencode($_SERVER["REQUEST_URI"]);
            if($lang["code"]===CMain::GetLangCode())
            {
                $ret .= <<<EOT
                <span class="f-bu f-bu-success">{$lang["name"]}</span>
EOT;

            }
            else
            {
                $ret .= <<<EOT
                <a class="f-bu" href="{$MAIN->root}lang.php?id={$lang["code"]}&backurl={$backurl}">{$lang["name"]}</a>
EOT;
            }
        }
    }

    $ret .= <<<EOT
            </div>
EOT;

    return $ret;
}

/**
 * Функция возвращает хлебные крошки админки
 * @return string
 */
function admin_show_breadcrumbs()
{
    global $MAIN;
    $ret = "";

    $ret .= <<<EOT
            <ul class="f-breadcrumbs">
EOT;

    $VN_ADMIN_INDEX = VN_ADMIN_INDEX;

    $MAIN->LoadLangMessages(__FILE__);
    $ADMIN_INDEX = $MAIN->GetLangMessage("ADMIN_INDEX");


    $page_name = $MAIN->GetLangMessage("ADMIN_INDEX");

    $ret .= <<<EOT
                <li><a href="{$VN_ADMIN_INDEX}">{$page_name}</a></li>
EOT;


    $skip_array = array();

    // параметры текущего файла
    $params = $MAIN->GetParams();
    $admin_current_table = null;  // текущая таблица
    $admin_current_kind = null;   // текущий вид страницы в админке
    $admin_current_name = null;   // название текущей страницы
    //$admin_current_file = null;   // название файла
    $basename = basename(preg_replace('/\?.*$/ims', '', $_SERVER["REQUEST_URI"]));

    $VN_ADMIN = VN_ADMIN;

    // найдем текущий файл
    $found = false;
    foreach($params as $key => $value)
    {
        if(isset($value["admin"])
            && is_array($value["admin"])
        )
        {
            foreach($value["admin"] as $key1 => $value1)
            {
                //var_dump($value1);
                if(isset($value1["file"])
                    && $basename == $value1["file"]
                    && isset($value1["name"])
                    && is_array($value1["name"])
                )
                {

                    $admin_current_kind = $key1;
                    $admin_current_table = $key;
                    $admin_current_name = CMain::GetCurrentArrayLang($value1["name"]);
                    $found = true;
                    break;
                }
            }
            if($found)
            {
                break;
            }
        }
    }



    // промежуточная навигация
    // определяется массивом вида $breadcrumb_parents = array("table" => array("page" => "all"|"edit"|"etc", "vars" => "id=123") )
    global $breadcrumb_parents;
    if(isset($breadcrumb_parents) && is_array($breadcrumb_parents) && count($breadcrumb_parents))
    {
        //var_dump($breadcrumb_parents);
        foreach($breadcrumb_parents as $value)
        {
            $vars = ($value["vars"]?('?'.$value["vars"]):"");
            $page_name = CMain::GetCurrentArrayLang($params[$value["table"]]["admin"][$value["page"]]["name"]);
            $ret .= <<<EOT
                <li><a href="{$VN_ADMIN}/{$params[$value["table"]]["admin"][$value["page"]]["file"]}{$vars}">{$page_name}</a></li>
EOT;
        }
    }

    // специальная

    // обычная для страниц вида name_all.php и name_edit.php
    //var_dump($admin_current_kind);
    if($admin_current_kind != "all"
        && isset($params[$admin_current_table]["admin"]["all"]["url_template"])
        && isset($params[$admin_current_table]["admin"]["all"]["name"])
        && is_array($params[$admin_current_table]["admin"]["all"]["name"])
    )
    {
        $url_template = $MAIN->ReplaceTemplateGetParams($params[$admin_current_table]["admin"]["all"]["url_template"]);
        $page_name = CMain::GetCurrentArrayLang($params[$admin_current_table]["admin"]["all"]["name"]);
        $ret .= <<<EOT
                <li><a href="{$VN_ADMIN}/{$url_template}">{$page_name}</a></li>
EOT;

        //$MAIN->ReplaceTemplateGetParams($params[$admin_current_table]["admin"]["edit"]["url_template"]);
        if($admin_current_kind != "edit"
            && isset($params[$admin_current_table]["admin"]["edit"]["url_template"])
            && isset($params[$admin_current_table]["admin"]["edit"]["name"])
            && is_array($params[$admin_current_table]["admin"]["edit"]["name"])
        )
        {
            //echo ' &gt;&gt; <a href="'.VN_ADMIN . "/" .$MAIN->ReplaceTemplateGetParams($params[$admin_current_table]["admin"]["edit"]["url_template"]).'">'.CMain::GetCurrentArrayLang($params[$admin_current_table]["admin"]["edit"]["name"]).'</a>';
            $url_template = $MAIN->ReplaceTemplateGetParams($params[$admin_current_table]["admin"]["edit"]["url_template"]);
            $page_name = CMain::GetCurrentArrayLang($params[$admin_current_table]["admin"]["edit"]["name"]);
            $ret .= <<<EOT
                <li><a href="{$VN_ADMIN}/{$url_template}">{$page_name}</a></li>
EOT;
        }
    }

    // Page name
    $page_name = "";
    global $admin_current_name_custom;
    if($admin_current_name)
    {
        $page_name = $admin_current_name;
    }
    elseif(isset($admin_current_name_custom) && strlen($admin_current_name_custom))
    {
        $page_name = $admin_current_name_custom;
    }

    // if entity is set and entity name is not empty show entity name
    if(isset($entity) && is_a($entity, "CEntity") && $entity->GetHeader($entity->table."_name"))
    {
        $page_name = " \"" .$entity->GetHeader($entity->table."_name") ."\"";
    }

    $ret .= <<<EOT
                <li class="cur">{$page_name}</li>
EOT;

    //    $ret = <<<EOT
    //            <ul class="f-breadcrumbs">
    //                <li><a href="#">Главная</a></li>
    //                <li><a href="#">Категория</a></li>
    //                <li><a href="#">Вложенная категория</a></li>
    //                <li><a href="#">Вложенная категория</a></li>
    //                <li><a href="#">Вложенная категория</a></li>
    //                <li class="cur">Продукт</li>
    //            </ul><!-- f-breadcrumb -->
    //EOT;

    $ret .= <<<EOT
            </ul><!-- f-breadcrumb -->
EOT;

    return $ret;

}


/**
 * Функция возвращает форму авторизации пользователя в админке
 * @return string
 */
function admin_show_auth()
{
    global $MAIN;
    $MAIN->LoadLangMessages(__FILE__);
    $ADMIN_SIGNIN = $MAIN->GetLangMessage("ADMIN_SIGNIN");
    $ADMIN_SIGNOUT = $MAIN->GetLangMessage("ADMIN_SIGNOUT");
    $ret = "";

    if($MAIN->is_admin_non_auth === true  || $MAIN->AdminIsAuth() !== true)
    {
        $VN_ADMIN_SIGNIN = VN_ADMIN_SIGNIN;
        $backurl = "";
        if(isset($_REQUEST["backurl"]))
            $backurl = "?backurl=".urlencode($_REQUEST["backurl"]);
        $ret .= <<<EOT
            <div class="g-row p5px">
                <div class="float-right"><a href="{$VN_ADMIN_SIGNIN}{$backurl}">{$ADMIN_SIGNIN}</a></div>
            </div>
EOT;
    }
    else
    {
        $VN_ADMIN_SIGNOUT = VN_ADMIN_SIGNOUT;
        $ret .= <<<EOT
            <div class="g-row p5px">
                <div class="float-left">{$MAIN->adminuser->login}:</div>
                <div class="float-right"><a href="{$VN_ADMIN_SIGNOUT}">{$ADMIN_SIGNOUT}</a></div>
            </div>
EOT;
    }


    //    $ret = <<<EOT
    //            <div class="g-row p5px">
    //                <div class="float-left">AdminLogin:</div>
    //                <div class="float-right"><a href="#">выход</a></div>
    //            </div>
    //EOT;

    return $ret;

}


/**
 * Функция возвращает левое меню админки
 * @return string
 */
function admin_show_left_menu()
{
    global $MAIN;
    $ret = "";

    $ret .= <<<EOT
            <div class="g-row">
                <ul class="f-nav-list">
EOT;

    if($MAIN->AdminIsAuth())
    {
        $params = $MAIN->GetParams();

        foreach($params as $key => $value)
        {
            if(isset($value["admin"]["all"]["url_template"])
                && isset($value["admin"]["all"]["name"])
                && is_array($value["admin"]["all"]["name"])
            )
            {
                if(isset($value["superadmin"]) && $value["superadmin"] == "true"
                    && !$MAIN->adminuser->IsSuperadmin())
                {
                    continue;
                }

                $page_name = CMain::GetCurrentArrayLang($value["admin"]["all"]["name"]);
                $page_url = $MAIN->GetAdminPageUrl($key, "all");
                //<li><a href="{$value["admin"]["all"]["url_template"]}">{$page_name}</a></li>
                //<li class="active"><a href="{$value["admin"]["all"]["url_template"]}">{$page_name}</a></li>

//                var_dump($page_url);
//                var_dump(basename($_SERVER["REQUEST_URI"]));


                $table = $MAIN->GetAdminTablePage($_SERVER["REQUEST_URI"]);
                //var_dump($table);
                if($MAIN->GetAdminPageFile($table, "all") == $page_url)
                {
                    $ret .= <<<EOT
                    <li class="active"><a href="{$page_url}">{$page_name}</a></li>
EOT;
                }
                else
                {
                    $ret .= <<<EOT
                    <li><a href="{$page_url}">{$page_name}</a></li>
EOT;

                }

            }
        }
    }

    $ret .= <<<EOT
                </ul><!-- f-nav-list -->
            </div>
EOT;



    //    $ret = <<<EOT
    //            <div class="g-row">
    //                <ul class="f-nav-list">
    //                    <li><a href="#">Первый пункт</a></li>
    //                    <li class="active"><a href="#">Второй активный</a></li>
    //                    <li><a href="#">Третий</a></li>
    //                    <li><a href="#">Последний пункт</a></li>
    //                </ul><!-- f-nav-list -->
    //            </div>
    //EOT;
    return $ret;

}


/**
 * Функция возвращает подвал админки
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


?>
