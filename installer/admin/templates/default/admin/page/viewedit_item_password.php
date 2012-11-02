<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input class="" type="password" name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" size="{variable:view_edit_item_size}" value="{variable:view_edit_item_value}" {variable:view_edit_item_disabled} />
    <div class="clear-both"></div>
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />