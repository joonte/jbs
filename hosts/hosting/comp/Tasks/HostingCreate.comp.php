<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','HostingOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',Array('ID','UserID','Login','Domain','SchemeID','Password','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`) AS `ServerID`','(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `HostingOrdersOwners`.`ContractID`) as `ProfileID`'),Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
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
$ClassHostingServer = new HostingServer();
#-------------------------------------------------------------------------------
$IsSelected = $ClassHostingServer->Select((integer)$HostingOrder['ServerID']);
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
$HostingScheme = DB_Select('HostingSchemes','*',Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingScheme)){
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
$IPsPool = Explode("\n",$ClassHostingServer->Settings['Params']['IPsPool']);
#-------------------------------------------------------------------------------
$IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
#-------------------------------------------------------------------------------
$Args = Array($HostingOrder['Login'],$HostingOrder['Password'],$HostingOrder['Domain'],$IP,$HostingScheme);
#-------------------------------------------------------------------------------
$User = DB_Select('Users','*',Array('UNIQ','ID'=>$HostingOrder['UserID']));
#-------------------------------------------------------------------------------
switch(ValueOf($User)) {
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$Args[] = $User['Email'];
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ProfileID = (integer)$HostingOrder['ProfileID'];
#-------------------------------------------------------------------------------
if($ProfileID){
	#-------------------------------------------------------------------------------
	$Profile = DB_Select('Profiles',Array('TemplateID','Attribs'),Array('UNIQ','ID'=>$ProfileID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Profile)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		# No more...
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$Args[] = $Profile['TemplateID'];
		#-------------------------------------------------------------------------------
		$Args[] = $Profile['Attribs'];
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsCreate = Call_User_Func_Array(Array($ClassHostingServer,'Create'),$Args);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCreate)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsCreate;
case 'true':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'Active','RowsIDs'=>$HostingOrder['ID'],'Comment'=>'Заказ создан на сервере'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $HostingOrder['UserID'],
				'PriorityID'	=> 'Hosting',
				'Text'		=> SPrintF('Заказ хостинга логин [%s], домен (%s) создан на сервере (%s) с тарифным планом (%s), идентификатор пакета (%s)',$HostingOrder['Login'],$HostingOrder['Domain'],$ClassHostingServer->Settings['Address'],$HostingScheme['Name'],$HostingScheme['PackageID'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array(($ClassHostingServer->Settings['Address'])=>Array($HostingOrder['Login'],$HostingScheme['Name']));
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
