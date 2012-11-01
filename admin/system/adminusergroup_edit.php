<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => false, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminusergroup");


  $entity = new CEntity(
    array(
      "table"=>"adminusergroup",
      "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
  );


  if($MAIN->CheckPost())
  {
    $entity->SavePost();
    // redirect
    header("Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity  );
    exit;

  }
  if($MAIN->CheckGet("action","delete"))
  {
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("adminusergroup", "all") );
    exit;
  }

  $MAIN->IncludeModule("header.inc.php", true);
?>
Редактирование группы администраторов

<?=$entity->ViewEdit()?>

<?
  $MAIN->IncludeModule("footer.inc.php", true);
?>