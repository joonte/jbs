<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.top_panel.php
 * Type:     function
 * Name:     top_panel
 * Purpose:  gets the weather forecast
 * -------------------------------------------------------------
 */
function smarty_function_top_panel($params, $smarty) {
    if(IsSet($GLOBALS['__USER'])) {
        $__USER = $GLOBALS['__USER'];

        if(Is_Null($__USER))
            return ERROR | @Trigger_Error(400);

        $menuPath = SPrintF('%s/TopPanel',$__USER['InterfaceID']);

        $items = Styles_Menu($menuPath);
        if(Is_Error($items))
            return ERROR | @Trigger_Error(500);

        $items = &$items['Items'];
        //return print_r(array_values($items));
        $smarty->assign('items', array_values($items));

        $__USER = $GLOBALS['__USER'];
        $smarty->assign('userName', $__USER['Name']);

        $smarty->display('TopPanel/User.tpl');
    }
    else {
        $smarty->display('TopPanel/Logon.tpl');
    }
}
?>