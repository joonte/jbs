<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('DSOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$DSOrder['UserID'],'TypeID'=>'DSSuspend','Params'=>Array($DSOrder['ID'])));
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
$Parked = Array($DSOrder['IP']);
#-------------------------------------------------------------------------------
$Array = ($DSOrder['ExtraIP'])?Explode("\n",$DSOrder['ExtraIP']):Array();
#-------------------------------------------------------------------------------
foreach($Array as $IP)
	if(!In_Array(Trim($IP),$Parked))
		$Parked[] = Trim($IP);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$DSOrder['OrderID'],'Parked'=>Implode(',',$Parked)));
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
