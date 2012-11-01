<div id="box_id_{variable:box_id}" class="box ui-widget ui-widget-content ui-corner-all">
    <div class="box-header ui-widget-header ui-corner-all">
        <a class="box-button" onclick="box_toggle('box_id_{variable:box_id}'); return false;"><span class="box-icon ui-icon ui-icon-minus"></span>{variable:box_header}</a>
    </div>
    <div class="box-content">
        {variable:box_content}
    </div>
</div>
<script type="text/javascript">
    if($.cookie("box_id_{variable:box_id}_isshow") == 0)
    {
        box_hide('box_id_{variable:box_id}');
    }
    else
    {
        box_show('box_id_{variable:box_id}');
    }

</script>
