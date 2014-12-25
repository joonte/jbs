<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DNSmanagerOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/DNSmanagerServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','UserID','Login',
		'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`',
		'(SELECT `IsReselling` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`) as `IsReselling`',
		'(SELECT `Name` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`) as `SchemeName`'
		);
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$ClassDNSmanagerServer = new DNSmanagerServer();
	#-------------------------------------------------------------------------------
	$IsSelected = $ClassDNSmanagerServer->Select((integer)$DNSmanagerOrder['ServerID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'true':
		#-------------------------------------------------------------------------------
		$IsDelete = $ClassDNSmanagerServer->Delete($DNSmanagerOrder['Login'],$DNSmanagerOrder['IsReselling']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsDelete)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return $IsDelete;
		case 'true':
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $DNSmanagerOrder['UserID'],
					'PriorityID'	=> 'DNSmanager',
					'Text'		=> SPrintF('Заказ вторичного DNS логин [%s], тариф (%s) удален с сервера (%s)',$DNSmanagerOrder['Login'],$DNSmanagerOrder['SchemeName'],$ClassDNSmanagerServer->Settings['Address'])
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array($ClassDNSmanagerServer->Settings['Address'],$DNSmanagerOrder['Login'],$DNSmanagerOrder['SchemeName']);
			#-------------------------------------------------------------------------------
			return TRUE;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
