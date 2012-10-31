<span class="vieweditlist-subinfo-item-image-span">
    <img class="vieweditlist-subinfo-item-image-img vieweditlist-subinfo-item-image-img-wh" id="vieweditlist-subinfo-item-image-{variable:vieweditlist_subinfo_item_image_id}" src="{variable:VIEW_IMAGE}?id={variable:vieweditlist_subinfo_item_image_id}" alt="{variable:vieweditlist_subinfo_item_name}" border="0">
    <span >image size: <span class="vieweditlist-subinfo-item-image-sizex">{variable:vieweditlist_subinfo_item_image_sizex}</span>x<span class="vieweditlist-subinfo-item-image-sizey">{variable:vieweditlist_subinfo_item_image_sizey}</span></span>
</span>

<script type="text/javascript">
    $('#vieweditlist-subinfo-item-image-{variable:vieweditlist_subinfo_item_image_id}').load(function () {

        //alert('aaa');
        if($(this).width() < $(this).parent().parent().find(".vieweditlist-subinfo-item-image-sizex").text()
                || $(this).height() < $(this).parent().parent().find(".vieweditlist-subinfo-item-image-sizey").text()){

            var sizex = $(this).parent().parent().find(".vieweditlist-subinfo-item-image-sizex").text();
            var sizey = $(this).parent().parent().find(".vieweditlist-subinfo-item-image-sizey").text();

            //alert($(this).width() + " " + $(this).height() + " " + sizex + " " + sizey);

            $(this).before('<a class="vieweditlist-subinfo-item-image-a vieweditlist-subinfo-item-image-a-cursor-zoom-in"><div class="vieweditlist-subinfo-item-image-a-icon"></div></a>');
            $(this).appendTo($(this).parent().find('.vieweditlist-subinfo-item-image-a'));

            //alert($(this).parent().parent().find('.vieweditlist-subinfo-item-image-a').size());

            var f_click = function() {
                //alert("AAA");
                //alert(sizex + " " + sizey);
                $('#vieweditlist-subinfo-item-image-{variable:vieweditlist_subinfo_item_image_id}').removeClass('vieweditlist-subinfo-item-image-img-wh');
                $(this).unbind('click');
                $(this).removeClass('vieweditlist-subinfo-item-image-a-cursor-zoom-out');
                $(this).removeClass('vieweditlist-subinfo-item-image-a-cursor-zoom-in');
                $(this).addClass('vieweditlist-subinfo-item-image-a-cursor-zoom-out');
                $(this).parent().parent().find('.vieweditlist-subinfo-item-image-a').click(function () {
                    $('#vieweditlist-subinfo-item-image-{variable:vieweditlist_subinfo_item_image_id}').addClass('vieweditlist-subinfo-item-image-img-wh');
                    $(this).unbind('click');
                    $(this).removeClass('vieweditlist-subinfo-item-image-a-cursor-zoom-out');
                    $(this).removeClass('vieweditlist-subinfo-item-image-a-cursor-zoom-in');
                    $(this).addClass('vieweditlist-subinfo-item-image-a-cursor-zoom-in');
                    $(this).click(f_click);
                    return false;
                });
                return false;

            };

            $(this).parent().parent().find('.vieweditlist-subinfo-item-image-a').click(f_click);
            //$(this).parent().prepend('').append('</a>');

        }


    });
</script>
