<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ProxyOrderID		= (integer) @$Args['ProxyOrderID'];
$ContractID		= (integer) @$Args['ContractID'];
$ServerID		=  (string) @$Args['ServerID'];
$Host			=  (string) @$Args['Host'];
$Port			= (integer) @$Args['Port'];
$Login			=  (string) @$Args['Login'];
$Password		=  (string) @$Args['Password'];
$ProtocolType		=  (string) @$Args['ProtocolType'];
$IP			=  (string) @$Args['IP'];
$SchemeID		= (integer) @$Args['SchemeID'];
$DaysReserved		= (integer) @$Args['DaysReserved'];
$IsCreate		= (boolean) @$Args['IsCreate'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Servers',Array('ID'=>$ServerID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('SERVER_NOT_FOUND','Сервер не найден');
#-------------------------------------------------------------------------------
if($Count > 1)
	return new gException('SERVER_NOT_FOUND','Сервер не задан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID','ServersGroupID','(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) AS `ServiceID`'),Array('UNIQ','ID'=>$ServerID));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVER_NOT_FOUND','Сервер размещения не найден');
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ProxyScheme = DB_Select('ProxySchemes',Array('ID','ServersGroupID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
case 'array':
	#-------------------------------------------------------------------------------
	if($ProxyScheme['ServersGroupID'] != $Server['ServersGroupID'])
		return new gException('SERVERS_GROUP_NOT_EQUAL','Группа серверов сервера размещения и тарифного плана не совпадают');
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_FILLED','Договор клиента не указан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем старый контракт - сравниваем номерки, при условии что старый был
if($ProxyOrderID){
	#-------------------------------------------------------------------------------
	$OldContractID = DB_Select('ProxyOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`ProxyOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$ProxyOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OldContractID)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		if($OldContractID['ContractID'] != $ContractID){
			#-------------------------------------------------------------------------------
			# проверяем есть ли профиль у нового контракта
			$Count = DB_Count('Contracts',Array('Where'=>SPrintF('`ID` = %u AND `ProfileID` IS NOT NULL',$ContractID)));
			#-------------------------------------------------------------------------------
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if(!$Count)
				return new gException('CONTRACT_WITHOUT_PROFILE','У выбранного договора отсутствует профиль. Выберите другой договор, или, пусть клиент создаст и назначит профиль для этого договора.');
			#-------------------------------------------------------------------------------
		}
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
$IProxyOrder = Array(
			'Host'		=> $Host,
			'Port'		=> $Port,
			'Login'		=> $Login,
			'Password'	=> $Password,
			'ProtocolType'	=> $ProtocolType,
			'IP'		=> $IP,
			'SchemeID'	=> $ProxyScheme['ID']
			);
#-------------------------------------------------------------------------------
if($ProxyOrderID){
	#-------------------------------------------------------------------------------
	$ProxyOrder = DB_Select('ProxyOrders','OrderID',Array('UNIQ','ID'=>$ProxyOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ProxyOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на Proxy не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID']),Array('ID'=>$ProxyOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('ProxyOrders',$IProxyOrder,Array('ID'=>$ProxyOrderID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>$Server['ServiceID'],'IsPayed'=>TRUE,'ServerID'=>$Server['ID'],'Params'=>''));
	if(Is_Error($OrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IProxyOrder['OrderID'] = $OrderID;
	#-------------------------------------------------------------------------------
	$ProxyOrderID = DB_Insert('ProxyOrders',$IProxyOrder);
	if(Is_Error($ProxyOrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$ProxyScheme['CostDay']);
	#-------------------------------------------------------------------------------
	$OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
	if(Is_Error($OrdersConsiderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ProxyOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$ProxyOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на Proxy добавлен'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
