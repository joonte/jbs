<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Columns = Array(
			'ID', 'UserID',
			'(SELECT `Item` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Item`',
			'(SELECT `Name` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Name`',
			'ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) AS `DaysRemainded`'
		);
#-------------------------------------------------------------------------------
$Where = "(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) = 'Default' AND (SELECT `ConsiderTypeID` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) != 'Upon' AND `StatusID` = 'Active' AND ROUND((`ExpirationDate` - UNIX_TIMESTAMP())/86400) IN (1,3,5,7)";
#-------------------------------------------------------------------------------
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#---------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = SPrintF('OrdersNoticeSuspend, Handled %u orders',SizeOf($Orders));
	#---------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		$IsSend = NotificationManager::sendMsg(new Message('OrdersSuspend',(integer)$Order['UserID'],Array('Order'=>$Order)));
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
	} # end foreach
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
