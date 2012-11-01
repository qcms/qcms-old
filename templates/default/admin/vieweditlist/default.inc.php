<?
//-----------------------------------------------------------------------------
// Элементы формы редактирования
//-----------------------------------------------------------------------------

$_template_list_header = '
<style type="text/css">

TABLE.vieweditlist-table{
  border-top:#999 1px solid;
  border-right:#999 1px solid;
}

TR.vieweditlist-tr{
  background:#FFF;
}

TR.vieweditlist-tr-hover, TR.vieweditlist-tr-hover TD{
  background:#BFB;
}

TR.vieweditlist-tr TH{
  background:#EEE;
  border-bottom:#999 1px solid;
  border-left:#999 1px solid;
}

TR.vieweditlist-tr TD{

  border-bottom:#999 1px solid;
  border-left:#999 1px solid;
}

TR.vieweditlist-tr TH,
TR.vieweditlist-tr TD {
  padding:3px;
}
</style>


<table class="vieweditlist-table" cellspacing="0" cellpadding="0">
';

$_template_list_footer = '
</table>
<script language="JavaScript" type="text/javascript">
			$("TR.vieweditlist-tr").hover(
			function () {
				$(this).addClass("vieweditlist-tr-hover");
			},
			function () {
				$(this).removeClass("vieweditlist-tr-hover");
			}
		);
</script>
';

$_template_list_tr_header = '
  <tr class="vieweditlist-tr">
';

$_template_list_th_item = '
  <th>[item]</th>
';

$_template_list_td_item = '
  <td>[item]</td>
';

$_template_list_tr_footer = '
  </tr>
';


$_template_list_parent_level0 = '=>&nbsp;';
$_template_list_parent_level1 = '==&nbsp;';


$_template_list_updown = '
    <td colspan="2"><input type="submit" value="Сохранить"></td>
';


$_template_list_delete = '
<br>
<br>
<script language="JavaScript">
<!--//
function submit_delete()
{
  if(window.confirm("Подтвердите удаление объекта?"))
  {
    document.forms["deleteform"].submit();
  }
}
//-->
</script>
<form name="deleteform" id="deleteform" method="get" action="[action]">
  <input type="Hidden" name="action" value="delete">
  <input type="Hidden" name="id" value="[id]">
  <input type="Button" onclick="submit_delete()" value="Удалить">
</form>
';


?>