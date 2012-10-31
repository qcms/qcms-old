<?
//-----------------------------------------------------------------------------
// Отображение списка модулей страницы по центру
//-----------------------------------------------------------------------------

  
$adminuser_withoutgroup_list = new CEntityList(
	array(
		"table" => "adminuser",
		"where" => "adminusergroup_id IS NULL OR adminusergroup_id='0'"
	)
);

?>
<a href="adminuser_edit.php">Добавить администратора</a>
<?=
  $adminuser_withoutgroup_list->ViewEditList(
    array(
      "keys"=>array("adminuser_ln", "adminuser_isshow"),
      "actions"=>array("edit"),
    )
  )
?>

