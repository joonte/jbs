<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DSServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','OrderID','UserID','SchemeID','ServerID','IP'),Array('UNIQ','ID'=>$DSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
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
$ClassDSServer = new DSServer();
#-------------------------------------------------------------------------------
$IsSelected = $ClassDSServer->Select((integer)$DSOrder['ServerID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSelected)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DSScheme = DB_Select('DSSchemes','*',Array('UNIQ','ID'=>$DSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($DSScheme)){
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
$IsActive = Call_User_Func_Array(Array($ClassDSServer,'Active'),Array($DSScheme));
#-------------------------------------------------------------------------------
switch(ValueOf($IsActive)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsActive;
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $DSOrder['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF('Подключен к сети арендованный сервер, заказ #%s, тариф (%s), IP адрес %s',$DSOrder['OrderID'],$DSScheme['Name'],$DSOrder['IP'])
		);
#-------------------------------------------------------------------------------
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($ClassDSServer->Settings['Address'])=>Array($DSScheme['Name']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
