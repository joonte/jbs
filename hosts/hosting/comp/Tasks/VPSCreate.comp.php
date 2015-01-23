<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','VPSOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/VPSServer.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','OrderID','UserID','Login','IP','Domain','SchemeID','Password',
		'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `ServerID`',
		'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `Params`',
		'(SELECT `ProfileID` FROM `Contracts` WHERE `Contracts`.`ID` = `VPSOrdersOwners`.`ContractID`) AS `ProfileID`'
		);
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
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
$VPSServer = new VPSServer();
#-------------------------------------------------------------------------------
$IsSelected = $VPSServer->Select((integer)$VPSOrder['ServerID']);
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
$VPSScheme = DB_Select('VPSSchemes','*',Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSScheme)){
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
if(IsSet($VPSOrder['Params']['DiskTemplate'])){
	#-------------------------------------------------------------------------------
	foreach(Explode("\n",$VPSServer->Settings['Params']['DiskTemplate']) as $Line){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/VPSCreate]: Line = (%s)',print_r($Line,true)));
		#-------------------------------------------------------------------------------
		$Template = Explode('=',Trim($Line));
		#-------------------------------------------------------------------------------
		if($Template[0] == $VPSOrder['Params']['DiskTemplate'])
			$DiskTemplate = $Template[0];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/VPSCreate]: DiskTemplate = (%s)',IsSet($DiskTemplate)?$DiskTemplate:'не задан'));
#-------------------------------------------------------------------------------
if(!IsSet($DiskTemplate) || StrLen($DiskTemplate) < 2){
	#-------------------------------------------------------------------------------
	$DiskTemplates = Explode("\n",$VPSServer->Settings['Params']['DiskTemplate']);
	#-------------------------------------------------------------------------------
	$Template = Explode('=',Trim($DiskTemplates[0]));
	#-------------------------------------------------------------------------------
	$DiskTemplate = $Template[0];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/VPSCreate]: DiskTemplate = (%s)',print_r($DiskTemplate,true)));
#-------------------------------------------------------------------------------
$VPSOrder['DiskTemplate'] = $DiskTemplate;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IPsPool = Explode("\n",$VPSServer->Settings['Params']['IPsPool']);
#-------------------------------------------------------------------------------
$IP = $IPsPool[Rand(0,Count($IPsPool) - 1)];
#-------------------------------------------------------------------------------
$Args = Array($VPSOrder,$IP,$VPSScheme);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsCreate = Call_User_Func_Array(Array($VPSServer,'Create'),$Args);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCreate)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsCreate;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
# вносим адрес в базу
$IsUpdate = DB_Update('VPSOrders',Array('IP'=>$IsCreate['IP']),Array('ID'=>$VPSOrder['ID']));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error('[comp/Tasks/VPSCreate]: не удалось прописать IP адрес для виртуального сервера');
#-------------------------------------------------------------------------------
# вписываем адрес в массив, чтоб не лазить в базу
$VPSOrder['IP'] = $IsCreate['IP'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrder['ID'],'Comment'=>'Заказ создан на сервере'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
$Event = Array(
		'UserID'	=> $VPSOrder['UserID'],
		'PriorityID'	=> 'Hosting',
		'Text'		=> SPrintF('Заказ VPS [%s] создан на сервере (%s) с тарифным планом (%s), идентификатор пакета (%s)',$VPSOrder['IP'],$VPSServer->Settings['Address'],$VPSScheme['Name'],$VPSScheme['PackageID'])
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array($VPSServer->Settings['Address'],$VPSOrder['IP'],$VPSScheme['Name']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
