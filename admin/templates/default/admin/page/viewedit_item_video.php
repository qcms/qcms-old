<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" type="file" value="">
    <div class="float-right" ><a href="#" class="f-bu f-bu-default" onclick="$('#form-id-{variable:viewedit_type}-{variable:viewedit_id}').submit(); return false;">Загрузить</a></div>
    <div class="clear-both">&nbsp;</div>

    {if:viewedit_item_video_delete}
    <div class="videoplayer" id="vp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}">You need Flash player 8+ and JavaScript enabled to view this video.</div>

    <div class="float-right" ><a href="{variable:viewedit_item_video_delete}" class="f-bu f-bu-warning">Удалить файл</a></div>
    {else:viewedit_item_video_delete}
    <div class="float-right" ><span class="f-message">Файл не загружен</span></div>
    {endif:viewedit_item_video_delete}
    <div class="clearfix"></div>
    <div class="h5"></div>

    <script type="text/javascript">
        var params = { allowScriptAccess: "always", wmode: "transparent", allowFullScreen: "true"	 };
        var atts = { id: "vp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}", styleclass: "videoplayer" };

        swfobject.embedSWF(
            "/playervideo/playervideo.swf?table={variable:view_edit_item_table}&video={variable:view_edit_item_id}&preview={variable:view_edit_item_preview}&id={variable:view_edit_item_identity}",
            "vp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}",
            "{variable:view_edit_item_video_width}",
            "{variable:view_edit_item_video_height}",
            "8",
            null,
            null,
            params,
            atts
        );

        arr_video[arr_video.length] = {variable:view_edit_item_video_id};
    </script>

    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />