<?
//-----------------------------------------------------------------------------
// Элементы отображения
//-----------------------------------------------------------------------------
$_template_yes = '<b>Да</b>';
$_template_no = '<b>Нет</b>';


$_template_playervideo = '
<script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js.");
	} else {'."
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
			'width', '448',
			'height', '362',
			'src', 'playervideo/playervideo_[video_id]',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'wmode', 'transparent',
			'devicefont', 'false',
			'id', 'playervideo_[video_id]',
			'bgcolor', '#ffffff',
			'name', 'playervideo_[video_id]',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'movie', 'playervideo/playervideo_[video_id]',
			'salign', ''
			); //end AC code
	}
</script>" . '
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="448" height="362" id="playervideo_[video_id]" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="true" />
	<param name="movie" value="playervideo/playervideo_[video_id].swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />	<embed src="playervideo/playervideo_[video_id].swf" quality="high" bgcolor="#ffffff" width="448" height="362" name="playervideo_[video_id]" align="middle" allowScriptAccess="sameDomain" wmode="transparent" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>


<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/

arr_video[arr_video.length] = [video_id];

/*]]>*/
</script>

';

?>