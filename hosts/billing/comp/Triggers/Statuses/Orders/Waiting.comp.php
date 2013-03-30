<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ServiceOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Debug(SPrintF('[comp/Triggers/Statuses/ServiceOrders/Waiting]: ServiceOrder = %s',print_r($ServiceOrder,true)));
#-------------------------------------------------------------------------------
$UserID = $GLOBALS['__USER']['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Code','Name','NameShort'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
switch(ValueOf($Service)){
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
$Params = Array(
		'SchemeID'	=> 0,
		'OrderID'	=> $ServiceOrder['ID'],
		'MaxOrders'	=> 0, /* может из полей доставать? */
		'ServiceID'	=> $Service['ID'],
		'ServiceName'	=> $Service['Name'],
		'SchemeName'	=> $Service['NameShort']
		);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Orders/OrdersHistory',$Params);
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
