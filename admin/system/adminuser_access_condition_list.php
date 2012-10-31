<?
//-----------------------------------------------------------------------------
// Отображение списка модулей страницы по центру
//-----------------------------------------------------------------------------
global $MAIN;

	$GLOBALS["adminuser_access_id"] = (isset($_GET["id"])?$_GET["id"]:0);
	  
	$adminuser_access_condition_list = new CEntityList(
		array(
			"table" => "adminuser_access_condition",
			"table_parent" => "adminuser_access",
			"key_parent" => "adminuser_access_id",
			"parent_id" => $GLOBALS["adminuser_access_id"],
		)
	);
	
//	global $adminuser_access_entity_values;
//	$adminuser_access_entity_values = array();
//	foreach ($MAIN->GetAdminEntities(array("all", "edit"), true) as $table)
//	{
//		$adminuser_access_entity_values[$table] = array($MAIN->GetAdminEntityName($table));
//	}  	
//	$adminuser_access_entity_params = array(
//		"name" => array("Объект"),
//		"type" => "select", // тип поля string, password, text, number, datetime, checkbox, select, image, sound, video, file
//		"params" => array( // параметры поля
//			"length" => "30", // длина поля для полей типа string, number
//			"nolang" => "true", // поле одинаково во всех языках, берется значение с индексом [0]
//			"readonly" => "true",   // неизменяемое значение
//			"select" => array( // параметры поля типа select
//				"size" => "1", // size для поля типа select
//				"multiselect" => "false", // возможность множественного выбора для поля типа select
//				"values" => "adminuser_access_entity_values",
//			),
//		),
//	);
//	$MAIN->SetTableFieldParams("adminuser_access", "adminuser_access_entity", $adminuser_access_entity_params);

  global $adminuser_access_condition_fields;
  global $entity;
  //var_dump($entity);
  $adminuser_access_condition_fields = array();
  if($entity->identity)
  {
	  $tableFields = $MAIN->GetTableFieldsParams($entity->GetHeader("adminuser_access_entity"));
	  foreach ($tableFields as $field => $field_params)
		{
			$adminuser_access_condition_fields[$field] = $field_params["name"];
		} 
  }
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
  
	$MAIN->SetTableFieldParams("adminuser_access_condition", "adminuser_access_condition_field", $adminuser_access_condition_field_params);
  
	
?>
<a href="adminuser_access_condition_edit.php?adminuser_id=<?=$GLOBALS["adminuser_id"]?>&adminuser_access_id=<?=$GLOBALS["adminuser_access_id"]?>">Добавить</a>
<?=
  $adminuser_access_condition_list->ViewEditList(
    array(
      "keys"=>array("adminuser_access_condition_field", "adminuser_access_condition_isshow"),
      "actions"=>array("edit","up","down")
    )
  )
?>

