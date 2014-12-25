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
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('ID','UserID','Login','SchemeID','Password','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`'),Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
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
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DNSmanagerScheme = DB_Select('DNSmanagerSchemes','*',Array('UNIQ','ID'=>$DNSmanagerOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerScheme)){
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
$Args = Array($DNSmanagerOrder['Login'],$DNSmanagerOrder['Password'],$DNSmanagerScheme);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsCreate = Call_User_Func_Array(Array($ClassDNSmanagerServer,'Create'),$Args);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCreate)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsCreate;
case 'true':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>'Active','RowsIDs'=>$DNSmanagerOrder['ID'],'Comment'=>'Заказ создан на сервере'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $DNSmanagerOrder['UserID'],
				'PriorityID'	=> 'DNSmanager',
				'Text'		=> SPrintF('Заказ вторичного DNS логин [%s] создан на сервере (%s) с тарифным планом (%s), идентификатор пакета (%s)',$DNSmanagerOrder['Login'],$ClassDNSmanagerServer->Settings['Address'],$DNSmanagerScheme['Name'],$DNSmanagerScheme['PackageID'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array($ClassDNSmanagerServer->Settings['Address'],$DNSmanagerOrder['Login'],$DNSmanagerScheme['Name']);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
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
