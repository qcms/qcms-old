<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <textarea class="" name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" cols="{variable:view_edit_item_cols}" rows="{variable:view_edit_item_rows}" {variable:view_edit_item_disabled}>{variable:view_edit_item_value}</textarea>
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />