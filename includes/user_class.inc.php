<?php
//-----------------------------------------------------------------------------
// класс польователей и доступа к сущьностям
//-----------------------------------------------------------------------------

/**
 * Класс группы администраторов
 * @author Infoways
 */
class CAdminUserGroup
{
	function CAdminUserGroup($params = array())
	{
		
	}
	
	function CheckAccess($params=array())
	{
		
	}
}


/**
 * Класс администратора 
 * @author Infoways
 */
class CAdminUser
{
	var $group;
	var $entity;
	var $login;
    var $password;
	//var $session_id;
	var $messages;
	var $errors;
    var $hash;
	
	
	/**
	 * Конструктор 
	 * @param unknown_type $params
	 */
	function CAdminUser($params = array("login"=>"", "password"=>"", "hash"=>"", ))
	{
		if(!isset($params["login"]))
			$params["login"] = "";
		if(!isset($params["password"]))
			$params["password"] = "";
        if(!isset($params["hash"]))
            $params["hash"] = "";

		$this->entity = false;
		$this->group = false;
		$this->login = $params["login"];
		$this->password = $params["password"];
        $this->hash = $params["hash"];
		$this->messages = array();
		$this->errors = array();
		
		if(!$this->IsSuperadmin()
            && $this->login
            && $this->password
        )
		{
			$entity = new CEntity(
				array(
					"table"=>"adminuser",
					"index_suffix"=>"ln",
					"id"=>$this->login,
					"where" => "adminuser_isshow='1' AND adminuser_password='{$this->password}'",
				)
			);
			if($entity->identity)
            {
                $this->entity = $entity;
                $this->hash = $this->GetHash($this->login, $this->password);
            }
			else
            {
                unset($entity);
            }
		}
        elseif(!$this->IsSuperadmin()
            && $this->hash
        )
        {
            $entityList = new CEntityList(
                array(
                    "table"=>"adminuser",
                    "where" => "adminuser_isshow='1' AND adminuser_hash='{$this->hash}'",
                )
            );
            if($entityList->GetCount())
            {
                $this->entity = $entityList->list[0];
                $this->login = $this->entity->GetHeader("adminuser_ln");
                $this->password = $this->entity->GetHeader("adminuser_password");
            }
            else
            {
                unset($entityList);

            }

        }
        elseif($this->IsSuperadmin())
        {
            if($this->hash)
            {
                $this->login = ADMIN_USER;
                $this->password = ADMIN_PASSWORD;
            }
            else
            {
                $this->hash = $this->GetHash($this->login, $this->password);
            }
        }
		//error_log(print_r($this, true));
	}
	
	/**
	 * @param CEntity $adminuser_access
	 * @param CEntity $entity
	 * @param string $access "r"|"w"|"a"|"d"
	 * @return boolean
	 */
	function GetEntityAccess($adminuser_access, $entity, $access)
	{
		$ret = false;
		
		// Проверим правила из "adminuser_access"
		if($this->AccessToBool($access, "r"))
		{
			$ret = $adminuser_access->GetHeader("adminuser_access_isread")=="1";
		}
		elseif($this->AccessToBool($access, "w"))
		{
			$ret = $adminuser_access->GetHeader("adminuser_access_iswrite")=="1";
		}
		elseif($this->AccessToBool($access, "a"))
		{
			$ret = $adminuser_access->GetHeader("adminuser_access_isadd")=="1";
		}
		elseif($this->AccessToBool($access, "d"))
		{
			$ret = $adminuser_access->GetHeader("adminuser_access_isdelete")=="1";
		}
		return $ret;
	}
	
	/**
	 * Функция проверки доступа к объекту
	 * @param CEntity $entity
	 * @param string $access "r"|"w"|"a"|"d"
	 * @return boolean если доступ разрешен то возвращает true
	 */
	function CheckEntityAccess($entity, $access="")
	{
		global $MAIN;
		$ret = false;
		if($this->IsSuperadmin())
		{
			// суперадмину можно все
			$ret = true;
		}
		else
		{
			$adminuser_access_arr = $this->GetAdminuserAccess($entity);
			if($adminuser_access_arr && is_array($adminuser_access_arr) && count($adminuser_access_arr))
			{
				$adminuser_access = $adminuser_access_arr[count($adminuser_access_arr)-1];
				
				$ret = $this->GetEntityAccess($adminuser_access, $entity, $access);
				
//				// Проверим правила из "adminuser_access"
//				if($this->AccessToBool($access, "r"))
//				{
//					$ret = $adminuser_access->GetHeader("adminuser_access_isread")=="1";
//				}
//				elseif($this->AccessToBool($access, "w"))
//				{
//					$ret = $adminuser_access->GetHeader("adminuser_access_iswrite")=="1";
//				}
//				elseif($this->AccessToBool($access, "a"))
//				{
//					$ret = $adminuser_access->GetHeader("adminuser_access_isadd")=="1";
//				}
//				elseif($this->AccessToBool($access, "d"))
//				{
//					$ret = $adminuser_access->GetHeader("adminuser_access_isdelete")=="1";
//				}
				
				// условия
				$adminuser_access_condition_arr = $this->GetAdminuserAccessConditions($entity, $adminuser_access);
				//var_dump($adminuser_access_condition_arr);
				
//				// если есть условия то их нужно проверить
//				if(is_array($adminuser_access_condition_arr) && count($adminuser_access_condition_arr))
//				{
//					// список рабочих правил (которые применимы к объекту
//					$adminuser_access_condition_arr_working_rules = array();
//					
//					
//					// есть условия в "adminuser_access_condition"
//					foreach($adminuser_access_condition_arr as $adminuser_access_condition)
//					{
//						$tableParamHierarchy = $MAIN->GetTableParam($entity->table, "hierarchy");
//						$tableFieldParams = $MAIN->GetTableFieldParams($entity->table, $adminuser_access_condition->GetHeader("adminuser_access_condition_field"));
//						
//						if($tableFieldParams["type"] == "select" &&
//						is_array($tableParamHierarchy) && isset($tableParamHierarchy["hierarchy"])
//						&& $tableParamHierarchy["hierarchy"] == "true" 
//						&& $adminuser_access_condition->GetHeader("adminuser_access_condition_ishierarchisch"))
//						{
//							// иерархичный объект
//							$list = $entity->GetHierarchy();
//							if($list->Find($adminuser_access_condition->GetHeader("adminuser_access_condition_value")))
//							{
//								// нашли в иерархии => правило действует
//								$adminuser_access_condition_arr_working_rules[] = $adminuser_access_condition;
//							}
//							
//						}
//						else
//						{
//							// неиерархичный объект или правило без учета иерархии объектов
//							// или простое правило
//							$adminuser_access_condition_arr_working_rules[] = $adminuser_access_condition;
//						}
//					}
//					
//					$adminuser_access_condition_arr = $adminuser_access_condition_arr_working_rules;
//					var_dump($adminuser_access_condition_arr);

				// если есть рабочие правила
					if(count($adminuser_access_condition_arr))
					{
						$ret = $this->CheckEntityAccessConditons($entity, $access, $adminuser_access, $adminuser_access_condition_arr);
					}
//				}

				
			}
		}	
			
		return $ret;
	}
	
	
	/**
	 * Функция проверки условий для заданного объекта и права доступа
	 * @param CEntity $entity
	 * @param string $access "r"|"w"|"a"|"d"
	 * @param Array(CEntity) $arAccessCondiitons
	 * @return boolean если доступ разрешен то возвращает true
	 */
	function CheckEntityAccessConditons($entity, $access, $adminuser_access, $adminuser_access_conditions_arr)
	{
		global $MAIN;
		// если условий нет - то значение из правила для объекта!!!
		$ret = $this->GetEntityAccess($adminuser_access, $entity, $access);
		//var_dump($ret);
		
		//var_dump($adminuser_access_conditions_arr);
		
		$adminuser_access_conditions_work_arr = array(); 
		foreach($adminuser_access_conditions_arr as $adminuser_access_condition)
		{
			// проверим выполняется ли это условие для объекта $entity
			if($adminuser_access->GetHeader("adminuser_access_entity") == $entity->table)
			{
				//var_dump($MAIN->GetTableFieldParam($adminuser_access->GetHeader("adminuser_access_entity"), $adminuser_access_condition->GetHeader("adminuser_access_condition_field"), "type"));
				switch($MAIN->GetTableFieldParam($adminuser_access->GetHeader("adminuser_access_entity"), $adminuser_access_condition->GetHeader("adminuser_access_condition_field"), "type"))
				{
					case "select":
						//$list_entity_parents = $entity->GetHierarchy();
						$list_entity_parents = $entity->GetHierarchy();
						//echo "<pre>"; print_r($list_entity_parents);echo "</pre>";
						
						
						//echo "000_";
						//var_dump(count($list_entity_parents->items));
						//var_dump($list_entity_parents);
						if($adminuser_access_condition->GetHeader("adminuser_access_condition_ishierarchisch")  == "1")
						{
							//echo "A_";
							//echo "AA__";
							//var_dump($list_entity_parents);
							//var_dump($list_entity_parents->Find(intval($adminuser_access_condition->GetHeader("adminuser_access_condition_value"))));
							
							if($list_entity_parents->Find($adminuser_access_condition->GetHeader("adminuser_access_condition_value")))
							{
								//echo "AAA_";
								$adminuser_access_conditions_work_arr = array_merge($adminuser_access_conditions_work_arr, array($adminuser_access_condition));
							}
						}
						else
						{
							//echo "B_";
							if($entity->GetField($adminuser_access_condition->GetHeader("adminuser_access_condition_field")) == $adminuser_access_condition->GetHeader("adminuser_access_condition_value"))
							{
								//echo "BBB_";
								$adminuser_access_conditions_work_arr = array_merge($adminuser_access_conditions_work_arr, array($adminuser_access_condition));
							}
						}
						break;
					default:
						if($entity->GetField($adminuser_access_condition->GetHeader("adminuser_access_condition_field")) == $adminuser_access_condition->GetHeader("adminuser_access_condition_value"))
						{
							$adminuser_access_conditions_work_arr = array_merge($adminuser_access_conditions_work_arr, array($adminuser_access_condition));
						}
						break;
				}
			}
			elseif($adminuser_access->GetHeader("adminuser_access_ishierarchisch") == "1")
			{
				$adminuser_access_conditions_work_arr = array_merge($adminuser_access_conditions_work_arr, array($adminuser_access_condition));
			}
			else 
			{
				continue;
			}
		}
		//var_dump($adminuser_access_conditions_work_arr);echo "<br /><br /><br />";
		
		//var_dump($entity);
//		debug_print_backtrace();
//		echo "<br />";
//		echo "<br />";
//		echo "<br />";
		
		//var_dump($adminuser_access_conditions_work_arr);
		
		if(count($adminuser_access_conditions_work_arr))
		{
			foreach($adminuser_access_conditions_work_arr as $adminuser_access_condition)
			{
				//var_dump($adminuser_access_condition->GetHeader("adminuser_access_condition_field"));
				if($entity->GetField($adminuser_access_condition->GetHeader("adminuser_access_condition_field")) 
				 != $adminuser_access_condition->GetHeader("adminuser_access_condition_value"))
				{
					$ret = false;
				}
				if($this->AccessToBool($access, "r"))
				{
					$ret = $adminuser_access_condition->GetHeader("adminuser_access_condition_isread")=="1";
				}
				elseif($this->AccessToBool($access, "w"))
				{
					$ret = $adminuser_access_condition->GetHeader("adminuser_access_condition_iswrite")=="1";
				}
				elseif($this->AccessToBool($access, "a"))
				{
					$ret = $adminuser_access_condition->GetHeader("adminuser_access_condition_isadd")=="1";
				}
				elseif($this->AccessToBool($access, "d"))
				{
					$ret = $adminuser_access_condition->GetHeader("adminuser_access_condition_isdelete")=="1";
				}
				if(!$ret) // найден запрет - ЗАПРЕЩЕНО!!!
					break;
			}
		}
		return $ret;
	}
	
	
	/**
	 * Функция возвращает массив доступа к объекту (таблице), последнее правило - решающее
	 * @param CEntity $entity
	 * @return array(CEntity(adminuser_access))
	 */
	function GetAdminuserAccess($entity)
	{
		$ret = array();
		if($this->entity && is_a($this->entity, "CEntity") && $this->entity->identity
		&& is_a($entity,"CEntity") && $entity->table)
		{
			$list = new CEntityList(
				array(
					"table"=>"adminuser_access",
					"where"=>"adminuser_id='{$this->entity->identity}' AND adminuser_access_isshow='1' AND adminuser_access_entity='{$entity->table}'",
					"order_key" => "adminuser_access_order",
					"order_key_sort" => "ASC",
					//"order_key_sort" => "DESC",
					//"limit" => "0,1",
				)
			);
			
			if($list->GetCount())
			{
				$ret = array_merge($ret, $list->list);
			}
		}
		
		return $ret;
	}
	
	
	/**
	 * Функция возвращает массив правил доступа к объекту (записи таблицы), 
	 * последнее правило - решающее
	 * @param CEntity $entity
	 * @return array(CEntity(adminuser_access_condition))
	 */
	function GetAdminuserAccessConditions($entity, $adminuser_access)
	{
		global $MAIN;
		
		$ret = array();
		
		if($this->entity && is_a($this->entity, "CEntity") && $this->entity->identity
		&& is_a($entity,"CEntity") && $entity->table
		&& is_a($adminuser_access,"CEntity") && $adminuser_access->identity)
		{

			$list = new CEntityList(
				array(
					"table"=>"adminuser_access_condition",
					"where"=>"adminuser_access_id='{$adminuser_access->identity}' AND adminuser_access_condition_isshow='1'",
					"order_key" => "adminuser_access_condition_order",
					"order_key_sort" => "ASC",
					//"order_key_sort" => "DESC",
					//"limit" => "0,1",
				)
			);
			
			if($list->GetCount())
			{
				$ret = array_merge($ret, $list->list);
				//var_dump($ret);
			}
		}
		return $ret;
	}
	
	/**
	 * Функция проверки доступа к объекту
	 * @param CEntity $entity
	 * @param string $field
	 * @param string $access
	 */
	function CheckEntityFieldAccess($entity, $field, $access=false)
	{
		
	}
	
	
	
	/**
	 * @param string $access "rwad"
	 * @param string $part = "r"|"w"|"a"|"d"
	 * @return boolean
	 */
	function AccessToBool($access, $part)
	{
		$ret = false;
		
		if(strlen($part) == 1)
		{
			$pos = strpos($part, $access);
			if(!($pos===false))
			{
				$ret = true;
			}
		}
		return $ret;
	}
	
	
	/**
	 * Функция возвращаеть true если пользователь автризован
	 * @return boolean
	 */
	function IsAuth($table=false)
	{
		$ret = false;
        //if($this->session_id == session_id())
        {
            if($this->IsSuperadmin())
                $ret = true;
            elseif($this->IsActive($table))
                $ret = true;
        }
		return $ret;
	}
	
	/**
	 * @return boolean
	 * Возвращает true если пользователь активен
	 */
	function IsActive($table=false)
	{
		global $MAIN;
		$ret = false;
		
		if(isset($this->entity) && is_a($this->entity, "CEntity") && $this->entity->GetHeader("adminuser_isshow") == "1")
		{
			$ret = true;
			if($table && $MAIN->GetTableParam($table, "superadmin")=="true")
			{
				$ret = false; // только для суперадминов
			}
		}
		return $ret;
	}
	
	
	/**
	 * @return boolean
	 * возращает true если пользователь суперадмин
	 */
	function IsSuperadmin()
	{
		$ret = false;
		
		if(isset($this->login)
            && strlen($this->login)
            && isset($this->password)
            && strlen($this->password)
		    && defined("ADMIN_USER")
            && defined("ADMIN_PASSWORD")
		    && $this->login === ADMIN_USER
            && $this->password === ADMIN_PASSWORD
        )
        {
            $ret = true;

        }
        elseif(isset($this->hash)
            && strlen($this->hash)
            && defined("ADMIN_USER")
            && defined("ADMIN_PASSWORD")
            && $this->GetHash(ADMIN_USER, ADMIN_PASSWORD) === $this->hash
        )
        {
            $ret = true;
        }

		
		return $ret;
	}


    function GetHash($login, $password)
    {
        return md5($login.$password.ADMIN_SECRET);
    }

	/**
	 * добавляет сообщение
	 * @param string $message
	 */
	function AddMessage($message)
	{
		$this->messages[] = $message;
	}
	 
	/**
	 * @param string $message
	 */
	function AddError($message)
	{
		$this->errors[] = $message;
	}
	
	/**
	 * @return number
	 */
	function MessagesCount()
	{
		return count($this->messages);
	}
	
	/**
	 * @return number
	 */
	function ErrorsCount()
	{
		return count($this->errors);
	}
	
}

?>