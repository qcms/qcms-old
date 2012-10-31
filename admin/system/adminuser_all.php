<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => true, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminuser");

  $list = new CEntityList(
    array(
      "table"=>"adminuser",
      "pagecount"=>10
    )
  );


  // обработка действий
  if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
  {
    $list->MoveUp($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->QueryStringWithoutParams());
    exit;
  }
  if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
  {
    $list->MoveDown($_GET["id"]);
    // redirect
    header("Location: " . $MAIN->QueryStringWithoutParams());
    exit;
  }
  $MAIN->IncludeModule("header.inc.php", true);
?>
<a href="adminuser_edit.php">Добавить администратора</a>
<br>&nbsp;<br>

<?=$list->ViewEditList(array("keys"=>array("adminuser_login", "adminuser_fullname", "adminuser_isactive"), "actions"=>array("edit","up","down")))?>

<?
  $MAIN->IncludeModule("footer.inc.php", true);
?>