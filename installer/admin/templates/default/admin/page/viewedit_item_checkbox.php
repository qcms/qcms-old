<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input class="" type="checkbox" name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" value="1" {variable:view_edit_item_checked} {variable:view_edit_item_disabled} />
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />