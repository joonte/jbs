<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ISPswOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug(SPrintF('[comp/Triggers/Statuses/ISPswOrders/Waiting]: ISPswOrder = %s',print_r($ISPswOrder,true)));
#-------------------------------------------------------------------------------
$UserID = $GLOBALS['__USER']['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('ID','Code','Name'),Array('UNIQ','ID'=>51000));
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
$Scheme = DB_Select(SPrintF('%sSchemes',$Service['Code']),Array('*'),Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
switch(ValueOf($Scheme)){
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
		'SchemeID'	=> $ISPswOrder['SchemeID'],
		'OrderID'	=> $ISPswOrder['OrderID'],
		'MaxOrders'	=> $Scheme['MaxOrders'],
		'ServiceID'	=> $Service['ID'],
		'ServiceName'	=> $Service['Name'],
		'SchemeName'	=> $Scheme['Name']
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
