<?php
/*
 * Файл пользовательских функций XAJAX
 * подключается если в CMain параметр $MAIN->need_xajax==true
 * */

global $xajax;

//// регистрация XAJAX функции
// $xajax->register(XAJAX_FUNCTION, "some_function_name");
///**
// * Функция обработки XAJAX запроса
// * может быть вызвана в JavaScript как "xajax_some_function_name(some_pram)"
// * @param unknown_type $some_param
// * @return xajaxResponse
// */
//function some_function_name($some_param)
//{
//	$response = new xajaxResponse();
//	
//	return $response;
//}


// регистрация XAJAX функциий
$xajax->register(XAJAX_FUNCTION, "profile_edit_form_submit");
$xajax->register(XAJAX_FUNCTION, "profile_child_edit_form_submit");
$xajax->register(XAJAX_FUNCTION, "profile_child_delete");
$xajax->register(XAJAX_FUNCTION, "profile_show_child_list");
$xajax->register(XAJAX_FUNCTION, "profile_select_child");
$xajax->register(XAJAX_FUNCTION, "profile_show_child_edit");
$xajax->register(XAJAX_FUNCTION, "profile_show_current_child_name");


function profile_show_current_child_name()
{
	$response = new xajaxResponse();
	
	profile_show_selected_child_name_ex($response);
	return $response;
}

/**
 * Функция отображения имени текущего ребенка в шапке сайта
 * @param xajaxResponse $response
 */
function profile_show_selected_child_name_ex($response)
{
	global $MAIN, $user;
	
	$user_child_entity_list = new CEntityList(
		array(
			"table" => "user_child",
			"table_parent" => "user",
			"key_parent" => "user_id",
			"parent_id" => $user->identity,
			"where" => "user_child_isshow = '1' AND user_child_isselected='1'",
			"limit" => "LIMIT 0,1",
		)
	);
	
	if($user_child_entity_list->GetCount())
	{
		$user_child = $user_child_entity_list->list[0];
		$response->assign("header-auth-profile-child-name","innerHTML", $user_child->GetHeader("user_child_name"));
	}
	else 
	{
		$response->assign("header-auth-profile-child-name","innerHTML", "не выбран");
	}
	
	return;
}

/**
 * Функция удаления ребенка
 * @param unknown_type $child_id
 * @return xajaxResponse
 */
function profile_child_delete($child_id)
{
	global $MAIN, $user;
	$response = new xajaxResponse();
	
	
	$user_child_entity_list = new CEntityList(
		array(
			"table" => "user_child",
			"table_parent" => "user",
			"key_parent" => "user_id",
			"parent_id" => $user->identity,
			"where" => "user_child_isshow = '1' AND user_child_id='".intval($child_id)."'",
			"limit" => "LIMIT 0,1",
		)
	);

	if($user_child_entity_list->GetCount()>0)
	{
		$user_child = $user_child_entity_list->list[0];
		$user_child->Save(array("user_child_isshow" => "0"));
		$response->alert("Информация удалена!");

		$user_child_entity_list = new CEntityList(
			array(
				"table" => "user_child",
				"table_parent" => "user",
				"key_parent" => "user_id",
				"parent_id" => $user->identity,
				"where" => "user_child_isshow = '1' AND user_child_isselected='1'",
				"limit" => "LIMIT 0,1",
			)
		);
		if(!$user_child_entity_list->GetCount())
		{
			$user_child_entity_list = new CEntityList(
				array(
					"table" => "user_child",
					"table_parent" => "user",
					"key_parent" => "user_id",
					"parent_id" => $user->identity,
					"where" => "user_child_isshow = '1'",
					"limit" => "LIMIT 0,1",
				)
			);
			if($user_child_entity_list->GetCount())
			{
				$user_child_entity_list->list[0]->Save(array("user_child_isselected"=>"1"));
			}
			
		}
		profile_show_selected_child_name_ex($response);
		$response->script("xajax_profile_show_child_list();");
	}
	
	
	return $response;
}


/**
 * Функция отображения формы редактирования ребенка
 * @param integer $child_id
 * @return xajaxResponse
 */
function profile_show_child_edit($child_id)
{
	global $MAIN, $user, $user_child_sexs;
	
	$response = new xajaxResponse();
	
	if(intval($child_id) > 0)
	{
		$user_child_entity_list = new CEntityList(
			array(
				"table" => "user_child",
				"table_parent" => "user",
				"key_parent" => "user_id",
				"parent_id" => $user->identity,
				"where" => "user_child_isshow = '1' AND user_child_id='".intval($child_id)."'",
				"limit" => "LIMIT 0,1",
			)
		);
	
		if($user_child_entity_list->GetCount()>0)
		{
			$user_child = $user_child_entity_list->list[0];
	
			$user_child_name = $user_child->GetHeader("user_child_name");
			$user_child_frend_name = $user_child->GetHeader("user_child_frend_name");
			
			$form_text = <<<EOT
<div class="header">Редактирование информации о ребенке {$user_child_name}:</div>
<div class="clear-both"></div>
<a href="#" onclick="xajax_profile_show_child_list();return false;">Вернуться в список</a>
<div class="clear-both"></div>

							<form method="post" id="profile-child-edit-form" onsubmit="xajax_profile_child_edit_form_submit(xajax.getFormValues('profile-child-edit-form')); return false;" action="#">
								<input name="child_id" value="{$user_child->identity}" type="hidden" />
								<fieldset>
									<label class="float-left">Имя ребенка:</label>
									<input name="user_profile_user_child_name" id="user_profile_user_child_name" class="float-left text" type="text" value="{$user_child_name}" />
								</fieldset>
								<fieldset>
									<label class="float-left">Пол:</label>
									<select name="user_profile_user_child_sex" id="user_profile_user_child_sex" class="combobox">
EOT;

									
			if(!$user_child->GetHeader("user_child_sex"))
			{
				$form_text .= <<<EOT
										<option value="0">Не выбрано</option>
EOT;
			}
			foreach ($user_child_sexs as $key=>$value)
			{
				$selected = $key==$user_child->GetHeader("user_child_sex")?" selected":"";
				$option_value = $MAIN->GetCurrentArrayLang($value);
				$form_text .= <<<EOT
										<option value="{$key}" {$selected}>{$option_value}</option>
EOT;
			}
			$form_text .= <<<EOT
									</select>
								</fieldset>
								
								<fieldset>
									<label class="float-left">Имя друга ребенка:</label>
									<input name="user_profile_user_child_frend_name" id="user_profile_user_child_frend_name" class="float-left text" type="text" value="{$user_child_frend_name}" />
								</fieldset>
								<fieldset>
									<label class="float-left">Пол друга ребенка:</label>
									<select name="user_profile_user_child_frend_sex" id="user_profile_user_child_frend_sex" class="combobox">
EOT;

									
			if(!$user_child->GetHeader("user_child_frend_sex"))
			{
				$form_text .= <<<EOT
										<option value="0">Не выбрано</option>
EOT;
			}
			foreach ($user_child_sexs as $key=>$value)
			{
				$selected = $key==$user_child->GetHeader("user_child_frend_sex")?" selected":"";
				$option_value = $MAIN->GetCurrentArrayLang($value);
				$form_text .= <<<EOT
										<option value="{$key}" {$selected}>{$option_value}</option>
EOT;
			}
			$form_text .= <<<EOT
									</select>
								</fieldset>
								
								<fieldset>
									<input type="button" class="cancel float-left" onclick="$('#profile-child-edit-form').each (function(){ this.reset(); }); return false;" value="Отменить" />
									<input type="button" class="save float-left" onclick="$('#profile-child-edit-form').submit(); return false;" value="Сохранить" />
									<input type="button" class="delete float-right" onclick="if(confirm('Точно удалить?')) { xajax_profile_child_delete({$user_child->identity}); } return false;" value="Удалить" />
								</fieldset>
							</form>
EOT;
	
			$response->assign("children-list", "innerHTML", $form_text);
			
		}
		
	}
	else 
	{

		$user_child_name = "";
		$user_child_frend_name = "";
		
		$form_text = <<<EOT
<div class="header">Добавление информации о ребенке {$user_child_name}:</div>
<div class="clear-both"></div>
<a href="#" onclick="xajax_profile_show_child_list();return false;">Вернуться в список</a>
<div class="clear-both"></div>

							<form method="post" id="profile-child-edit-form" onsubmit="xajax_profile_child_edit_form_submit(xajax.getFormValues('profile-child-edit-form')); return false;" action="#">
								<input name="do" value="add" type="hidden" />
								<fieldset>
									<label class="float-left">Имя ребенка:</label>
									<input name="user_profile_user_child_name" id="user_profile_user_child_name" class="float-left text" type="text" value="{$user_child_name}" />
								</fieldset>
								<fieldset>
									<label class="float-left">Пол:</label>
									<select name="user_profile_user_child_sex" id="user_profile_user_child_sex" class="combobox">
EOT;

									
		$form_text .= <<<EOT
										<option value="0">Не выбрано</option>
EOT;
		foreach ($user_child_sexs as $key=>$value)
		{
			$selected = "";
			$option_value = $MAIN->GetCurrentArrayLang($value);
			$form_text .= <<<EOT
										<option value="{$key}" {$selected}>{$option_value}</option>
EOT;
		}
		$form_text .= <<<EOT
									</select>
								</fieldset>
								
								<fieldset>
									<label class="float-left">Имя друга ребенка:</label>
									<input name="user_profile_user_child_frend_name" id="user_profile_user_child_frend_name" class="float-left text" type="text" value="{$user_child_frend_name}" />
								</fieldset>
								<fieldset>
									<label class="float-left">Пол друга ребенка:</label>
									<select name="user_profile_user_child_frend_sex" id="user_profile_user_child_frend_sex" class="combobox">
EOT;

									
		$form_text .= <<<EOT
										<option value="0">Не выбрано</option>
EOT;
		foreach ($user_child_sexs as $key=>$value)
		{
			$selected = "";
			$option_value = $MAIN->GetCurrentArrayLang($value);
			$form_text .= <<<EOT
										<option value="{$key}" {$selected}>{$option_value}</option>
EOT;
		}
		$form_text .= <<<EOT
									</select>
								</fieldset>
								
								<fieldset>
									<input type="button" class="cancel float-left" onclick="$('#profile-child-edit-form').each (function(){ this.reset(); }); return false;" value="Отменить" />
									<input type="button" class="save float-left" onclick="$('#profile-child-edit-form').submit(); return false;" value="Сохранить" />
								</fieldset>
							</form>
EOT;
	
		$response->assign("children-list", "innerHTML", $form_text);
			
		
	}
	
	
	//$response->alert($child_id);
	return $response;
}

function profile_child_edit_form_submit($formData)
{
	global $MAIN, $user, $user_child_sexs;
	
	$response = new xajaxResponse();
	//$response->alert("Сохранение изменений!");
	
	
	$changes = array();
	$errors = array();
	$error_ids = array();
	
	$ids = array(
		"user_profile_user_child_name",
		"user_profile_user_child_frend_name",
		"user_profile_user_child_sex",
		"user_profile_user_child_frend_sex",
	);
	
 	// проверки
  	
 	// обязательные поля
 	if(!trim($formData["user_profile_user_child_name"])
 	|| strip_tags($formData["user_profile_user_child_name"]) != $formData["user_profile_user_child_name"]
	|| stripslashes($formData["user_profile_user_child_name"]) != $formData["user_profile_user_child_name"]) 
 	{
 		$error_ids[] = "user_profile_user_child_name";
 	} 
 	if(!trim($formData["user_profile_user_child_frend_name"])
 	|| strip_tags($formData["user_profile_user_child_frend_name"]) != $formData["user_profile_user_child_frend_name"]
	|| stripslashes($formData["user_profile_user_child_frend_name"]) != $formData["user_profile_user_child_frend_name"]) 
 	{
 		$error_ids[] = "user_profile_user_child_frend_name";
 	}
	
 	if(!trim($formData["user_profile_user_child_sex"])
 	|| !$formData["user_profile_user_child_sex"]) 
 	{
 		$error_ids[] = "user_profile_user_child_sex";
 	} 
 	if(!trim($formData["user_profile_user_child_frend_sex"])
 	|| !$formData["user_profile_user_child_frend_sex"]) 
 	{
 		$error_ids[] = "user_profile_user_child_frend_sex";
 	} 
 	 	
 	if(count($error_ids))
 	{
 		$errors[] = "Следует заполнить обязательные поля!";
 	}
 	
 	if(isset($formData["child_id"]) && intval($formData["child_id"]))
 	{
	 	$user_child = new CEntity(
	 		array(
	 			"table" => "user_child",
	 			"id" => intval($formData["child_id"]),
	 			"where" => "user_id = '{$user->identity}' AND user_child_isshow = '1'"
	 		)
	 	);
 		
 	}
 	elseif(isset($formData["do"]) && $formData["do"] == "add" )
 	{
	 	$user_child = new CEntity(
	 		array(
	 			"table" => "user_child",
	 		)
	 	);
 		$changes["user_id"] = $user->identity;
 		$changes["user_child_isshow"] = "1";
 	}
 	
 	// проверка изменений
 	if($user_child->GetHeader("user_child_name") != $formData["user_profile_user_child_name"])
 	{
		$changes["user_child_name"] = $formData["user_profile_user_child_name"];
 	} 	
 	if($user_child->GetHeader("user_child_frend_name") != $formData["user_profile_user_child_frend_name"])
 	{
		$changes["user_child_frend_name"] = $formData["user_profile_user_child_frend_name"];
 	}
 	if($user_child->GetHeader("user_child_sex") != $formData["user_profile_user_child_sex"])
 	{
		$changes["user_child_sex"] = $formData["user_profile_user_child_sex"];
 	}
 	if($user_child->GetHeader("user_child_frend_sex") != $formData["user_profile_user_child_frend_sex"])
 	{
		$changes["user_child_frend_sex"] = $formData["user_profile_user_child_frend_sex"];
 	}
 	
	foreach ($ids as $id)
	{
		$response->script("$('#profile-child-edit-form #{$id}').css('border','#CCCCCC 1px solid')");
	} 	
 	
 	
 	// есть ошибки
 	if(count($error_ids))
 	{
		foreach ($error_ids as $error_id)
		{
			$response->script("$('#profile-child-edit-form #{$error_id}').css('border','#AA0000 1px solid')");
		}
		
		$response->alert(implode("
", $errors));
		
		return $response;
 	}
 	
 	
 	if(isset($formData["child_id"]) && intval($formData["child_id"]))
	{
	 	// нет ошибок - изменяем профайл
		$user_child_entity_list = new CEntityList(
			array(
				"table" => "user_child",
				"table_parent" => "user",
				"key_parent" => "user_id",
				"parent_id" => $user->identity,
				"where" => "user_child_isshow = '1' AND user_child_id='".intval($formData["child_id"])."'",
				"limit" => "LIMIT 0,1",
			)
		);
			
		if($user_child_entity_list->GetCount() > 0)
		{
			$user_child = $user_child_entity_list->list[0];
		 	if($user_child->identity && count($changes))
		 	{
				$user_child->Save($changes);
				if($user_child->GetHeader("user_child_isselected") == "1")
				{
					profile_show_selected_child_name_ex($response);
					//$response->assign("header-auth-profile-child-name","innerHTML", $user_child->GetHeader("user_child_name"));
					
				}
				
				$response->alert("Изменения сохранёны!");
		 	}
			
		}
	 	
	 	
 	}
 	elseif(isset($formData["do"]) && $formData["do"] == "add" )
 	{
	 	$user_child = new CEntity(
	 		array(
	 			"table" => "user_child",
	 		)
	 	);
 		$changes["user_id"] = $user->identity;
 		$changes["user_child_isshow"] = "1";
 		$changes["user_child_isselected"] = "1";
 		$changes["tale_id"] = "0";
 		
		// обнулим признак user_child_isselected, выбранных детей пользователя
		$db = new CDatabase();
		$query = "UPDATE user_child SET user_child_isselected = '0' WHERE user_id='{$user->identity}' AND user_child_isshow = '1' AND user_child_isselected = '1'";
		$db->Query($query);
 		
 		$user_child->Save($changes);
 		
 		if($user_child->identity)
 		{
			$response->alert("Информация добавлена!");
			//$response->assign("header-auth-profile-child-name","innerHTML", $user_child->GetHeader("user_child_name"));
			profile_show_selected_child_name_ex($response);
			$response->script("xajax_profile_show_child_edit({$user_child->identity});");
 		}
 	}
 	
 	
	
	//$response->alert(print_r($formData, true));
 	
	return $response;
}


/**
 * Отображение списка детей на странице профайла / закладка дети
 * @return xajaxResponse
 */
function profile_show_child_list()
{
	global $MAIN, $user, $user_child;
	
	$response = new xajaxResponse();

	
	if(!isset($user) || !is_a($user, "CEntity") || !$user->identity)
	{
		return $response;
	}
	
	$text = '
								<div class="header">Список детей</div>
								<ul class="children-list-ul">
	';
	
//	if(isset($user_child) && is_a($user_child, "CEntity") && $user_child->identity)
//	{
//		
//	}
	$user_child_list = new CEntityList(
		array(
			"table" => "user_child",
			"table_parent" => "user",
			"key_parent" => "user_id",
			"parent_id" => $user->identity,
			"where" => "user_child_isshow = '1'",
			"order_key" => "user_child_name",
			"template" => '
									<li class="[class]">[header:user_child_name][select]<a class="edit" onclick="xajax_profile_show_child_edit([identity]); return false;">Редактировать</a></li>
			',
			"function" => "show_profile_user_child_list_function"
		)
	);
	
	$text .= $user_child_list->ViewList();
	
	$text .= '
									<li>&nbsp;</li>
									<li>&nbsp;<a class="edit" onclick="xajax_profile_show_child_edit(0); return false;">Добавить</a></li>
								</ul>
	';
	
	$response->assign("children-list", "innerHTML", $text);
	
	return $response;
	
}


function show_profile_user_child_list_function($list, &$view)
{
	global $user;
	
  $user_child = false;
	$user_child_entity_list = new CEntityList(
		array(
			"table" => "user_child",
			"table_parent" => "user",
			"key_parent" => "user_id",
			"parent_id" => $user->identity,
			"where" => "user_child_isshow = '1' AND user_child_isselected = '1'",
			"limit" => "LIMIT 0,1",
		)
	);
		
	if($user_child_entity_list->GetCount() > 0)
	{
		$user_child = $user_child_entity_list->list[0];
	}
	
	
	if($user_child && is_a($user_child, "CEntity") && $user_child->identity
	&& $user_child->identity == $list->item->identity
	)
	{
		$view = preg_replace("/".preg_quote("[select]")."/ims", '<a>Выбран</a>', $view);
		$view = preg_replace("/".preg_quote("[class]")."/ims", 'selected', $view);
	}
	else
	{
		$view = preg_replace("/".preg_quote("[select]")."/ims", '<a class="select" onclick="xajax_profile_select_child('.$list->item->identity.'); return false;">Выбрать</a>', $view);
		$view = preg_replace("/".preg_quote("[class]")."/ims", '', $view);
	}
}


/**
 * Выбор текущего ребенка на странице профайла / закладка дети
 * @param integer $child_id
 * @return xajaxResponse
 */
function profile_select_child($child_id)
{
	global $MAIN, $user;
	
	$response = new xajaxResponse();
	//$response->alert($child_id);

	$user_child_entity_list = new CEntityList(
		array(
			"table" => "user_child",
			"table_parent" => "user",
			"key_parent" => "user_id",
			"parent_id" => $user->identity,
			"where" => "user_child_isshow = '1' AND user_child_id='".intval($child_id)."'",
			"limit" => "LIMIT 0,1",
		)
	);
		
	if($user_child_entity_list->GetCount() > 0)
	{
		// обнулим признак user_child_isselected, выбранных детей пользователя
		$db = new CDatabase();
		$query = "UPDATE user_child SET user_child_isselected = '0' WHERE user_id='{$user->identity}' AND user_child_isshow = '1' AND user_child_isselected = '1'";
		$db->Query($query);
		
		// выберем ребенка
		$user_child = $user_child_entity_list->list[0];
		$user_child->Save(array("user_child_isselected"=>"1"));
		
		//$response->assign("header-auth-profile-child-name","innerHTML", $user_child->GetHeader("user_child_name"));
		profile_show_selected_child_name_ex($response);
		$response->script("xajax_profile_show_child_list();");
		
	}
	
	
	return $response;
	
}


/**
 * Функция обработки XAJAX запроса
 * может быть вызвана в JavaScript как "profile_edit_form_submit(some_pram)"
 * @param unknown_type $some_param
 * @return xajaxResponse
 */
function profile_edit_form_submit($formData)
{
	global $MAIN, $user;
	
	$response = new xajaxResponse();
	
	$changes = array();
	$errors = array();
	$error_ids = array();
	
	$ids = array(
		"user_profile_user_name",
		"user_profile_user_surname",
		"user_profile_user_sex",
		"user_profile_user_email",
	);
	
 	// проверки
  	
 	// обязательные поля
 	if(!trim($formData["user_profile_user_name"])
 	|| strip_tags($formData["user_profile_user_name"]) != $formData["user_profile_user_name"]
	|| stripslashes($formData["user_profile_user_name"]) != $formData["user_profile_user_name"]) 
 	{
 		$error_ids[] = "user_profile_user_name";
 	} 
 	if(!trim($formData["user_profile_user_surname"])
 	|| strip_tags($formData["user_profile_user_surname"]) != $formData["user_profile_user_surname"]
	|| stripslashes($formData["user_profile_user_surname"]) != $formData["user_profile_user_surname"]) 
 	{
 		$error_ids[] = "user_profile_user_surname";
 	}
  	
 	if(!trim($formData["user_profile_user_email"])
 	|| strip_tags($formData["user_profile_user_email"]) != $formData["user_profile_user_email"]
	|| stripslashes($formData["user_profile_user_email"]) != $formData["user_profile_user_email"]) 
 	{
 		$error_ids[] = "user_profile_user_email";
 	}
  	
// 	if(!$formData["user_profile_city"]
//	|| $formData["user_profile_city"] == "0"
//	|| !intval($formData["user_profile_city"]))
// 	{
// 		$error_ids[] = "user_profile_city";
// 	}
  	
 	if(count($error_ids))
 	{
 		$errors[] = "Следует заполнить обязательные поля!";
 	}

 	// проверка изменений
 	if($user->GetHeader("user_name") != $formData["user_profile_user_name"])
 	{
		$changes["user_name"] = $formData["user_profile_user_name"];
 	} 	
 	if($user->GetHeader("user_surname") != $formData["user_profile_user_surname"])
 	{
		$changes["user_surname"] = $formData["user_profile_user_surname"];
 	}
 	if($user->GetHeader("user_sex") != $formData["user_profile_user_sex"])
 	{
		$changes["user_sex"] = $formData["user_profile_user_sex"];
 	}
// 	if($user->GetHeader("city_id") != $formData["user_profile_city"])
// 	{
//		$changes["city_id"] = $formData["user_profile_city"];
// 	}
// 	if($user->GetHeader("city_metro_id") != $formData["user_profile_city_metro"])
// 	{
//		$changes["city_metro_id"] = $formData["user_profile_city_metro"];
// 	}
 	
 	
 	if(!check_email($formData["user_profile_user_email"]))
 	{
 		$error_ids[] = "user_profile_user_email";
 		$errors[] = "Неправильный E-mail!";
 	}
 	
 	// user_email
 	if($formData["user_profile_user_email"] != $user->GetHeader("user_email")
 	&& check_email($formData["user_profile_user_email"]))
 	{
 		// проверка на уникальность
 		$user_entity_list = new CEntityList(
 			array(
 				"table" => "user",
 				"where" => "user_email = '{$formData["user_profile_user_email"]}'",
 			)
 		);
 		if($user_entity_list->GetCount())
 		{
			$error_ids[] = "user_profile_user_email";
			$erros[] = "Пользователь с таким E-mail уже зарегистрирован!";
			//s_add_error(VN_PAGE."?id=user&a=profile_edit", "Пользователь с таким E-mail уже зарегистрирован!");
		}
		else 
		{
			 $changes["user_email"] = $formData["user_profile_user_email"];
		}
 	}
	
// 	// проверка даты
// 	if($formData["user_profile_d_d"] != "00" 
// 	|| $formData["user_profile_d_m"] != "00"
// 	|| $formData["user_profile_d_y"] != "0000")
// 	{
// 		if(!checkdate($formData["user_profile_d_m"], $formData["user_profile_d_d"], $formData["user_profile_d_y"]))
// 		{
//			$error_ids[] = "user_profile_d_d";
//			$error_ids[] = "user_profile_d_m";
//			$error_ids[] = "user_profile_d_y";
//			$errors[] = "Некорректная дата рождения!";
// 		}
// 		else 
// 		{
// 			$changes["user_date_burn"] = "{$formData["user_profile_d_y"]}-{$formData["user_profile_d_m"]}-{$formData["user_profile_d_d"]} 00:00:00";
// 		}
// 	}
 	
	foreach ($ids as $id)
	{
		$response->script("$('#profile-edit-form #{$id}').css('border','#CCCCCC 1px solid')");
	} 	
 	
 	// есть ошибки
 	if(count($error_ids))
 	{
		foreach ($error_ids as $error_id)
		{
			$response->script("$('#profile-edit-form #{$error_id}').css('border','#AA0000 1px solid')");
		}
		
		$response->alert(implode("
", $errors));
		
		return $response;
 	}
 	
 	
 	// нет ошибок - изменяем профайл
	//$response->alert(print_r($formData, true));
 	if($user->identity && count($changes))
 	{
		$user->Save($changes);
		$response->script("$('#header-auth-profile-name').text('".$user->GetHeader("user_name")." ".$user->GetHeader("user_surname")."')");
		
		$response->alert("Профайл сохранён!");
 	}

	return $response;
}



?>