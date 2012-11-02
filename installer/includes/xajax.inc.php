<?
//-----------------------------------------------------------------------------
// Модуль подключения xajax
//-----------------------------------------------------------------------------

  global $MAIN,$xajax;
  include ($MAIN->root.'xajax_core/xajax.inc.php');

  // подключение xajax
  $xajax = new xajax();
  //$xajax->configure('debug', true);
  $xajax->configure( 'defaultMode', 'synchronous' ); 
  
?>