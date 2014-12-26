<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$DNSmanagerOrders = DB_Select('DNSmanagerOrdersOwners','*',Array('Where'=>"`DaysRemainded` IN (1,5,10,15) AND `StatusID` = 'Active'"));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return MkTime(4,20,0,Date('n'),Date('j')+1,Date('Y'));
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = SPrintF('Notified %u accounts',SizeOf($DNSmanagerOrders));
#-------------------------------------------------------------------------------
foreach($DNSmanagerOrders as $DNSmanagerOrder){
	#-------------------------------------------------------------------------------
	$IsSend = NotificationManager::sendMsg(new Message('DNSmanagerNoticeSuspend',(integer)$DNSmanagerOrder['UserID'],Array('DNSmanagerOrder'=>$DNSmanagerOrder)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
	case 'true':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return MkTime(4,20,0,Date('n'),Date('j')+1,Date('Y'));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
