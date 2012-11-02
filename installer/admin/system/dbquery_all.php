<?
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
        "is_admin"=>true,
    )
);
$MAIN->HeaderIncludes();
$MAIN->Init("dbquery_all");

$admin_current_name_custom = $MAIN->GetAdminPageName("dbquery_all", "all");

$MAIN->ShowTemplate(__FILE__, "", "page", "page.php", true);

/**
 * Функция отображения страницы административного интерфейса
 * возвращает контекст страницы административного интерфейса
 * @return string
 */
function admin_show_content()
{
    global $MAIN;
    $ret = "";

    $query_string = $MAIN->QueryStringWithoutParams();
    $query = "";
    if(isset($_POST["query"]) && $_POST["query"])
    {
        $query = get_magic_quotes_gpc()?stripslashes(htmlspecialchars($_POST["query"])):htmlspecialchars($_POST["query"]);
    }

    $checked = (isset($_POST["showresult"]) && $_POST["showresult"]=="1")?" checked":"";

    $ret .= <<<EOT
<form method="post" enctype="multipart/form-data" action="{$query_string}">
    Текст запроса:<br>
    <textarea name="query" cols="60" rows="20">{$query}</textarea><br />
    Файл с текстом запроса:<br />
    <input type="file" name="query_file" /><br />
    <input type="Checkbox" name="showresult" value="1" {$checked}>Показывать результат запроса<br />
    <input type="Submit" value="Выполнить">
</form>
EOT;


    if(isset($_POST["query"]) && $_POST["query"])
    {

        $content = get_magic_quotes_gpc()?stripslashes($_POST["query"]):$_POST["query"];
        $showresult = false;
        if( isset($_POST["showresult"]) &&  $_POST["showresult"] == "1")
            $showresult = true;
        $ret .= parse_mysql_dump($content, $showresult);
    }
    else if(isset($_FILES["query_file"])
        && file_exists($_FILES["query_file"]["tmp_name"]))
    {
        $handle = fopen($_FILES["query_file"]["tmp_name"], "r") or die("Cannot open file " . $_FILES["query_file"]["tmp_name"]);
        $content = fread($handle, filesize($_FILES["query_file"]["tmp_name"])) or die("Cannot read file " . $_FILES["query_file"]["tmp_name"]);
        fclose($handle) or die("Error close file " . $_FILES["query_file"]["tmp_name"]);
        unlink($_FILES["query_file"]["tmp_name"]) or die("Error delete temp file " . $_FILES["query_file"]["tmp_name"]);

        $ret .= parse_mysql_dump($content, $_POST["showresult"] == "1");
    }



    return $ret;
}

exit;
/*
$root = "../";

include_once($root . "configuration/configuration.inc.php");
include_once($root . "includes/main_class.inc.php");

$MAIN = new CMain($root,array("need_config" => false,"need_session" => true, "need_session_db" =>false, "is_admin"=>true));
$MAIN->HeaderIncludes();
$MAIN->Init("dbquery_all");

$MAIN->IncludeModule("header.inc.php", true);



if(isset($_POST["query"]) && $_POST["query"])
{

    $content = get_magic_quotes_gpc()?stripslashes($_POST["query"]):$_POST["query"];
    parse_mysql_dump($content, $_POST["showresult"] == "1");
}
else if(isset($_FILES["query_file"])
    && file_exists($_FILES["query_file"][tmp_name]))
{
    $handle = fopen($_FILES["query_file"][tmp_name], "r") or die("Cannot open file " . $_FILES["query_file"][tmp_name]);
    $content = fread($handle, filesize($_FILES["query_file"][tmp_name])) or die("Cannot read file " . $_FILES["query_file"][tmp_name]);
    fclose($handle) or die("Error close file " . $_FILES["query_file"][tmp_name]);
    unlink($_FILES["query_file"][tmp_name]) or die("Error delete temp file " . $_FILES["query_file"][tmp_name]);

    parse_mysql_dump($content, $_POST["showresult"] == "1");
}

?>
<form method="post" enctype="multipart/form-data" action="<?=$MAIN->QueryStringWithoutParams()?>">
    Текст запроса:<br>
    <textarea name="query" cols="60" rows="20"><?
        if(isset($_POST["query"]))
            echo get_magic_quotes_gpc()?stripslashes(htmlspecialchars($_POST["query"])):htmlspecialchars($_POST["query"]);?></textarea><br />
    Файл с текстом запроса:<br />
    <input type="file" name="query_file" /><br />
    <input type="Checkbox" name="showresult" value="1"<?=(isset($_POST["showresult"]) && $_POST["showresult"]=="1")?" checked":""?>>Показывать результат запроса<br>
    <input type="Submit" value="Выполнить">
</form>

<?
$MAIN->IncludeModule("footer.inc.php", true);

*/

function parse_mysql_dump($query_content, $show_result=false)
{
    $ret = "";

    $old_time_limit = ini_get('max_execution_time');
    set_time_limit(0);


    /*
        $file_content = explode("
    ", $query_content);
    */
    $file_content = preg_split("/[
]/",$query_content);



    //var_dump($file_content);

    //$file_content = file($url);
    $query = "";
    foreach($file_content as $sql_line)
    {
        //var_dump($sql_line);
        $sql_line = preg_replace("/^--.*$/", "", $sql_line);
        $sql_line = preg_replace("/^\\#.*$/", "", $sql_line);
        $sql_line = preg_replace("/^\\s*\\/\\*.*\\*\\/\\s*$/", "", $sql_line);
        if(trim($sql_line) != "" /*&& strpos($sql_line, "--") === false*/)
        {

            $query .= $sql_line;
            if(preg_match("/.*;\\s*$/", $sql_line))
            {
                //var_dump($query);
                $db = new CDatabase();
                $result = $db->Query($query, true);

                if($db->error_no)
                {
                    $ret .= "<h3>";
                    $ret .= "ERROR:";
                    $ret .= "<br />";
                    $ret .= $db->error_no;
                    $ret .= " ";
                    $ret .= $db->error;
                    $ret .= "<br />";
                    $ret .= "</h3>";
                }

                //$result = mysql_query($query) or die(die_mysql_error_show($query));
                if($show_result)
                {
                    $ret .= <<<EOT
<br />
<br />
$query
<br />
<br />
EOT;

                    $ret .= show_mysql_result($result);
                }
                $query = "";
                $db->Free();
            }
        }
    }
    set_time_limit($old_time_limit);

    return $ret;
}

function show_mysql_result($result)
{
    $ret = "";
    global $connection;
    if($result)
    {
        //if($_POST["showresult"] == "1")
        {
            if(@mysql_info($connection))
            {
                $ret .= "<br />";
                $ret .= mysql_info($connection);

                //                echo "<br>";
                //                echo mysql_info($connection);
            }

            $affected_rows = @mysql_affected_rows($connection);
            $num_rows = @mysql_num_rows($result);

            if($affected_rows)
            {
                $ret .= "<br />";
                $ret .= "Обработано записей: " . $affected_rows;
                $ret .= "<br />";
                //                echo "<br />";
                //                echo "Обработано записей: " . $affected_rows;
                //                echo "<br />";
            }

            if($num_rows > 0)
            {

                $ret .=  "<h3>". mysql_field_table($result,0) . "</h3>";
                //echo "<h3>". mysql_field_table($result,0) . "</h3>";

                //echo "<br>";
                //echo "Обработано записей: " . $num_rows;

                if($row  = mysql_fetch_assoc($result))
                {
                    $ret .= <<<EOT
                <table width="100%" cellpadding="10" cellspacing="0" border="1" class="admin_edittable">
                    <tr class="admin_editth" bgcolor="#EEEEEE">

EOT;
                    foreach($row as $key => $value)
                    {
                        $ret .= <<<EOT
                            <th>{$key}</th>
EOT;
                    }

                    $ret .= <<<EOT
                    </tr>
EOT;
                    do
                    {
                        $ret .= <<<EOT
                        <tr class="admin_edittr">
EOT;

                        foreach($row as /*$key =>*/ $value)
                        {
                            $ret .= <<<EOT
                                <td>{$value}</td>
EOT;
                        }
                        $ret .= <<<EOT
                        </tr>
EOT;
                    }
                    while($row  = mysql_fetch_assoc($result));

                    $ret .= <<<EOT
                </table>
EOT;
                }
            }
        }
    }
    else
    {
        $ret .= <<<EOT
<br />
Ошибка выполнения запроса
<br />
EOT;
        $ret .= mysql_errno() . ": " . mysql_error() . "\n";

        //        echo "<br>";
        //        echo "Ошибка выполнения запроса";
        //        echo "<br>";
        //        echo mysql_errno() . ": " . mysql_error() . "\n";
    }

    return $ret;

}
?>
