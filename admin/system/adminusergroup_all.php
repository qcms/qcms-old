<?
  $root = "../";

  include_once($root . "configuration/configuration.inc.php");
  include_once($root . "includes/main_class.inc.php");
  
  $MAIN = new CMain($root,array("need_config" => true, "is_admin"=>true));
  $MAIN->HeaderIncludes();
  $MAIN->Init("adminusergroup");


  $list = new CEntityList(
    array(
      "table"=>"adminusergroup",
      //"pagecount"=>20,
    )
  );


  $MAIN->IncludeModule("header.inc.php", true);
  
/*  
?>
<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminusergroup_all")'><span id="adminusergroup_all_status">-</span> Список групп</a></div>
	<div id="adminusergroup_all" class="box-content">
	
<a href="adminusergroup_edit.php">Добавить</a>
<br />
<?=$list->ViewEditList(
  array(
    "keys"=>array("adminusergroup_ln", "adminusergroup_isshow"),
    "actions"=>array("edit")
  )
)?></div>
</div>
<?
*/
?>

<div class="box">
	<div class="box-header"><a onclick='xajax_show_hide_change("adminuser_withoutgroup_list")'><span id="adminuser_withoutgroup_list_status">-</span> Список администраторов</a></div>
	<div id="adminuser_withoutgroup_list" class="box-content"><?$MAIN->IncludeFile("adminuser_withoutgroup_list.php", true);?></div>
</div>


<?

?>
<script type="text/javascript">
<!--
	xajax_show_hide_current("adminusergroup_all");
	xajax_show_hide_current("adminuser_withoutgroup_list");
//-->
</script>
<?	
  $MAIN->IncludeModule("footer.inc.php", true);
?>