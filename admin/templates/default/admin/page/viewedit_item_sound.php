<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" type="file" value="">
    <div class="float-right" ><a href="#" class="f-bu f-bu-default" onclick="$('#form-id-{variable:viewedit_type}-{variable:viewedit_id}').submit(); return false;">Загрузить</a></div>
    <div class="clear-both">&nbsp;</div>

    {if:viewedit_item_sound_delete}
    <div class="soundplayer" id="sp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}">You need Flash player 8+ and JavaScript enabled to view this video.</div>

    <div class="float-right" ><a href="{variable:viewedit_item_sound_delete}" class="f-bu f-bu-warning">Удалить файл</a></div>
    {else:viewedit_item_sound_delete}
    <div class="float-right" ><span class="f-message">Файл не загружен</span></div>
    {endif:viewedit_item_sound_delete}
    <div class="clearfix"></div>
    <div class="h5"></div>

    <script type="text/javascript">
        var params = { allowScriptAccess: "always", wmode: "transparent", allowFullScreen: "true"	 };
        var atts = { id: "sp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}", styleclass: "soundplayer" };

        swfobject.embedSWF(
            "/playersound/playersound.swf?table={variable:view_edit_item_table}&sound={variable:view_edit_item_id}&id={variable:view_edit_item_identity}",
            "sp_{variable:view_edit_item_id}_{variable:view_edit_item_identity}",
            "448",
            "20",
            "8",
            null,
            null,
            params,
            atts
        );

        arr_sound[arr_sound.length] = {variable:view_edit_item_sound_id};
    </script>
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />