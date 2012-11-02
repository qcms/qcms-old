<?php
$root = "../";
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

global $MAIN;

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => true,
        "need_session_db"=>false,
        "need_xajax" => false,
        "is_admin"=>true
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init("mysqldump_all");
if(!$MAIN->adminuser->IsSuperadmin())
    die("Wrong user!");


if($MAIN->CheckGet("action", "test"))
{
    $db = new CDatabase();
    $link = false;
    $db_selected = false;
    $error = false;
    if($db->CheckConnectionDb($link, $db_selected, $error))
    {
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Проверка подключения (".DATABASE_USER."@".DATABASE_SERVER.") к БД (".DATABASE_NAME.") выполнена успешно!");
    }
    else
    {
        if(!$link)
        {
            s_add_error($MAIN->QueryStringWithoutParams(), "Ошибка подключения к серверу БД! ({$error})");
        }
        else
        {
            s_add_error($MAIN->QueryStringWithoutParams(), "Ошибка выбора БД! ({$error})");

        }
    }
    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "backup"))
{
    $filename = false;
    $db = new CDatabase();
    $filename = "db-backup-".DATABASE_NAME."-".date("Y-m-d_His").".sql";
    $filename = $db->BackupTables($filename);
    unset($db);

    if($filename)
    {
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Резервная копия выполнена успешно!<br />Файл '{$filename}' успешно добавлен.");
        //var_dump(s_get_vars_array($MAIN->QueryStringWithoutParams()));
    }
    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "download") && $MAIN->CheckGetExists("f"))
{
    $entry = $_GET["f"];
    if(preg_match('/^[\w\d-]+\.sql$/is', $entry))
    {
        // We'll be outputting a PDF
        header('Content-type: application/octet-stream');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$entry.'"');
        // The PDF source is in original.pdf
        readfile($MAIN->root.DIRNAME_DATABASE."/".$entry);
    }
    //header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "delete") && $MAIN->CheckGetExists("f"))
{
    $entry = $_GET["f"];
    if(preg_match('/^[\w\d-]+\.sql$/is', $entry))
    {
        unlink($MAIN->root.DIRNAME_DATABASE."/{$entry}");
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Файл '{$entry}' успешно удален.");
    }

    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}


$admin_current_name_custom = $MAIN->GetAdminPageName("mysqldump_all", "all");

$MAIN->ShowTemplate(__FILE__, "", "page", "page.php", true);

s_reset_vars($MAIN->QueryStringWithoutParams());
s_reset_errors($MAIN->QueryStringWithoutParams());


/**
 * Функция отображения страницы административного интерфейса
 * возвращает контекст страницы административного интерфейса
 * @return string
 */
function admin_show_content()
{
    global $MAIN;
    $ret = "";



    $messages = s_get_vars_array($MAIN->QueryStringWithoutParams());
    if(count($messages))
    {
        foreach ($messages as $message)
        {
            $ret .= <<<EOT
        <div class="f-message">
            <span class="close"></span>
            {$message}
        </div>
EOT;
        }
    }

    $errors = s_get_errors_array($MAIN->QueryStringWithoutParams());
    if(count($errors))
    {

        foreach ($errors as $message)
        {
            $ret .= <<<EOT
        <div class="f-message-error">
            <span class="close"></span>
            {$message}
        </div>

EOT;
        }
    }


    $query_string = $MAIN->QueryStringWithoutParams();
    $ret .= <<<EOT
<br />
<form action="" method="get">
    <input name="action" type="hidden" value="test"  />
    <input type="submit" class="f-bu" value="Тест соединения с БД" />
</form>

<br />

<form action="" method="get">
    <input name="action" type="hidden" value="backup" />
    <input type="submit" class="f-bu" value="Выполнить резервную копию БД" />
</form>

<script language="JavaScript">
    <!--//
    function submit_delete(f)
    {
        if(window.confirm("Подтвердите удаление объекта?"))
        {
            document.forms["deleteform"].f.value = f;
            document.forms["deleteform"].submit();
        }
    }
    //-->
</script>

<form name="deleteform" id="deleteform" method="get" action="{$query_string}">
    <input type="Hidden" name="action" value="delete">
    <input type="Hidden" name="f" id="f" value="[id]">
</form>

EOT;



    $list = new CList();

    $d = dir($MAIN->root.DIRNAME_DATABASE."/");

    $i = 0;
    while (false !== ($entry = $d->read()))
    {

        if(preg_match('/^[\w\d-\.]+\.sql$/is', $entry))
        {
            $i++;
            $list_item = new CListItem($i, $entry);
            $list->Add($list_item);
//            echo $_template_list_tr_header;
//            echo str_replace('[item]', $entry, $_template_list_td_item);
//            echo str_replace('[item]', '<a href="'.$MAIN->QueryStringWithoutParams().'?action=download&f='.$entry.'">Скачать</a> <a href="#" onclick="submit_delete(\''.$entry.'\');return false;">Удалить</a>', $_template_list_td_item);
//            echo $_template_list_tr_footer;
        }
    }
    $d->close();

    $ret .= <<<EOT
<table>
    <tr>
        <th>Имя файла</th>
        <th style="text-align:right;" width="30%">Действия</th>
    <tr>
EOT;

    if($list->GetCount()>0)
    {
        foreach($list->items as $list_item_id => $list_item)
        {

            //$list_item->name;

            $ret .= <<<EOT
    <tr>
        <td>{$list_item->name}</td>
        <td style="text-align:right;"><a href="{$query_string}?action=download&f={$list_item->name}">Скачать</a> <a href="#" onclick="submit_delete('{$list_item->name}');return false;">Удалить</a></td>
    <tr>
EOT;

        }
    }



    $ret .= <<<EOT
</table>
EOT;

    //$ret .= print_r($list, true);

//    echo $_template_list_footer;


    return $ret;
}
exit;

$root = "../";
include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

//error_log('mysqldump_all.php: $_SERVER["REQUEST_URI"]: '.$_SERVER["REQUEST_URI"]);

$MAIN = new CMain($root,
    array(
        "need_config" => false,
        "need_session" => true,
        "need_session_db"=>false,
        "need_xajax" => false,
        "is_admin"=>true,
    )
);

$MAIN->HeaderIncludes();
$MAIN->Init("mysqldump_all");

if(!$MAIN->adminuser->IsSuperadmin())
    die("Wrong user!");
//include($root . "includes/admin.inc.php");

if($MAIN->CheckGet("action", "test"))
{
    $db = new CDatabase();
    $link = false;
    $db_selected = false;
    $error = false;
    if($db->CheckConnectionDb($link, $db_selected, $error))
    {
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Проверка подключения (".DATABASE_USER."@".DATABASE_SERVER.") к БД (".DATABASE_NAME.") выполнена успешно!");
    }
    else
    {
        if(!$link)
        {
            s_add_error($MAIN->QueryStringWithoutParams(), "Ошибка подключения к серверу БД! ({$error})");
        }
        else
        {
            s_add_error($MAIN->QueryStringWithoutParams(), "Ошибка выбора БД! ({$error})");

        }
    }
    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "backup"))
{
    $filename = false;
    $db = new CDatabase();
    $filename = "db-backup-".DATABASE_NAME."-".date("Y-m-d_His").".sql";
    $filename = $db->BackupTables($filename);
    unset($db);

    if($filename)
    {
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Резервная копия выполнена успешно!<br />Файл '{$filename}' успешно добавлен.");
        //var_dump(s_get_vars_array($MAIN->QueryStringWithoutParams()));
    }
    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "download") && $MAIN->CheckGetExists("f"))
{
    $entry = $_GET["f"];
    if(preg_match('/^[\w\d-]+\.sql$/is', $entry))
    {
        // We'll be outputting a PDF
        header('Content-type: application/octet-stream');
        // It will be called downloaded.pdf
        header('Content-Disposition: attachment; filename="'.$entry.'"');
        // The PDF source is in original.pdf
        readfile($MAIN->root.DIRNAME_DATABASE."/".$entry);
    }
    //header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

if($MAIN->CheckGet("action", "delete") && $MAIN->CheckGetExists("f"))
{
    $entry = $_GET["f"];
    if(preg_match('/^[\w\d-]+\.sql$/is', $entry))
    {
        unlink($MAIN->root.DIRNAME_DATABASE."/{$entry}");
        s_add_var($MAIN->QueryStringWithoutParams(), "message", "Файл '{$entry}' успешно удален.");
    }

    header("Location:".$MAIN->QueryStringWithoutParams());
    exit;
}

$MAIN->IncludeModule("header.inc.php", true);

$messages = s_get_vars_array($MAIN->QueryStringWithoutParams());
if(count($messages))
{
    foreach ($messages as $message)
    {
        ?>
    <div class="message"><?=$message?></div>
    <?
    }
}

$errors = s_get_errors_array($MAIN->QueryStringWithoutParams());
if(count($errors))
{

    foreach ($errors as $message)
    {
        ?>
    <div class="error"><?=$message?></div>
    <?
    }
}
?>


<form action="" method="get">
    <input name="action" type="hidden" value="test"  />
    <input type="submit" value="Тест соединения с БД" />
</form>

<br />

<form action="" method="get">
    <input name="action" type="hidden" value="backup" />
    <input type="submit" value="Выполнить резервную копию БД" />
</form>

<br />
<br />
<script language="JavaScript">
    <!--//
    function submit_delete(f)
    {
        if(window.confirm("Подтвердите удаление объекта?"))
        {
            document.forms["deleteform"].f.value = f;
            document.forms["deleteform"].submit();
        }
    }
    //-->
</script>
<form name="deleteform" id="deleteform" method="get" action="<?=$MAIN->QueryStringWithoutParams()?>">
    <input type="Hidden" name="action" value="delete">
    <input type="Hidden" name="f" id="f" value="[id]">
</form>


<?
$templatePath = $MAIN->GetTemplatePath("default", "vieweditlist", "default", $MAIN->is_admin);
if(file_exists($templatePath))
{
    include($templatePath);
}

$d = dir($MAIN->root.DIRNAME_DATABASE."/");
//echo "Handle: " . $d->handle . "<br />";
//echo "Path: " . $d->path . "<br />";

echo $_template_list_header;
echo $_template_list_tr_header;
echo str_replace('[item]', "Имя файла", $_template_list_th_item);
echo str_replace('[item]', "Действия", $_template_list_th_item);
echo $_template_list_tr_footer;

while (false !== ($entry = $d->read()))
{

    if(preg_match('/^[\w\d-]+\.sql$/is', $entry))
    {
        echo $_template_list_tr_header;
        echo str_replace('[item]', $entry, $_template_list_td_item);
        echo str_replace('[item]', '<a href="'.$MAIN->QueryStringWithoutParams().'?action=download&f='.$entry.'">Скачать</a> <a href="#" onclick="submit_delete(\''.$entry.'\');return false;">Удалить</a>', $_template_list_td_item);
        //echo $entry."<br />";
        echo $_template_list_tr_footer;
    }
}
$d->close();

echo $_template_list_footer;
?>
<br>
<br>
<?
s_reset_vars($MAIN->QueryStringWithoutParams());
s_reset_errors($MAIN->QueryStringWithoutParams());

$MAIN->IncludeModule("footer.inc.php", true);
?>