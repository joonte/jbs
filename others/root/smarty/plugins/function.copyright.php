<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.copyright.php
 * Type:     function
 * Name:     copyright
 * Purpose:  gets the weather forecast
 * -------------------------------------------------------------
 */
function smarty_function_copyright($params, $smarty) {
    $Copyright = DB_Select('Config','Value',Array('UNIQ','Where'=>"`Param` = 'Copyright'"));
    if(!Is_Array($Copyright))
      return ERROR | @Trigger_Error(500);

    return $Copyright['Value'];
}
?>