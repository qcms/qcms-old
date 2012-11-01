<?
//-----------------------------------------------------------------------------
// Элементы формы редактирования
//-----------------------------------------------------------------------------

$_template_form_scripts = '
<script language="JavaScript">
var arr_editor = [];
var current_sound = 0;
var arr_sound = [];
var i;
var current_video = 0;
var arr_video = [];
var is_play = false;
function set_current_sound_number(n)
{
  current_sound = n;
}
function get_current_sound_number()
{
  return current_sound;
}
function play_next_sound_number(n)
{
  for(i=0; i<arr_sound.length; i++)
  {
    if(""+arr_sound[i] == ""+n)
    {
      if(i+1<arr_sound.length)
      {
        //alert(i+1);
        current_sound = arr_sound[i+1];
        return arr_sound[i+1];
      }
    }
  }
  is_play = false;
  current_sound=0;
  return 0;
}
function set_current_video_number(n)
{
  current_video = n;
}
function get_current_video_number()
{
  return current_video;
}
function play_next_video_number(n)
{
  for(i=0; i<arr_video.length; i++)
  {
    if(""+arr_video[i] == ""+n)
    {
      if(i+1<arr_video.length)
      {
        //alert(i+1);
        current_video = arr_video[i+1];
        return arr_video[i+1];
      }
    }
  }
  is_play = false;
  current_video=0;
  return 0;
}
function get_is_play()
{
  return is_play;
}
function set_is_play(v)
{
  is_play = v;
}
function thisMovie(movieName) {
  if (navigator.appName.indexOf("Microsoft") != -1) {
    return window[movieName];
    //return document.all[movieName];
  } else {
    //alert(document[movieName]);
    return document[movieName];
    //return getElementById(movieName);
  }
}
';


$_template_form_scripts .= '
//alert("aaa");


</script>
';

$_template_form_header = '
<style type="text/css">

TABLE.viewedit-table{
  border-top:#999 1px solid;
  border-right:#999 1px solid;
  margin:5px;
}

TABLE.viewedit-table TR.viewedit-tr TH.viewedit-td,
TABLE.viewedit-table TR.viewedit-tr TD.viewedit-td-left{
  background:#EEE;
  border-bottom:#999 1px solid;
  border-left:#999 1px solid;
}

TABLE.viewedit-table TR.viewedit-tr TD.viewedit-td{
  background:#FFF;
  border-bottom:#999 1px solid;
  border-left:#999 1px solid;
}

TABLE.viewedit-table TR.viewedit-tr TH.viewedit-td,
TABLE.viewedit-table TR.viewedit-tr TD.viewedit-td-left,
TABLE.viewedit-table TR.viewedit-tr TD.viewedit-td-right{
  padding:3px;
  border-bottom:#999 1px solid;
  border-left:#999 1px solid;
}

TABLE.viewedit-table TR.viewedit-tr TH.viewedit-td-right,
TABLE.viewedit-table TR.viewedit-tr TD.viewedit-td-right{
  min-width:450px;
}

</style>



<form class="entity_edit_form" action="[action]?action=edit&id=[id]" onsubmit="submit_form_function();" method="post" enctype="multipart/form-data">
<table width="95%" class="viewedit-table" border="0" cellspacing="0" cellpadding="0">
';

$_template_form_tr_header = '
  <tr class="viewedit-tr" align="left" valign="middle">
';


$_template_form_tr_footer = '
  </tr>
';

$_template_form_tr_td_left = '
    <td class="viewedit-td-left" align="right" width="30%" valign="top">[content]</td>
';

$_template_form_tr_td_left_rowspan2 = '
    <td class="viewedit-td-left" align="right" width="30%" valign="top" rowspan="2">[content]</td>
';

$_template_form_tr_td_left_rowspan3 = '
    <td class="viewedit-td-left" align="right" width="30%" valign="top" rowspan="3">[content]</td>
';

$_template_form_tr_td_right = '
    <td class="viewedit-td-right" width="70%">[content]</td>
';


$_template_form_footer = '
</table>
</form>
';


$_template_form_update = '
  <tr align="center" valign="middle">
    <td colspan="2"><input type="submit" value="Сохранить"></td>
  </tr>
';


$_template_form_delete = '
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


$_template_playervideo = '
<div class="videoplayer" id="vp_[video]_[id]">You need Flash player 8+ and JavaScript enabled to view this video.</div>
<script type="text/javascript">
	var params = { allowScriptAccess: "always", wmode: "transparent", allowFullScreen: "true"	 };
	var atts = { id: "vp_[video]_[id]", styleclass: "videoplayer" };
	swfobject.embedSWF(
		"/playervideo/playervideo.swf?table=[table]&video=[video]&preview=[preview]&id=[id]", 
		"vp_[video]_[id]", "[width]", "[height]", "8", null, null, params, atts);

	arr_video[arr_video.length] = [video_id];
</script>
';

$_template_playeraudio = '<script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js.");
	} else {
		AC_FL_RunContent(' . "
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
			'width', '448',
			'height', '20',
			'src', 'playersound/playersound_[sound_id].swf',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'transparent',
			'devicefont', 'false',
			'id', 'playersound_[sound_id]',
			'bgcolor', '#ffffff',
			'name', 'playersound_[sound_id]',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', 'playersound/playersound_[sound_id]',
			'salign', ''
			); //end AC code " . '
	}
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="448" height="20" id="playersound_[sound_id]" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="playersound/playersound_[sound_id]" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />	<embed src="playersound/playersound_[sound_id].swf" quality="high" wmode="transparent" bgcolor="#ffffff" width="448" height="20" name="playersound_[sound_id]" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>

<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/


arr_sound[arr_sound.length] = [sound_id];

//alert(arr_sound.length);

/*]]>*/
</script>';

?>