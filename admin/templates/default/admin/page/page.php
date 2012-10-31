<!doctype html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7" lang="ru"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" lang="ru"><![endif]-->
<!--[if IE 8]><html class="lt-ie9" lang="ru"><![endif]-->
<!--[if gt IE 8]><!--><html lang="ru"><!--<![endif]-->
<head>
    <title>QCMS - {variable:admin_current_name_custom} - {constant:VN_SERVER_NAME}</title>

    <link href="/{constant:DIRNAME_ADMIN}/s/framework.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="/{constant:DIRNAME_ADMIN}/js/html5.js"></script>
    <![endif]-->

    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/smoothness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery-ui-1.8.20.custom.min.js"></script>

    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/style.css" rel="stylesheet" />

    <!-- elRTE -->
    <link rel="stylesheet" href="/{constant:DIRNAME_ADMIN}/e/css/elrte.min.css" type="text/css" media="screen">
    <script src="/{constant:DIRNAME_ADMIN}/e/js/elrte.min.js"                  type="text/javascript"></script>
    <script src="/{constant:DIRNAME_ADMIN}/e/js/i18n/elrte.ru.js"              type="text/javascript"></script>

    <!-- elFinder -->
    <script src="/{constant:DIRNAME_ADMIN}/e/js/elfinder.min.js" type="text/javascript"></script>
    <script src="/{constant:DIRNAME_ADMIN}/e/js/i18n/elfinder.ru.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/{constant:DIRNAME_ADMIN}/e/css/elfinder.css" type="text/css" media="screen" title="no title">

    <!-- jquery.treeview -->
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery.treeview.js"></script>
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery.cookie.js"></script>
    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/jquery.treeview.css" rel="stylesheet" />

    <script src="/{constant:DIRNAME_ADMIN}/js/swfobject.js" type="text/javascript"></script>

    <script type="text/javascript" language="JavaScript">
        // editor options
        {constant:VN_EDITOR3_SETTINGS}


        var arr_editor = [];
        var current_sound = 0;
        var arr_sound = [];
        var i;
        var current_video = 0;
        var arr_video = [];
        var is_play = false;
        function set_current_sound_number(n)
        {
            current_sound = n;
        }
        function get_current_sound_number()
        {
            return current_sound;
        }
        function play_next_sound_number(n)
        {
            for(i=0; i<arr_sound.length; i++)
            {
                if(""+arr_sound[i] == ""+n)
                {
                    if(i+1<arr_sound.length)
                    {
                        //alert(i+1);
                        current_sound = arr_sound[i+1];
                        return arr_sound[i+1];
                    }
                }
            }
            is_play = false;
            current_sound=0;
            return 0;
        }
        function set_current_video_number(n)
        {
            current_video = n;
        }
        function get_current_video_number()
        {
            return current_video;
        }
        function play_next_video_number(n)
        {
            for(i=0; i<arr_video.length; i++)
            {
                if(""+arr_video[i] == ""+n)
                {
                    if(i+1<arr_video.length)
                    {
                        //alert(i+1);
                        current_video = arr_video[i+1];
                        return arr_video[i+1];
                    }
                }
            }
            is_play = false;
            current_video=0;
            return 0;
        }
        function get_is_play()
        {
            return is_play;
        }
        function set_is_play(v)
        {
            is_play = v;
        }
        function thisMovie(movieName) {
            if (navigator.appName.indexOf("Microsoft") != -1) {
                return window[movieName];
                //return document.all[movieName];
            } else {
                //alert(document[movieName]);
                return document[movieName];
                //return getElementById(movieName);
            }
        }

        var box_icon_togle = function (box_id){
            if($("#"+box_id +" .box-content").is(":visible")){
                $("#"+box_id +" .box-icon").addClass("ui-icon-minus");
                $("#"+box_id +" .box-icon").removeClass("ui-icon-plus");
                $.cookie(box_id+"_isshow", 1);
            }
            else{
                $("#"+box_id +" .box-icon").removeClass("ui-icon-minus");
                $("#"+box_id +" .box-icon").addClass("ui-icon-plus");
                $.cookie(box_id+"_isshow", 0);
            }
        }


        var box_show = function (box_id) {
            var box = $("#"+box_id);

            $("#"+box_id +" .box-content").show();

            box_icon_togle(box_id);
            return false;
        }


        var box_hide = function (box_id) {
            var box = $("#"+box_id);

            $("#"+box_id +" .box-content").hide();

            box_icon_togle(box_id);
            return false;
        }

        var box_toggle = function (box_id) {
            var box = $("#"+box_id);

            $("#"+box_id +" .box-content").toggle();

            box_icon_togle(box_id);
            return false;
        }



        $().ready(function () {
            //alert('a');
        });

        $(window).load(function () {
            // store current scrollTop for window
            var scroll_top = 0;
            if($(window).scrollTop()<$(window).height()){
                scroll_top = $(window).scrollTop();
            }


            // message close
            $(".f-message .close").click(function () {
                $(this).parent().hide();
            });


            // tabs
            $( ".view-edit-tabs" ).tabs({
                cookie: {
                    // store cookie for a day, without, it would be a session cookie
                    expires: 1
                }
            });


            // fix height
            if( ($(".content-right").height()+$(".header").height()+$(".footer").height()+20) < $(window).height() ){
                $(".content-right").height($(window).height() - $(".header").height() - $(".footer").height() - 20);
            }
            if($(".sidebar").height() < $(".content-right").height()){
                $(".sidebar").height($(".content-right").height());
            }


            // delete button
            $('.delete-button').click(function () {
                //alert("AAA");
                if(confirm("{lang_message:ENTITY_DELETE_CONFIRM}?"))
                {
                    return true;
                }
                else
                {
                    return false;
                }


                return false;
            });

            // restore scrollTop for window
            $(window).scrollTop(scroll_top);
            //alert(scroll_top);
        });
    </script>

</head>
<body>

<div class="g">
    <div class="g-row gray p5px header">
        <header>
            {function:admin_show_breadcrumbs}
        </header>
    </div><!-- g-row -->
    <div class="g-row content">
        <div class="g-3 gray sidebar">
            {function:admin_show_lang}
            {function:admin_show_auth}
            {function:admin_show_left_menu}
        </div><!-- g-4 -->
        <div class="g-9 content-right">
            <div class="p5px">
                {function:admin_show_messages}

                {function:admin_show_content}

                <br />
                <br />


            </div>
        </div><!-- g-8 -->
    </div><!-- g-row -->
    <div class="g-row gray footer p5px">
        {function:admin_show_footer}
    </div><!-- g-row -->
</div><!-- g -->

</body>
</html>