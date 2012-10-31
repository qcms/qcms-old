<?
//-----------------------------------------------------------------------------
// список модулей страницы
//-----------------------------------------------------------------------------
	global $MAIN;

  $GLOBALS["page_id"] = $_GET["id"];
  
  $page_module_list = new CEntityList(
    array(
      "table" => "page_module",
      "table_parent" => "page",
      "key_parent" => "page_id",
      "parent_id" => $GLOBALS["page_id"],
    )
  );

?>

<h3><?=$MAIN->GetAdminPageName("page_module", "list") ?></h3>
<a href="page_module_edit.php?page_id=<?=$GLOBALS["page_id"]?>">Добавить модуль</a>
<?=
  $page_module_list->ViewEditList(
    array(
      "keys"=>array("page_module_name", "page_module_type", "page_module_isshow"),
      "actions"=>array("edit","up","down")
    )
  )
?>