<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" type="file" value="">
    <div class="float-right" ><a href="#" class="f-bu f-bu-default" onclick="$('#form-id-{variable:viewedit_type}-{variable:viewedit_id}').submit(); return false;">Загрузить</a></div>
    <div class="clear-both">&nbsp;</div>

    {if:viewedit_item_file_delete}
    <div>file size: {variable:viewedit_item_file_filesize}</div>
    <div>file name: {variable:viewedit_item_file_filename}</div>
    <div>file ext: {variable:viewedit_item_file_fileext}</div>
    <div class="float-left"><a href="{variable:viewedit_item_file_download}" class="f-bu">Скачать файл</a>&nbsp;<a href="{variable:viewedit_item_file_open}" class="f-bu">Открыть файл</a></div>

    <div class="float-right" ><a href="{variable:viewedit_item_file_delete}" class="f-bu f-bu-warning">Удалить файл</a></div>
    {else:viewedit_item_file_delete}
    <div class="float-right" ><span class="f-message">Файл не загружен</span></div>
    {endif:viewedit_item_file_delete}
    <div class="h10 clear-both"></div>
    <br />

    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />