<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => false, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminuser_access");


  $GLOBALS["adminusergroup_id"] = false;
  if(isset($_GET["adminusergroup_id"]))
  	$GLOBALS["adminusergroup_id"] = $_GET["adminusergroup_id"];
  
  $GLOBALS["adminuser_id"] = false;
  if(isset($_GET["adminuser_id"]))
  	$GLOBALS["adminuser_id"] = $_GET["adminuser_id"];
  	
  $entity = new CEntity(
    array(
      "table"=>"adminuser_access",
      "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
  );
  
  global $adminuser_access_entity_values;
  $adminuser_access_entity_values = array();
  foreach ($MAIN->GetAdminEntities(array("all", "edit"), true) as $table)
	{
		$adminuser_access_entity_values[$table] = array($MAIN->GetAdminEntityName($table));
	}  	

  
  if($entity->identity && $entity->GetHeader("adminuser_access_entity"))
  {
  	$adminuser_access_entity_params = array(
			"name" => array("Объект"),
			"type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
			"params" => array( // параметры поля
				"length" => "30", // длина поля для полей типа string, number
				"nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
				"readonly" => "true",   // неизменяемое значение
				"select" => array( // параметры поля типа select
					"size" => "1", // size для поля типа select
					"multiselect" => "false", // возможность множественного выбора для поля типа select
					"values" => "adminuser_access_entity_values",
				),
			),
  	);
  	$MAIN->SetTableFieldParams("adminuser_access", "adminuser_access_entity", $adminuser_access_entity_params);
  	
  	if(!is_array($MAIN->GetTableParam($entity->GetHeader("adminuser_access_entity"), "hierarchy")))
  		$MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_ishierarchisch", "hidden", "true");
  }
  else
  {
  	$adminuser_access_entity_params = array(
			"name" => array("Объект"),
			"type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
			"params" => array( // параметры поля
				"length" => "30", // длина поля для полей типа string, number
				"nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
				"select" => array( // параметры поля типа select
					"size" => "1", // size для поля типа select
					"multiselect" => "false", // возможность множественного выбора для поля типа select
					"values" => "adminuser_access_entity_values",
				),
			),
  	);
  	$MAIN->SetTableFieldParams("adminuser_access", "adminuser_access_entity", $adminuser_access_entity_params);
  	
  	$MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_ishierarchisch", "hidden", "true");
  	$MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_isread", "hidden", "true");
  	$MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_iswrite", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_isadd", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_isdelete", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access", "adminuser_access_isshow", "hidden", "true");
  	
  }

//  $GLOBALS["adminusergroup_id"] = false;
//  if(isset($_GET["adminusergroup_id"]))
//  	$GLOBALS["adminusergroup_id"] = $_GET["adminusergroup_id"];
//  elseif($entity->GetHeader("adminusergroup_id"))
//  	$GLOBALS["adminusergroup_id"] = $entity->GetHeader("adminusergroup_id");
  
  if($MAIN->CheckPost("action", "edit"))
  {
//  	if($_POST["adminuser_ln"] == ADMIN_USER)
//  	{
//  		s_add_error($MAIN->QueryStringWithoutParams(), "Требуется выбрать другой логин!");
//  	}
//  	if($_POST["adminuser_ln"] == "")
//  	{
//  		s_add_error($MAIN->QueryStringWithoutParams(), "Логин не может быть пустым!");
//  	}
//  	
//  	if(s_source_errors_count($MAIN->QueryStringWithoutParams()))
//  	{
//  		// redirect
//	    header("Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity  );
//	    exit;
//  	}

  	$entity->SavePost();
    // redirect
    $location = "Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity;
    if($entity->GetHeader("adminuser_id"))
    	$location .= "&adminuser_id=" . $entity->GetHeader("adminuser_id");
    if($entity->GetHeader("adminusergroup_id"))
    	$location .= "&adminusergroup_id=" . $entity->GetHeader("adminusergroup_id");
    header($location);
    exit;

  }
  
  // обработка действий
  if($MAIN->CheckGet("action", "up") && $MAIN->CheckGetExists("id"))
  {
	  $list = new CEntityList(
	    array(
	      "table"=>"adminuser_access",
	      "table_parent"=>"adminuser",
	      "key_parent"=>"adminuser_id"
	    )
	  );
  	
    $list->MoveUp($_GET["id"]);
    // redirect
    //$GLOBALS["id"] = $_GET["adminuser_id"];
		header("Location: " . $MAIN->GetAdminPageUrl("adminuser", "edit", array("id"=>$_GET["adminuser_id"])) );
		exit;
  }
  if($MAIN->CheckGet("action", "down") && $MAIN->CheckGetExists("id"))
  {
	  $list = new CEntityList(
	    array(
	      "table"=>"adminuser_access",
	      "table_parent"=>"adminuser",
	      "key_parent"=>"adminuser_id"
	    )
	  );
  	  	
	  $list->MoveDown($_GET["id"]);
    // redirect
    //$GLOBALS["id"] = $_GET["adminuser_id"];
    //var_dump($GLOBALS["id"]);
    //echo $MAIN->GetAdminPageUrl("adminuser", "edit", array("id"=>$_GET["adminuser_id"]));exit;
		header("Location: " . $MAIN->GetAdminPageUrl("adminuser", "edit", array("id"=>$_GET["adminuser_id"])) );
	  exit;
  }
  
  if($MAIN->CheckGet("action","delete"))
  {
  	// удаление всех потомков
  	
  	// удаление объекта
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageFile("adminuser", "all") );
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
    $breadcrumb_parents[] = array(
      "table" => "adminusergroup",
      "page" => "edit",
      "vars" => "id=".$GLOBALS["adminusergroup_id"]
    );
		
	}
  
	$breadcrumb_parents[] = array(
      "table" => "adminuser",
      "page" => "edit",
      "vars" => "id=".$GLOBALS["adminuser_id"]
	);
	
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
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_access_edit")'><span id="adminuser_access_edit_status">-</span> <?=$MAIN->GetAdminPageName("adminuser_access", "edit")?></a></div>
	<div id="adminuser_access_edit" class="box-content"><?=$entity->ViewEdit()?></div>
</div>


<?/*?>
<form action="<?=$MAIN->GetAdminPageUrl("adminuser_access", "edit")?>?action=edit" method="post" enctype="application/x-www-form-urlencoded">
	<fieldset>
		<label>Объект:</label>
		<select name="adminuser_access_entity" id="adminuser_access_entity">
			<option value="0">Не выбрано</option>
<?
	foreach ($MAIN->GetAdminEntities(array("all", "edit"), true) as $table)
	{
?>
			<option value="<?=$table?>"<? 
		if($entity->GetHeader("adminuser_access_entity") == $table)
			echo " selected";
			?>><?=$MAIN->GetAdminEntityName($table)?></option>
<?
	}
?>
		</select>
	</fieldset>
	<fieldset>
		<label><?=$MAIN->GetAdminEntityFieldName("adminuser_access", "adminuser_access_isedit")?>:</label>
		<input type="checkbox" name="adminuser_access_isedit" id="adminuser_access_isedit" value="1" <? if($entity->GetHeader("adminuser_access_isedit") == "1") echo " checked";?> />
	</fieldset>
	<fieldset>
		<label><?=$MAIN->GetAdminEntityFieldName("adminuser_access", "adminuser_access_isadd")?>:</label>
		<input type="checkbox" name="adminuser_access_isadd" id="adminuser_access_isadd" value="1" <? if($entity->GetHeader("adminuser_access_isadd") == "1") echo " checked";?> />
	</fieldset>
	<fieldset>
		<label><?=$MAIN->GetAdminEntityFieldName("adminuser_access", "adminuser_access_isdelete")?>:</label>
		<input type="checkbox" name="adminuser_access_isdelete" id="adminuser_access_isdelete" value="1" <? if($entity->GetHeader("adminuser_access_isdelete") == "1") echo " checked";?> />
	</fieldset>
	<fieldset>
		<label><?=$MAIN->GetAdminEntityFieldName("adminuser_access", "adminuser_access_ishierarchisch")?>:</label>
		<input type="checkbox" name="adminuser_access_ishierarchisch" id="adminuser_access_ishierarchisch" value="1" <? if($entity->GetHeader("adminuser_access_ishierarchisch") == "1") echo " checked";?> />
	</fieldset>
	<fieldset>
		<label><?=$MAIN->GetAdminEntityFieldName("adminuser_access", "adminuser_access_isshow")?>:</label>
		<input type="checkbox" name="adminuser_access_isshow" id="adminuser_access_isshow" value="1" <? if($entity->GetHeader("adminuser_access_isshow") == "1") echo " checked";?> />
	</fieldset>
	<fieldset>
		<label>&nbsp;</label>
		<input type="submit" value="Сохранить" />
	</fieldset>	

</form>
<?*/?>
<?
	if($entity->identity && $entity->GetHeader("adminuser_access_entity"))
	{
?>

<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_access_condition_list")'><span id="adminuser_access_condition_list_status">-</span> <?=$MAIN->GetAdminPageName("adminuser_access_condition", "list")?></a></div>
	<div id="adminuser_access_condition_list" class="box-content"><?$MAIN->IncludeFile("adminuser_access_condition_list.php", true);?></div>
</div>
<?
	}
?>


<script type="text/javascript">
<!--
	xajax_show_hide_current("adminuser_access_edit");
	xajax_show_hide_current("adminuser_access_condition_list");
//-->
</script>

<?
	s_reset_errors($MAIN->QueryStringWithoutParams());
  $MAIN->IncludeModule("footer.inc.php", true);
?>
