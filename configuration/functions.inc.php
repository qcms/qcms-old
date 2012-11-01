<?php
/*
 * Файл пользовательских функций
 * */





/**
 * Функция подстановки в сказку значений шаблонов
				Шаблоны подстановки:
        {NAME} - имя
				{M:Текст для мальчика}
				{W:Текст для девочки}
				{FREND} - имя друга
				{FM:Текст для друга мальчика}
				{FW:Текст для друга девочки}
				{TM:Текст разказчика мужчины}
				{TW:Текст разказчика женщины}
 * @param string $user_sex
 * @param string $children_name
 * @param string $children_sex
 * @param string $children_frend_name
 * @param string $children_frend_sex
 * @param string $text
 */
function TaleTemplate($user_sex, $children_name, $children_sex, $children_frend_name, $children_frend_sex, &$text)
{
	// замена {NAME}
	$text = preg_replace("/".preg_quote("{NAME}")."/ims", $children_name, $text);
	// замена {FREND}
	$text = preg_replace("/".preg_quote("{FREND}")."/ims", $children_name, $text);
	
	// текст про ребенка
	if($children_sex == "1")
	{
		// для мальчика
		$text = preg_replace("/\\{W\\:[^}]*\\}/ims", "", $text); // текст для девочки
		$text = preg_replace("/\\{M\\:([^}]*)\\}/ims", '${1}', $text); // текст для мальчика
	}
	elseif($children_sex == "2")
	{
		// для девочки
		$text = preg_replace("/\\{M\\:[^}]*\\}/ims", "", $text); // текст для мальчика
		$text = preg_replace("/\\{W\\:([^}]*)\\}/ims", '${1}', $text); // текст для девочки
	}
	
	// текст про друга
	if($children_frend_sex == "1")
	{
		// для мальчика
		$text = preg_replace("/\\{FW\\:[^}]*\\}/ims", "", $text); // текст для девочки
		$text = preg_replace("/\\{FM\\:([^}]*)\\}/ims", '${1}', $text); // текст для мальчика
	}
	elseif($children_frend_sex == "2")
	{
		// для девочки
		$text = preg_replace("/\\{FM\\:[^}]*\\}/ims", "", $text); // текст для мальчика
		$text = preg_replace("/\\{FW\\:([^}]*)\\}/ims", '${1}', $text); // текст для девочки
	}
	
	// текст разказчика
	if($user_sex == "1")
	{
		// для мальчика
		$text = preg_replace("/\\{TW\\:[^}]*\\}/ims", "", $text); // текст расказчика женщины
		$text = preg_replace("/\\{TM\\:([^}]*)\\}/ims", '${1}', $text); // текст разказчика мужчины
	}
	elseif($user_sex == "2")
	{
		// для девочки
		$text = preg_replace("/\\{TM\\:[^}]*\\}/ims", "", $text); // текст разказчика мужчины
		$text = preg_replace("/\\{TW\\:([^}]*)\\}/ims", '${1}', $text); // текст расказчика женщины
	}
	
}


function GetTaleContent($tale_id, $user_id, $children_id)
{
	$ret = "";
	
	$tale_entity = new CEntity(
    array(
      "table"=>"tale",
      "id"=>$tale_id,
    	"where"=>"tale_isshow = '1'",
    )
  );
	
  if($tale_entity->identity)
  {
		$user_entity = new CEntity(
	    array(
	      "table"=>"user",
	      "id"=>$user_id,
	    	"where"=>"user_isshow = '1'",
	    )
	  );

	  if($user_entity->identity)
	  {
	  	$user_child_list = new CEntityList(
	  		array(
	  			"table" => "user_child",
      		"table_parent" => "user",
	  			"parent_id" => $user_entity->identity,
	  			"where" => "user_child_isshow = '1' AND user_child_isselected = '1'",
	  		)
	  	);
	  	
	  	if($user_child_list->GetCount() > 0)
	  	{
	  		$user_child_entity = $user_child_list->list[0];
	  		$ret = GetTaleContentEx(
	  			$tale_id, 
	  			$user_entity->GetHeader("user_sex"), 
	  			$user_child_entity->GetHeader("user_child_name"), 
	  			$user_child_entity->GetHeader("user_child_sex"), 
	  			$user_child_entity->GetHeader("user_child_frend_name"), 
	  			$user_child_entity->GetHeader("user_child_frend_name")
	  		);
	  	}
	  	
	  	
	  }
  	
  }
	
	return $ret;
}


function GetTaleContentEx($tale_id, $user_sex, $children_name, $children_sex, $children_frend_name, $children_frend_sex)
{
	$ret = "";
	$tale_module_list = new CEntityList(
		array(
			"table" => "tale_module",
			"table_parent" => "tale",
			"key_parent" => "tale_id",
			"parent_id" => $tale_id,
			"where" => "tale_module_isshow = '1'",
			"function" => "tale_module_list_show_function",
			"template" => '[template]',
		)
	);
	
	$ret = $tale_module_list->ViewList();
	TaleTemplate($user_sex, $children_name, $children_sex, $children_frend_name, $children_frend_sex, $ret);
	return $ret;
}

/**
 * Обработка вывода списка модулей сказки
 * @param CEntityList $list
 * @param string $view
 */
function tale_module_list_show_function($list, &$view)
{
	$item = $list->item;
	$template = "";
	
	switch ($item->GetHeader("tale_module_type"))
	{





		
		case "1": //  "1" => array("Текст"),
			$tale_module_text = $item->GetText("tale_module_text");
			$template = <<<EOT
				<div class="text">
					<div class="text-in">{$tale_module_text}</div>
				</div>			
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);
			break;
		case "2": //	"2" => array("Текст с изображением слева"),
			
			$tale_module_text = $item->GetText("tale_module_text");
			$tale_module_picture_template = <<<EOT
					<a rel="img_{$item->identity}" class="picture left" href="[image_file:tale_module_picture]"><div class="corner tl"></div><div class="corner tr"></div><div class="corner br"></div><div class="corner bl"></div><img class="picture-image" src="[image_file:tale_module_preview]" /></a>
					<script type="text/javascript">
					  $('a[rel="img_{$item->identity}"]').fancybox();
					</script>
EOT;
			
			$tale_module_picture = $item->View(array("template"=>$tale_module_picture_template) );
			$template = <<<EOT
				<div class="text-picture">
					<div class="text-in">
					{$tale_module_picture}
					{$tale_module_text}
					</div>
				</div>
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);
			
			break;
		case "3": //	"3" => array("Текст с изображением справа"),
			$tale_module_text = $item->GetText("tale_module_text");
			$tale_module_picture_template = <<<EOT
					<a rel="img_{$item->identity}" class="picture right" href="[image_file:tale_module_picture]"><div class="corner tl"></div><div class="corner tr"></div><div class="corner br"></div><div class="corner bl"></div><img class="picture-image" src="[image_file:tale_module_preview]" /></a>
					<script type="text/javascript">
					  $('a[rel="img_{$item->identity}"]').fancybox();
					</script>
EOT;
			
			$tale_module_picture = $item->View(array("template"=>$tale_module_picture_template) );
			$template = <<<EOT
				<div class="text-picture">
					<div class="text-in">
					{$tale_module_picture}
					{$tale_module_text}
					</div>
				</div>
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);
			break;
		case "4": //	"4" => array("Галерея изображений"),
			$tale_module_name = $item->GetHeader("tale_module_name");
			$template = <<<EOT
				<div class="gallery">
					<a class="prev" id="gallery_{$item->identity}_prev"></a>
					<a class="next" id="gallery_{$item->identity}_next"></a>
					<div class="gallery-in" id="gallery_{$item->identity}">
						<ul>
							[images]
						</ul>
					</div>
					<script type="text/javascript">
					$(function() {
					    $("#gallery_{$item->identity}").jCarouselLite({
					        btnNext: "#gallery_{$item->identity}_next",
					        btnPrev: "#gallery_{$item->identity}_prev",
					        visible: 5,
					        //auto: 1500,
					        speed: 600,
					        circular: false,
					        mouseWheel: false
					    });
					    $("a.gallery-item").attr('rel', 'gallery_{$item->identity}').fancybox();
					});
					
					</script>
		
				</div>
			
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);
			$view = preg_replace("/".preg_quote("[images]")."/ims", tale_module_gallery($item->identity), $view);
			
			break;
			
		case "5": //	"5" => array("Реклама"),
			break;
		case "50": //	"50" => array("Модуль 3 ссылки"),
			break;
		case "100": // "100" => array("Разделитель (отступ)"),
			$template = <<<EOT
				<div class="clear-both h20"></div>
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);			
			break;				
		case "101": // "101" => array("Разделитель (линия)"),
			$template = <<<EOT
				<div class="line clear-both"></div>
EOT;
			$view = preg_replace("/".preg_quote("[template]")."/ims", $template, $view);			
			break;				
		default:
			break;
	}
	
}


/**
 * Функция отображает изображения галереи для заданного модуля
 * @param integer $tale_module_id
 */
function tale_module_gallery($tale_module_id)
{
	$ret = "";
	$template = <<<EOT
							<li><a class="gallery-item" rel="gallery_{$tale_module_id}" title="[header:tale_module_image_name]" href="[image_file:tale_module_image_picture]"><img src="[image_file:tale_module_image_preview]" /></a></li>
EOT;
  $tale_module_gallery_list = new CEntityList(
    array(
      "table" => "tale_module_image",
      "table_parent" => "tale_module",
      "key_parent" => "tale_module_id",
      "parent_id" => $tale_module_id,
      "where" => "tale_module_image_isshow = '1'",
    	"template" => $template,
    )
  );
  
  $ret = $tale_module_gallery_list->ViewList();
	
	return $ret;
}

/**
 * Функция отображает изображения для заданного модуля
 * @param integer $tale_module_id
 */
function tale_module_images($tale_module_id)
{
	$ret = "";
	
	$template = <<<EOT
    <div class="photo"><a href="[image_file:tale_module_text_image_picture]" rel="tale_module_{$tale_module_id}_images" title="[header:tale_module_text_image_name]" class="galery">
    <img src="[image_file:tale_module_text_image_preview]" width="300" height="200" border="0"   /></a></div> <br /><br />
EOT;
	$template_last = <<<EOT
    <div class="photo"><a href="[image_file:tale_module_text_image_picture]" rel="tale_module_{$tale_module_id}_images" title="[header:tale_module_text_image_name]" class="galery">
    <img src="[image_file:tale_module_text_image_preview]" width="300" height="200" border="0"   /></a></div> <br /><br />

<script type="text/javascript">
	$("a[rel=tale_module_{$tale_module_id}_images]").fancybox({
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	400, 
		'speedOut'		:	400, 
		'titlePosition'	:	'over',
		'overlayOpacity' : 0.7,
		'overlayColor' : '#000',
		'onComplete'	:	function() {
			$("#fancybox-wrap").hover(function() {
				$("#fancybox-title").show();
			}, function() {
				$("#fancybox-title").hide();
			});
		}
	});
</script>
EOT;

	
  $tale_module_text_image_list = new CEntityList(
    array(
      "table" => "tale_module_text_image",
      "table_parent" => "tale_module",
      "key_parent" => "tale_module_id",
      "parent_id" => $tale_module_id,
      "where" => "tale_module_text_image_isshow = '1'",
    	"template" => $template,
    	"template_last" => $template_last,
    )
  );
  
  $ret = $tale_module_text_image_list->ViewList();

  return $ret;
}


// признак того что требуется преобразование URL
define("USE_URLREWRITE", "1");

/**
 * Функция преобразует URL в читаемый
 * из http://site.domain/index.php?id=user&a=signin
 * в http://site.domain/user/signin/
 * из http://site.domain/index.php?id=user&a=signin&b=333&c=12134
 * в http://site.domain/user/signin/333/12134/
 * 
 * @param string $url
 * @return string
 */
function GetUrlrewrite($url)
{
	$ret = $url;
	
	if(!defined("USE_URLREWRITE") || USE_URLREWRITE !== "1" )
	{
		return $ret;
	}
	
	$vn_page = VN_PAGE;
	$vn_page = str_replace("/", "\/", preg_quote($vn_page));
	$pattern = '/'.$vn_page.'\?(id=([^'.preg_quote('#"\'&').']+))(&[a-z]=([^'.preg_quote('#"\'&').']+))*(\#[^$]+)?/ims';

	$matches = false;
	if(preg_match($pattern, $ret, $matches))
	{
		//error_log(var_export($matches, true));
		if(is_array($matches) && count($matches) > 2)
		{
			$ret = VN_SERVER.VN_DIR;
			for($i=1; $i<count($matches); $i+=2)
			{
				if(isset($matches[$i+1]))
				{
					$ret .= $matches[$i+1]."/";
				}
				elseif(substr($matches[$i],0,1) == "#")
				{
					$ret .= $matches[$i];
				}
			}
		}
	}
	
	return $ret;	
}

?>