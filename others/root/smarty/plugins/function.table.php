<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.table.php
 * Type:     function
 * Name:     table
 * Purpose:  gets the weather forecast
 * -------------------------------------------------------------
 */
function smarty_function_table($params, $smarty) {
    $tableName = $params['name'];

    if(!$tableName) { return ""; }

    $DOM = new DOM();

    $Links = &Links();
    # Коллекция ссылок
    $Links['DOM'] = &$DOM;

    $comp = Comp_Load('Tables/Super', $tableName);
    if(Is_Error($comp))
      return ERROR | @Trigger_Error(500);

    $DOM->Object = $comp;

    $Out = $DOM->Build();

    if(Is_Error($Out))
      return ERROR | @Trigger_Error(500);

    return $Out;
}
?>