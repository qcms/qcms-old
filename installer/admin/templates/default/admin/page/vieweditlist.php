<div class="view-edit-list" id="view-edit-list-id-{variable:view_edit_list_id}">
    {if:vieweditlist_ishierarchy}
    <div class="view-edit-list-controls" id="view-edit-list-controls-id">
        <a class="control"><img src="s/images/plus.gif" />&nbsp;Свернуть</a>
        <a class="control"><img src="s/images/minus.gif" />&nbsp;Развернуть</a>
    </div>
    {endif:vieweditlist_ishierarchy}
    {variable:vieweditlist_navigation_pager}
    <ul class="treeview view-edit-list-ul treeview" id="view-edit-list-ul-id-{variable:view_edit_list_id}">
        {variable:vieweditlist_items}
    </ul>
    {variable:vieweditlist_navigation_pager}
    <div class="clear-both"></div>
</div>
<script type="text/javascript" language="JavaScript">
    $('#view-edit-list-ul-id-{variable:view_edit_list_id}').ready(function () {
        //alert($('.view-edit-list-li .item').size());


        var subinfo_offset_left = 0;
        var subinfo_item_offset_left = [];
        var i=0;

        $('#view-edit-list-ul-id-{variable:view_edit_list_id} .subinfo').each(function () {
            if($(this).offset().left > subinfo_offset_left){
                subinfo_offset_left = $(this).offset().left;
            }
        });
        $('#view-edit-list-ul-id-{variable:view_edit_list_id} .subinfo').offset({ left: subinfo_offset_left});

        $('#view-edit-list-ul-id-{variable:view_edit_list_id} .subinfo').each(function () {
            if($(this).find('.subinfo-item').size()>1)
            {
                for(i=1; i<$(this).find('.subinfo-item').size(); i++)
                {
                    if(subinfo_item_offset_left[i] == undefined){
                        subinfo_item_offset_left[i] = 0;
                    }

                    var v1 = $($(this).find('.subinfo-item')[i]);
                    if(subinfo_item_offset_left[i] < v1.offset().left){
                        subinfo_item_offset_left[i] = v1.offset().left;
                    }

                }
            }
        });

        $('#view-edit-list-ul-id-{variable:view_edit_list_id} .subinfo').each(function () {
            for(i=1; i<subinfo_item_offset_left.length; i++){
                var v1 = $($(this).find('.subinfo-item')[i]);
                v1.offset({left: subinfo_item_offset_left[i]});
            }
        });

        $('#view-edit-list-ul-id-{variable:view_edit_list_id}').treeview({
            animated: "fast"
            , control:"#view-edit-list-controls-id"
            , persist: "cookie"
            , cookieId: "treeview"
            //, prerendered: true
            //, persist: "location"
        });

        $('.view-edit-list-li .item').each(function (index) {
            if(index % 2 == 0){
                $(this).addClass("item-odd");
            }
        });


    });
</script>
