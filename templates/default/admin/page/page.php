<!doctype html>
<!--[if lt IE 7]><html class="lt-ie9 lt-ie8 lt-ie7" lang="ru"><![endif]-->
<!--[if IE 7]><html class="lt-ie9 lt-ie8" lang="ru"><![endif]-->
<!--[if IE 8]><html class="lt-ie9" lang="ru"><![endif]-->
<!--[if gt IE 8]><!--><html lang="ru"><!--<![endif]-->
<head>
    <title>{variable:page_name}</title>

    <link href="/{constant:DIRNAME_ADMIN}/s/framework.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="/{constant:DIRNAME_ADMIN}/js/html5.js"></script>
    <![endif]-->

    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/smoothness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery-ui-1.8.20.custom.min.js"></script>

    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/style.css" rel="stylesheet" />

    <!-- elRTE -->
    <link rel="stylesheet" href="/{constant:DIRNAME_ADMIN}/e/css/elrte.min.css" type="text/css" media="screen" charset="utf-8">
    <script src="/{constant:DIRNAME_ADMIN}/e/js/elrte.min.js"                  type="text/javascript" charset="utf-8"></script>
    <script src="/{constant:DIRNAME_ADMIN}/e/js/i18n/elrte.ru.js"              type="text/javascript" charset="utf-8"></script>

    <!-- elFinder -->
    <script src="/{constant:DIRNAME_ADMIN}/e/js/elfinder.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/{constant:DIRNAME_ADMIN}/e/js/i18n/elfinder.ru.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="/{constant:DIRNAME_ADMIN}/e/css/elfinder.css" type="text/css" media="screen" title="no title" charset="utf-8">

    <!-- jquery.treeview -->
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery.treeview.js"></script>
    <script type="text/javascript" src="/{constant:DIRNAME_ADMIN}/js/jquery.cookie.js"></script>
    <link type="text/css" href="/{constant:DIRNAME_ADMIN}/s/jquery.treeview.css" rel="stylesheet" />


    <script type="text/javascript" language="JavaScript">

        // editor options
        var editor_opts = {
            lang         : 'ru',   // set your language
            styleWithCSS : true,
            width        : 630,
            height       : 300,
            toolbar      : 'maxi'
        };

        var box_toggle = function (box_id) {
            var box = $("#"+box_id);

            $("#"+box_id +" .box-content").toggle();


            if($("#"+box_id +" .box-content").is(":visible")){
                $("#"+box_id +" .box-icon").addClass("ui-icon-minus");
                $("#"+box_id +" .box-icon").removeClass("ui-icon-plus");
            }
            else{
                $("#"+box_id +" .box-icon").removeClass("ui-icon-minus");
                $("#"+box_id +" .box-icon").addClass("ui-icon-plus");
            }
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
            $( ".view-edit-tabs" ).tabs();


            // fix height
            if( ($(".content-right").height()+$(".header").height()+$(".footer").height()+20) < $(window).height() ){
                $(".content-right").height($(window).height() - $(".header").height() - $(".footer").height() - 20);
            }
            if($(".sidebar").height() < $(".content-right").height()){
                $(".sidebar").height($(".content-right").height());
            }


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
        {function:admin_breadcrumbs_box}
        <ul class="f-breadcrumbs">
            <li><a href="#">Главная</a></li>
            <li><a href="#">Категория</a></li>
            <li><a href="#">Вложенная категория</a></li>
            <li><a href="#">Вложенная категория</a></li>
            <li><a href="#">Вложенная категория</a></li>
            <li class="cur">Продукт</li>
        </ul><!-- f-breadcrumb -->
    </header>
</div><!-- g-row -->
<div class="g-row content">
<div class="g-3 gray sidebar">
    {function:admin_auth_box}
    <div class="g-row p5px">
        <div class="float-left">AdminLogin:</div>
        <div class="float-right"><a href="#">выход</a></div>
    </div>
    {function:admin_left_menu_box}
    <div class="g-row">
        <ul class="f-nav-list">
            <li><a href="#">Первый пункт</a></li>
            <li class="active"><a href="#">Второй активный</a></li>
            <li><a href="#">Третий</a></li>
            <li><a href="#">Последний пункт</a></li>
        </ul><!-- f-nav-list -->
    </div>
</div><!-- g-4 -->
<div class="g-9 content-right">
<div class="p5px">

{function:admin_show_messages_box}

<div class="f-message f-message-success">
    <span class="close"></span>
    <strong>Внимание</strong>, текст успешного сообщения
</div><!--f-message -->
<div class="f-message">
    <span class="close"></span>
    <strong>Внимание</strong>, текст простого сообщения
</div><!--f-message -->
<div class="f-message f-message-error">
    <span class="close"></span>
    <strong>Внимание</strong>, текст ошибки
</div><!--f-message -->


<div id="box_id1" class="hidden box ui-widget ui-widget-content ui-corner-all">
    <div class="box-header ui-widget-header ui-corner-all">
        <a class="box-button" onclick="box_toggle('box_id1'); return false;"><span class="box-icon ui-icon ui-icon-minus"></span>Заголовок 1</a>
    </div>
    <div class="box-content">


        <div class="view-edit">
            <form class="">

                <div class="view-edit-tabs" id="view-edit-tabs-id1">

                    <ul class="">
                        <li class="tab-header"><a href="#view-edit-tab-tabid1">Первый пункт</a></li>
                        <li class="tab-header"><a href="#view-edit-tab-tabid2">Второй</a></li>
                        <li class="tab-header"><a href="#view-edit-tab-tabid3">Третий</a></li>
                        <li class="tab-header"><a href="#view-edit-tab-tabid4">Последний пункт</a></li>
                        <li><a onclick="$('#view-edit-tabs-id1 .tab-header').hide();$(this).parent().hide(); $(this).parent().next().show(); $('.view-edit-tabs .view-edit-tab').removeClass('ui-tabs-hide'); return false;">Show All</a></li>
                        <li class="hidden"><a onclick="$('#view-edit-tabs-id1 .tab-header').show(); $(this).parent().hide(); $(this).parent().prev().show(); $('#view-edit-tabs-id1').tabs('destroy'); $('#view-edit-tabs-id1').tabs(); return false;">Show Tabs</a></li>
                    </ul>

                    <div id="view-edit-tab-tabid1" class="view-edit-tab">

                        <div class="f-row">
                            <label for="view-edit-id1">ID:</label>
                            <input class="" type="text" id="view-edit-id1" size="6" readonly="readonly" value="123" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-string-id1">Название поля строка:</label>
                            <input class="" type="text" id="view-edit-string-id1" size="30" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-int-id1">Название поля число:</label>
                            <input class="" type="text" id="view-edit-int-id1" size="6" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-text-id1">Название поля текст:</label>
                            <textarea class="" id="view-edit-text-id1" cols="50" rows="10"></textarea>
                        </div>
                        <div class="f-row">
                            <label for="view-edit-editor-id1">Название поля редактор:</label>
                            <textarea class="editor" id="view-edit-editor-id1" cols="50" rows="10"></textarea>
                            <script type="text/javascript" charset="utf-8">

                                // create editor
                                $('#view-edit-editor-id1').elrte(editor_opts);
                            </script>
                        </div>

                    </div>
                    <div id="view-edit-tab-tabid2" class="view-edit-tab">
                        <div class="f-row">
                            <label for="view-edit-string-id2">Название поля строка:</label>
                            <input class="" type="text" id="view-edit-string-id2" size="30" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-int-id2">Название поля число:</label>
                            <input class="" type="text" id="view-edit-int-id2" size="6" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-text-id2">Название поля текст:</label>
                            <textarea class="" id="view-edit-text-id2" cols="50" rows="10"></textarea>
                        </div>
                        <div class="f-row">
                            <label for="view-edit-editor-id2">Название поля редактор:</label>
                            <textarea class="" id="view-edit-editor-id2" cols="50" rows="10"></textarea>
                            <script type="text/javascript" charset="utf-8">

                                // create editor
                                $('#view-edit-editor-id2').elrte({
                                    lang         : 'ru',   // set your language
                                    styleWithCSS : true,
                                    width        : 650,
                                    height       : 300,
                                    toolbar      : 'maxi'
                                });
                            </script>
                        </div>


                    </div>
                    <div id="view-edit-tab-tabid3" class="view-edit-tab">
                        <div class="f-row">
                            <label for="view-edit-string-id3">Название поля строка:</label>
                            <input class="" type="text" id="view-edit-string-id3" size="30" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-int-id3">Название поля число:</label>
                            <input class="" type="text" id="view-edit-int-id3" size="6" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-text-id3">Название поля текст:</label>
                            <textarea class="" id="view-edit-text-id3" cols="50" rows="10"></textarea>
                        </div>
                        <div class="f-row">
                            <label for="view-edit-editor-id3">Название поля редактор:</label>
                            <textarea class="" id="view-edit-editor-id3" cols="50" rows="10"></textarea>
                            <script type="text/javascript" charset="utf-8">

                                // create editor
                                $('#view-edit-editor-id3').elrte({
                                    lang         : 'ru',   // set your language
                                    styleWithCSS : true,
                                    width        : 650,
                                    height       : 300,
                                    toolbar      : 'maxi'
                                });
                            </script>
                        </div>


                    </div>
                    <div id="view-edit-tab-tabid4" class="view-edit-tab">
                        <div class="f-row">
                            <label for="view-edit-string-id4">Название поля строка:</label>
                            <input class="" type="text" id="view-edit-string-id4" size="30" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-int-id4">Название поля число:</label>
                            <input class="" type="text" id="view-edit-int-id4" size="6" />
                        </div>
                        <div class="f-row">
                            <label for="view-edit-text-id4">Название поля текст:</label>
                            <textarea class="" id="view-edit-text-id4" cols="50" rows="10"></textarea>
                        </div>
                        <div class="f-row">
                            <label for="view-edit-editor-id4">Название поля редактор:</label>
                            <textarea class="" id="view-edit-editor-id4" cols="50" rows="10"></textarea>
                            <script type="text/javascript" charset="utf-8">

                                // create editor
                                $('#view-edit-editor-id4').elrte({
                                    lang         : 'ru',   // set your language
                                    styleWithCSS : true,
                                    width        : 650,
                                    height       : 300,
                                    toolbar      : 'maxi'
                                });
                            </script>
                        </div>

                    </div>
                </div>

                <script>
                </script>

                <div class="f-actions">
                    <button type="submit" class="f-bu">Сохранить</button>
                    <button type="reset" class="f-bu">Отменить</button>
                </div><!-- f-actions -->
            </form>
        </div>

    </div>
</div>


<div id="box_id2" class="box ui-widget ui-widget-content ui-corner-all">
    <div class="box-header ui-widget-header ui-corner-all">
        <a class="box-button" onclick="box_toggle('box_id2'); return false;"><span class="box-icon ui-icon ui-icon-minus"></span>Заголовок 2</a>
    </div>
    <div class="box-content">

        <div class="view-edit-list" id="view-edit-list-id">
            <div class="view-edit-list-controls" id="view-edit-list-controls-id">
                <a class="control"><img src="s/images/plus.gif" />&nbsp;Свернуть</a>
                <a class="control"><img src="s/images/minus.gif" />&nbsp;Развернуть</a>
            </div>
            <ul class="treeview view-edit-list-ul treeview" id="view-edit-list-ul-id">
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                    <ul>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                            <ul>
                                <li class="view-edit-list-li">
                                    <div class="item">
                                        <div class="info">Название</div>
                                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                        <div class="clear-both"></div>
                                    </div>
                                </li>
                                <li class="view-edit-list-li">
                                    <div class="item">
                                        <div class="info">Название</div>
                                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                        <div class="clear-both"></div>
                                    </div>
                                </li>
                                <li class="view-edit-list-li">
                                    <div class="item">
                                        <div class="info">Название</div>
                                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                        <div class="clear-both"></div>
                                    </div>
                                </li>
                                <li class="view-edit-list-li">
                                    <div class="item">
                                        <div class="info">Название</div>
                                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                        <div class="clear-both"></div>
                                    </div>
                                </li>
                                <li class="view-edit-list-li">
                                    <div class="item">
                                        <div class="info">Название</div>
                                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                        <div class="clear-both"></div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                    <ul>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                        <li class="view-edit-list-li">
                            <div class="item">
                                <div class="info">Название</div>
                                <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                                <div class="clear-both"></div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                </li>
                <li class="view-edit-list-li">
                    <div class="item">
                        <div class="info">Название</div>
                        <div class="action"><a href="#">Изменить</a><a href="#">Вверх</a><a href="#">Вниз</a></div>
                        <div class="clear-both"></div>
                    </div>
                </li>
            </ul>
            <div class="clear-both"></div>
        </div>
        <script type="text/javascript" language="JavaScript">
            $('#view-edit-list-ul-id').ready(function () {
                //alert($('.view-edit-list-li .item').size());



                $('#view-edit-list-ul-id').treeview({
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

    </div>
</div>

<br />
<br />


</div>
</div><!-- g-8 -->
</div><!-- g-row -->
<div class="g-row gray footer p5px">
    <div>
        {function:admin_show_footer_box}
        &copy; QCMS - 2012
    </div>
</div><!-- g-row -->
</div><!-- g -->

</body>
</html>