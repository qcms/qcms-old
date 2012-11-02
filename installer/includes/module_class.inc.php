<?
//-----------------------------------------------------------------------------
// класс модуля страницы
//-----------------------------------------------------------------------------
class ModuleClass
{
  //===========================================================================
  // данные члены
  var $ln = null;
  var $page_id = null;
  var $number = null;

  var $entity  = null;
  var $id  = null;
  var $is_show = false;
  var $link = null;
  var $name = null;
  var $text = null;

  //===========================================================================
  // функции члены
  //---------------------------------------------------------------------------
  // конструктор
  function ModuleClass($ln, $page_id, $number)
  {
    if($ln != null)
    {
      $this->ln = $ln;
      $this->entity = new EntityClass(
        "module",
        'none',
        "d.m.Y",
        array(),
        array("module_datetime"),
        array("module_ln", "module_link", "module_name", "module_isshow", "module_order"),
        array("module_text"),
        $this->ln,
        "ln",
        array("module_ln", "module_link", "module_isshow", "module_order"),
        array("module_text")
      );
      if($this->entity)
      {
        $this->is_show = ($this->entity->headers["module_isshow"] == "1");
        $this->link = $this->entity->headers["module_link"];
        $this->name = $this->entity->headers["module_name"];
        $this->text = $this->entity->texts["module_text"];
      }

    }
    else
    {
      $this->page_id = $page_id;
      // запрос на модули для данной страницы
      $query_modules = "
SELECT page_module_id
WHERE page_id = " . $this->page_id . "
ORDER BY page_module_order
LIMIT " . ($this->number-1) . ", 1" ;
      $result = mysql_result($query_modules) or die(mysql_errno() . ": " . mysql_error(). "\n");
      if($result && $row = mysql_fetch_assoc($result))
      {
        $this->entity = new EntityClass(
          "page_module",
          'none',
          "d.m.Y",
          array(),
          array("page_module_datetime"),
          array("page_id", "page_module_name", "page_module_isshow", "page_module_order"),
          array("module_text"),
          $row["page_module_id"],
          "id",
          array("page_module_isshow", "page_module_order", "page_id"),
          array("page_module_text")
        );
        if($this->entity)
        {
          $this->is_show = ($this->entity->headers["page_module_isshow"] == "1");
          $this->name = $this->entity->headers["page_module_name"];
          $this->text = $this->entity->texts["page_module_text"];
        }
      }
    }

  }

}
?>
