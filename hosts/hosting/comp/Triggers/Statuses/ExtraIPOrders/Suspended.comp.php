<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ExtraIPOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ExtraIPOrder['UserID'],'TypeID'=>'ExtraIPSuspend','Params'=>Array($ExtraIPOrder['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($IsAdd)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$ExtraIPOrder['OrderID'],'Parked'=>$ExtraIPOrder['Login']));
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Comp;
case 'array':
	return TRUE;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
