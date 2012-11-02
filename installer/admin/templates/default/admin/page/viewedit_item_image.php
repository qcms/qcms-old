<div class="f-row">
    <label for="{variable:view_edit_item_id}">{variable:view_edit_item_name}:</label>
    <input class="" type="file" name="{variable:view_edit_item_id}" id="{variable:view_edit_item_id}" size="{variable:view_edit_item_size}" value="" {variable:view_edit_item_disabled} />
    <div class="float-right" ><a href="#" class="f-bu f-bu-default" onclick="$('#form-id-{variable:viewedit_type}-{variable:viewedit_id}').submit(); return false;">Загрузить</a></div>
    <div class="clear-both" style="height: 10px;"></div>
    <div class="viewedit-item-image">
        {if:viewedit_item_image_delete}
        <div class="viewedit-item-image-div">
            <img class="viewedit-item-image-img viewedit-item-image-img-wh" id="viewedit-item-image-{variable:view_edit_item_id}" src="{variable:VIEW_IMAGE}?id={variable:viewedit_item_image_id}" alt="{variable:view_edit_item_name}" border="0">
        </div>

        <div>image size: <span class="viewedit-item-image-sizex">{variable:viewedit_item_image_sizex}</span>x<span class="viewedit-item-image-sizey">{variable:viewedit_item_image_sizey}</span></div>
        <div>file size: {variable:viewedit_item_image_filesize}</div>
        <div>file name: {variable:viewedit_item_image_filename}</div>
        <div class="float-right" ><a href="{variable:viewedit_item_image_delete}" class="f-bu f-bu-warning delete-button">Удалить файл</a></div>
        {else:viewedit_item_image_delete}
        <div class="float-right" ><span class="f-message">Файл не загружен</span></div>
        {endif:viewedit_item_image_delete}
        <div class="clearfix"></div>
        <div class="h5"></div>
    </div>
    <script type="text/javascript">
        $('#viewedit-item-image-{variable:view_edit_item_id}').load(function () {

            if($(this).width() < $(this).parent().parent().find(".viewedit-item-image-sizex").text()
                || $(this).height() < $(this).parent().parent().find(".viewedit-item-image-sizey").text()){

                var sizex = $(this).parent().parent().find(".viewedit-item-image-sizex").text();
                var sizey = $(this).parent().parent().find(".viewedit-item-image-sizey").text();

                //alert($(this).width() + " " + $(this).height() + " " + sizex + " " + sizey);

                $(this).before('<a class="viewedit-item-image-a viewedit-item-image-a-cursor-zoom-in"><div class="viewedit-item-image-a-icon"></div></a>');
                $(this).appendTo($(this).parent().find('.viewedit-item-image-a'));

                //alert($(this).parent().parent().find('.viewedit-item-image-a').size());

                var f_click = function() {
                    //alert("AAA");
                    //alert(sizex + " " + sizey);
                    $('#viewedit-item-image-{variable:view_edit_item_id}').removeClass('viewedit-item-image-img-wh');
                    $(this).unbind('click');
                    $(this).removeClass('viewedit-item-image-a-cursor-zoom-out');
                    $(this).removeClass('viewedit-item-image-a-cursor-zoom-in');
                    $(this).addClass('viewedit-item-image-a-cursor-zoom-out');
                    $(this).parent().parent().find('.viewedit-item-image-a').click(function () {
                        $('#viewedit-item-image-{variable:view_edit_item_id}').addClass('viewedit-item-image-img-wh');
                        $(this).unbind('click');
                        $(this).removeClass('viewedit-item-image-a-cursor-zoom-out');
                        $(this).removeClass('viewedit-item-image-a-cursor-zoom-in');
                        $(this).addClass('viewedit-item-image-a-cursor-zoom-in');
                        $(this).click(f_click);
                        return false;
                    });
                    return false;

                };

                $(this).parent().parent().find('.viewedit-item-image-a').click(f_click);
                //$(this).parent().prepend('').append('</a>');

            }
            

        });
    </script>
    {if:view_edit_item_help}
    <div class="f-input-help">{variable:view_edit_item_help}</div>
    {endif:view_edit_item_help}
</div>
<hr class="clear-both" />