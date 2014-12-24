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
$DNSmanagerOrderID	= (integer) @$Args['DNSmanagerOrderID'];
$ContractID		= (integer) @$Args['ContractID'];
$ServerID		=  (string) @$Args['ServerID'];
$Login			=  (string) @$Args['Login'];
$Password		=  (string) @$Args['Password'];
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
$Server = DB_Select('Servers',Array('ID','ServersGroupID'),Array('UNIQ','ID'=>$ServerID));
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
$DNSmanagerScheme = DB_Select('DNSmanagerSchemes',Array('ID','ServersGroupID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
case 'array':
	#-------------------------------------------------------------------------------
	if($DNSmanagerScheme['ServersGroupID'] != $Server['ServersGroupID'])
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
if($DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$OldContractID = DB_Select('DNSmanagerOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`DNSmanagerOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$DNSmanagerOrderID));
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
$IDNSmanagerOrder = Array(
			'Login'		=> $Login,
			'Password'	=> $Password,
			'SchemeID'	=> $DNSmanagerScheme['ID']
			);
#-------------------------------------------------------------------------------
if($DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$DNSmanagerOrder = DB_Select('DNSmanagerOrders','OrderID',Array('UNIQ','ID'=>$DNSmanagerOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на DNSmanager не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID']),Array('ID'=>$DNSmanagerOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('DNSmanagerOrders',$IDNSmanagerOrder,Array('ID'=>$DNSmanagerOrderID));
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
	$OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>52000,'IsPayed'=>TRUE,'ServerID'=>$Server['ID']));
	if(Is_Error($OrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IDNSmanagerOrder['OrderID'] = $OrderID;
	#-------------------------------------------------------------------------------
	$DNSmanagerOrderID = DB_Insert('DNSmanagerOrders',$IDNSmanagerOrder);
	if(Is_Error($DNSmanagerOrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$DNSmanagerScheme['CostDay']);
	#-------------------------------------------------------------------------------
	$OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
	if(Is_Error($OrdersConsiderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$DNSmanagerOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на DNSmanager добавлен'));
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
