<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.menu_left.php
 * Type:     function
 * Name:     menu_left
 * Purpose:  gets the weather forecast
 * -------------------------------------------------------------
 */
function smarty_function_menu_left($params, $smarty) {
    $MenuPath = $params['MenuPath'];

    if(!$MenuPath) { return FALSE; }

    $XML = Styles_Menu($MenuPath);
    if(Is_Error($XML))
      return ERROR | @Trigger_Error(500);

    $items = $XML['Items'];

    if(!Count($items))
      return FALSE;

    //echo print_r($items);
    $smarty->assign('items', array_values($items));
    $smarty->display('Menus/Left.tpl');
}
?>