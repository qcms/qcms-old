<div class="view-edit">

    <form action="{variable:viewedit_action}?action=edit&id={variable:viewedit_id}" class="" id="form-id-{variable:viewedit_type}-{variable:viewedit_id}" method="post" enctype="multipart/form-data">

        <div class="view-edit-header">
            <div class="f-row">
                <span class="view-edit-header-name">OBJECT:</span>
                <input class="" type="text" id="view-edit-header-type" size="20" readonly="readonly" value="{variable:viewedit_type}" />
                <span class="view-edit-header-name">ID:</span>
                <input class="" type="text" id="view-edit-header-id" size="6" readonly="readonly" value="{variable:viewedit_id}" />
            </div>
            <div class="f-row f-actions">
                <button type="submit" class="f-bu f-bu-default">Сохранить</button>
                <button type="reset" class="f-bu">Отменить</button>
                {if:viewedit_delete_link_up}
                <a href="{variable:viewedit_delete_link_up}" class="f-bu f-bu-warning float-right delete-button">Удалить</a>
                {endif:viewedit_delete_link_up}
            </div><!-- f-actions -->
        </div>

        <div class="view-edit-tabs" id="view-edit-tabs-{variable:viewedit_id}">

            <ul class="">
                {variable:view_edit_tab_menus}
                <li><a onclick="$('#view-edit-tabs-{variable:viewedit_id} .tab-header').hide();$(this).parent().hide(); $(this).parent().next().show(); $('.view-edit-tabs .view-edit-tab').removeClass('ui-tabs-hide'); return false;">{variable:viewedit_tabs_show_all}</a></li>
                <li class="hidden"><a onclick="$('#view-edit-tabs-{variable:viewedit_id} .tab-header').show(); $(this).parent().hide(); $(this).parent().prev().show(); $('#view-edit-tabs-{variable:viewedit_id}').tabs('destroy'); $('#view-edit-tabs-{variable:viewedit_id}').tabs(); return false;">{variable:viewedit_tabs_show_tabs}</a></li>
            </ul>

            {variable:view_edit_tabs}
        </div>

        <script>
        </script>

        <div class="f-row f-actions">
            <button type="submit" class="f-bu f-bu-default">Сохранить</button>
            <button type="reset" class="f-bu">Отменить</button>
            {if:viewedit_delete_link_down}
            <a href="{variable:viewedit_delete_link_down}" class="f-bu f-bu-warning float-right delete-button">Удалить</a>
            {endif:viewedit_delete_link_down}
        </div><!-- f-actions -->
    </form>
</div>