<?
//-----------------------------------------------------------------------------
// список модулей сказки
//-----------------------------------------------------------------------------
	global $MAIN;

  $GLOBALS["page_module_id"] = $_GET["id"];
  
  $page_module_image_list = new CEntityList(
    array(
      "table" => "page_module_image",
      "table_parent" => "page_module",
      "key_parent" => "page_module_id",
      "parent_id" => $GLOBALS["page_module_id"],
    )
  );

?>

<h3><?=$MAIN->GetAdminPageName("page_module_image", "list") ?></h3>
<a href="page_module_image_edit.php?page_module_id=<?=$GLOBALS["page_module_id"]?>">Добавить изображение</a>
<?=
  $page_module_image_list->ViewEditList(
    array(
      "keys"=>array("page_module_image_name", "page_module_image_preview", "page_module_image_isshow"),
      "actions"=>array("edit","up","down")
    )
  )
?>