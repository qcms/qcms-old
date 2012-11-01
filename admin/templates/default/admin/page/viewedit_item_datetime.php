<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input class="" type="text" name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" maxlength="19" size="20" value="{variable:view_edit_item_value}" {variable:view_edit_item_disabled} />
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />