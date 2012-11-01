<?
//-----------------------------------------------------------------------------
// Главная страница
//-----------------------------------------------------------------------------
	$root = "";
  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  global $MAIN;
  
  $MAIN = new CMain($root,
      array(
          "is_admin"=>true
      )
  );
  $MAIN->HeaderIncludes();
  $MAIN->Init();
  	

  
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?=DEFAULT_CHARSET?>" />
	<title>test2</title>
  <meta name="author" content="Information Ways" />
</head>
<body topmargin="0">

<?
var_dump($MAIN->GetTemplateFile(__FILE__, "", "page", "page.php"));
var_dump($MAIN->GetTemplateContent(__FILE__, "", "page", "page.php"));
?>

</body>
</html>
