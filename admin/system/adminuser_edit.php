<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => false, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminuser");


  $GLOBALS["adminusergroup_id"] = false;
  if(isset($_GET["adminusergroup_id"]))
  	$GLOBALS["adminusergroup_id"] = $_GET["adminusergroup_id"];
  
  
  $entity = new CEntity(
    array(
      "table"=>"adminuser",
      "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
  );

  $GLOBALS["adminusergroup_id"] = false;
  if(isset($_GET["adminusergroup_id"]))
  	$GLOBALS["adminusergroup_id"] = $_GET["adminusergroup_id"];
  elseif($entity->GetHeader("adminusergroup_id"))
  	$GLOBALS["adminusergroup_id"] = $entity->GetHeader("adminusergroup_id");
  
  if($MAIN->CheckPost())
  {
  	if($_POST["adminuser_ln"] == ADMIN_USER)
  	{
  		s_add_error($MAIN->QueryStringWithoutParams(), "Требуется выбрать другой логин!");
  	}
  	if($_POST["adminuser_ln"] == "")
  	{
  		s_add_error($MAIN->QueryStringWithoutParams(), "Логин не может быть пустым!");
  	}
  	
  	if(s_source_errors_count($MAIN->QueryStringWithoutParams()))
  	{
  		// redirect
	    header("Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity  );
	    exit;
  	}

  	$entity->SavePost();
    // redirect
    header("Location: " .$MAIN->QueryStringWithoutParams(). "?id=" . $entity->identity  );
    exit;

  }
  if($MAIN->CheckGet("action","delete"))
  {
  	// удаление всех потомков
  	
  	// удаление объекта
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("adminuser", "all") );
    exit;
  }
  
  
  
  $breadcrumb_parents = array(
    "0" => array(
      "table" => "adminusergroup",
      "page" => "all",
      "vars" => ""
    ),
  );
  
	if($GLOBALS["adminusergroup_id"])
	{
    $breadcrumb_parents["1"] = array(
      "table" => "adminusergroup",
      "page" => "edit",
      "vars" => "id=".$GLOBALS["adminusergroup_id"]
    );
		
	}
  
  
  $MAIN->IncludeModule("header.inc.php", true);
?>

<?
	$errors = s_get_errors_array($MAIN->QueryStringWithoutParams());
	if(is_array($errors) && count($errors))
	{
		foreach ($errors as $error)
		{
?>
<div class="error"><?=$error?></div>
<?			
		}
	}
?>

<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_edit")'><span id="adminuser_edit_status">-</span> Редактирование администратора</a></div>
	<div id="adminuser_edit" class="box-content"><?=$entity->ViewEdit()?></div>
</div>

<?
	if($entity->identity)
	{
?>
<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_access_list")'><span id="adminuser_access_list_status">-</span> Объекты доступа администратора</a></div>
	<div id="adminuser_access_list" class="box-content"><?$MAIN->IncludeFile("adminuser_access_list.php", true);?></div>
</div>
<?		
	}
?>


<script type="text/javascript">
<!--
	xajax_show_hide_current("adminuser_edit");
	xajax_show_hide_current("adminuser_access_list");
//-->
</script>

<?
	s_reset_errors($MAIN->QueryStringWithoutParams());
  $MAIN->IncludeModule("footer.inc.php", true);
?>
