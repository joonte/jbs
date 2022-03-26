<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('HostingOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$HostingOrderID = $HostingOrder['ID'];
#-------------------------------------------------------------------------------
$Order = DB_Select('HostingOrdersOwners','OrderID',Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
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
$Comp = Comp_Load('www/API/OrderRestore',Array('OrderID'=>$Order['OrderID'],'IsNoDiscont'=>TRUE));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#$ExecuteDate = Comp_Load('HostingOrders/SearchExecuteTime');
#if(Is_Error($ExecuteDate))
#	return ERROR | @Trigger_Error(500);
$ExecuteDate = Time();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingDelete','ExecuteDate'=>$ExecuteDate,'Params'=>Array($HostingOrderID)));
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
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$HostingOrder['OrderID'],'Parked'=>Explode(',',$HostingOrder['Parked'])));
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
