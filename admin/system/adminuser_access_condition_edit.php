<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => false, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminuser_access_condition");



  $GLOBALS["adminusergroup_id"] = false;
  if(isset($_GET["adminusergroup_id"]))
  	$GLOBALS["adminusergroup_id"] = $_GET["adminusergroup_id"];
  
  $GLOBALS["adminuser_id"] = false;
  if(isset($_GET["adminuser_id"]))
  	$GLOBALS["adminuser_id"] = $_GET["adminuser_id"];
  
  $GLOBALS["adminuser_access_id"] = false;
  if(isset($_GET["adminuser_access_id"]))
  	$GLOBALS["adminuser_access_id"] = $_GET["adminuser_access_id"];

  
  $entity = new CEntity(
    array(
      "table"=>"adminuser_access_condition",
      "id"=>isset($_GET["id"])?$_GET["id"]:NULL
    )
  );
  
  
  
  if($entity->identity)
  {
  	$GLOBALS["adminuser_access_id"] = $entity->GetHeader("adminuser_access_id");
  }
  else
  {
  	$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_value", "hidden", "true");
  	$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_ishierarchisch", "hidden", "true");
  	$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_isread", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_iswrite", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_isadd", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_isdelete", "hidden", "true");
    $MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_isshow", "hidden", "true");
  }
  
  $entity_adminuser_access = new CEntity(
    array(
      "table"=>"adminuser_access",
      "id"=>$GLOBALS["adminuser_access_id"]
    )
  );
  if($entity_adminuser_access->identity)
  {
  	$GLOBALS["adminuser_id"] = $entity_adminuser_access->GetHeader("adminuser_id");
  	$GLOBALS["adminusergroup_id"] = $entity_adminuser_access->GetHeader("adminusergroup_id");

  	if($entity->identity)
  	{
  		//var_dump($MAIN->GetTableFieldParamEx($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"), "params", "select", "hierarchy"));
  		//var_dump($entity_adminuser_access->GetHeader("adminuser_access_entity"));
  		//var_dump($entity->GetHeader("adminuser_access_condition_field"));
  		if($MAIN->GetTableFieldParamEx($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"), "params", "select", "hierarchy") != "true")
  		{
  			$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_ishierarchisch", "hidden", "true");
  		} 
  		if(is_array($MAIN->GetTableFieldParamEx($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"), "params", "select", "table")))
  		{
  			// select (table)
  			$v = $MAIN->GetTableFieldParams($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"));
  			$MAIN->SetCurrentArrayLang($v["name"], $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "name")). ": ".$MAIN->GetCurrentArrayLang($v["name"]));
  			//var_dump($v);
  			$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_value", $v);
  		}
  		elseif($MAIN->GetTableFieldParamEx($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"), "params", "select", "values"))
  		{
  			// select (values)
  			$v = $MAIN->GetTableFieldParams($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"));
  			$MAIN->SetCurrentArrayLang($v["name"], $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "name")). ": ".$MAIN->GetCurrentArrayLang($v["name"]));
  			$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_value", $v);
  			
  		}
  		elseif($MAIN->GetTableFieldParamEx($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"), "type") == "checkbox")
  		{
  			// checkbox
  			$v = $MAIN->GetTableFieldParams($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"));
  			//var_dump($v);
  			$MAIN->SetCurrentArrayLang($v["name"], $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "name")). ": ".$MAIN->GetCurrentArrayLang($v["name"]));
  			//var_dump($v);
  			$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_value", $v);
  		}
  		elseif(false)
  		{
  			// datetime
  			$v = $MAIN->GetTableFieldParams($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"));
  			//var_dump($v);
  			$MAIN->SetCurrentArrayLang($v["name"], $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "name")). ": ".$MAIN->GetCurrentArrayLang($v["name"]));
  			//var_dump($v);
  			$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_value", $v);
  		}
  		elseif(false)
  		{
  			// image|file|sound|video - hide the field
  			$v = $MAIN->GetTableFieldParams($entity_adminuser_access->GetHeader("adminuser_access_entity"), $entity->GetHeader("adminuser_access_condition_field"));
  			//var_dump($v);
  			$MAIN->SetCurrentArrayLang($v["name"], $MAIN->GetCurrentArrayLang($MAIN->GetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "name")). ": ".$MAIN->GetCurrentArrayLang($v["name"]));
  			//var_dump($v);
  			$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_value", $v);
  			$MAIN->SetTableFieldParam("adminuser_access_condition", "adminuser_access_condition_value", "hidden", "true");
  		}
  	}
 	
  }
  
  //var_dump($entity_adminuser_access);exit;
  
  
  global $adminuser_access_entity_values;
  $adminuser_access_entity_values = array();
  foreach ($MAIN->GetAdminEntities(array("all", "edit"), true) as $table)
	{
		$adminuser_access_entity_values[$table] = array($MAIN->GetAdminEntityName($table));
	} 

	
  global $adminuser_access_condition_fields;
  $adminuser_access_condition_fields = array();
  if($entity_adminuser_access->identity)
  {
	  $tableFields = $MAIN->GetTableFieldsParams($entity_adminuser_access->GetHeader("adminuser_access_entity"));
	  foreach ($tableFields as $field => $field_params)
		{
			$adminuser_access_condition_fields[$field] = $field_params["name"];
		} 
  }
	//var_dump($adminuser_access_entity_values);
	
  
  global $adminuser_access_condition_field_params;
  $adminuser_access_entity_params = array(
		"name" => array("Объект"),
		"type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
		"params" => array( // параметры поля
			"length" => "30", // длина поля для полей типа string, number
			"nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
			//"readonly" => "true",   // неизменяемое значение
			"select" => array( // параметры поля типа select
				"size" => "1", // size для поля типа select
				"multiselect" => "false", // возможность множественного выбора для поля типа select
				"values" => "adminuser_access_entity_values",
			),
		),
  );
  $adminuser_access_condition_field_params = array(
		"name" => array("Поле объекта"),
		"type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
		"params" => array( // параметры поля
			"length" => "30", // длина поля для полей типа string, number
			"nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
			//"readonly" => "true",   // неизменяемое значение
			"select" => array( // параметры поля типа select
				"size" => "1", // size для поля типа select
				"multiselect" => "false", // возможность множественного выбора для поля типа select
				"values" => "adminuser_access_condition_fields",
			),
		),
  );
  
  $MAIN->SetTableFieldParams("adminuser_access", "adminuser_access_entity", $adminuser_access_entity_params);
	$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_field", $adminuser_access_condition_field_params);
  if($entity->identity)
  {
		$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_id", "readonly", "true");
		$MAIN->SetTableFieldParamsParam("adminuser_access_condition", "adminuser_access_condition_field", "readonly", "true");
  }
	
  
  if($MAIN->CheckPost("action", "edit"))
  {
  	//var_dump($_POST);exit;
  	$entity->SavePost();
    // redirect
    $location = "Location: " . $MAIN->QueryStringWithoutParams() . "?id=" . $entity->identity;
    if($GLOBALS["adminuser_id"])
    	$location .= "&adminuser_id=" . $GLOBALS["adminuser_id"];
    if($GLOBALS["adminusergroup_id"])
    	$location .= "&adminusergroup_id=" . $GLOBALS["adminusergroup_id"];
    if($GLOBALS["adminuser_access_id"])
    	$location .= "&adminuser_access_id=" . $GLOBALS["adminuser_access_id"];
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
  	$id = $entity->GetHeader("adminuser_access_id");
  	$adminuser_id = $entity_adminuser_access->GetHeader("adminuser_id");
  	$adminusergroup_id = $entity_adminuser_access->GetHeader("adminusergroup_id");
  	// удаление объекта
    $entity->Delete();
    // redirect
    header("Location: " . $MAIN->GetAdminPageUrl("adminuser_access", "edit", array("id"=>$id, "adminuser_id"=>$adminuser_id, "adminusergroup_id"=>$adminusergroup_id,)));
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

	$breadcrumb_parents[] = array(
      "table" => "adminuser_access",
      "page" => "edit",
      "vars" => "id=".$GLOBALS["adminuser_access_id"]."&adminuser_id=".$GLOBALS["adminuser_id"],
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
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_access_condition_edit")'><span id="adminuser_access_condition_edit_status">-</span> <?=$MAIN->GetAdminPageName("adminuser_access_condition", "edit")?></a></div>
	<div id="adminuser_access_condition_edit" class="box-content"><?=$entity->ViewEdit()?></div>
</div>

<?
/*
	if($entity->identity)
	{
?>

<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_access_condition_list")'><span id="adminuser_access_condition_list_status">-</span> <?=$MAIN->GetAdminPageName("adminuser_access_condition", "list")?></a></div>
	<div id="adminuser_access_condition_list" class="box-content"><?$MAIN->IncludeFile("adminuser_access_condition_list.php", true);?></div>
</div>
<?
	}
*/
?>


<script type="text/javascript">
<!--
	xajax_show_hide_current("adminuser_access_condition_edit");
	//xajax_show_hide_current("adminuser_access_condition_list");
//-->
</script>

<?
	s_reset_errors($MAIN->QueryStringWithoutParams());
  $MAIN->IncludeModule("footer.inc.php", true);
?>
