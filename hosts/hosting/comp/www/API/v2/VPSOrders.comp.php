<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
//$ContractID     = (integer) @$Args['ContractID'];
//$IsUponConsider = (boolean) @$Args['IsUponConsider'];
$OrderID	= (integer) @$Args[3];

//Debug(print_r($Args,true));
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
if($OrderID > 0)
	$Where[] = SPrintF('`OrderID` = %u',$OrderID);
#-------------------------------------------------------------------------------
$Columns = Array('*','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `VPSOrdersOwners`.`ContractID`) AS `Customer`','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`) AS `ServerID`');
#-------------------------------------------------------------------------------
$VPSOrders = DB_Select('VPSOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Out;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Servers = Array();
#-------------------------------------------------------------------------------
foreach($VPSOrders as $VPSOrder){
	#-------------------------------------------------------------------------------
	$ServerID = $VPSOrder['ServerID'];
	#-------------------------------------------------------------------------------
	// чтоб не делать запрросы на каждый тариф, храним сервера в массиве
	if(!IsSet($Servers[$ServerID])){
		#-------------------------------------------------------------------------------
		$Server = DB_Select('ServersOwners',Array('Address','Params'),Array('UNIQ','ID'=>$ServerID));
		#-------------------------------------------------------------------------------
		if(!Is_Array($Server))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$Servers[$ServerID] = $Server;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// добавляем сервер
	$VPSOrder['Address'] = $Servers[$ServerID]['Address'];
	#-------------------------------------------------------------------------------
	// добавляем ДНС
	for($i = 1; $i <= 4; $i++){
		#-------------------------------------------------------------------------------
		$NsName = SPrintF('Ns%sName',$i);
		#-------------------------------------------------------------------------------
		$VPSOrder[$NsName] = $Servers[$ServerID]['Params'][$NsName];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if($Servers[$ServerID]['Params']['SystemID'] == 'VmManager6_VPS')
		$VPSOrder['Login'] = SPrintF('%s@%s',$VPSOrder['Login'],$Servers[$ServerID]['Params']['Domain']);
	#-------------------------------------------------------------------------------
	UnSet($VPSOrder['AdminNotice']);
	#-------------------------------------------------------------------------------
	$Out[$VPSOrder['ID']] = $VPSOrder;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

