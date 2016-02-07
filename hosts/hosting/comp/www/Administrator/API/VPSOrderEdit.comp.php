<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$VPSOrderID     = (integer) @$Args['VPSOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$ServerID       =  (string) @$Args['ServerID'];
$Domain         =  (string) @$Args['Domain'];
$DiskTemplate	=  (string) @$Args['DiskTemplate'];
$Login          =  (string) @$Args['Login'];
$IP		=  (string) @$Args['IP'];
$Password       =  (string) @$Args['Password'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$IsCreate       = (boolean) @$Args['IsCreate'];
#-------------------------------------------------------------------------------
$Count = DB_Count('Servers',Array('ID'=>$ServerID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('SERVER_NOT_FOUND','Сервер не найден');
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Domain'],$Domain))
	return new gException('WRONG_DOMAIN','Неверный домен');
#-------------------------------------------------------------------------------
if(!$Login)
	return new gException('LOGIN_NOT_FILLED','Логин пользователя не указан');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['IP'],$IP))
	return new gException('WRONG_IP','Неверный IP адрес');
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID','ServersGroupID'),Array('UNIQ','ID'=>$ServerID));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVER_NOT_FOUND','Сервер размещения не найден');
	break;
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$VPSScheme = DB_Select('VPSSchemes',Array('ID','ServersGroupID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
	break;
case 'array':
	#-------------------------------------------------------------------------------
	if($VPSScheme['ServersGroupID'] != $Server['ServersGroupID'])
		return new gException('SERVERS_GROUP_NOT_EQUAL','Группа серверов сервера размещения и тарифного плана не совпадают');
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_FILLED','Договор клиента не указан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($VPSOrderID){
	#-------------------------------------------------------------------------------
	# ищщем старый контракт - сравниваем номерки
	$OldContractID = DB_Select('VPSOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`VPSOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$VPSOrderID));
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
$IVPSOrder = Array(
			'Domain'	=> $Domain,
			'Login'		=> $Login,
			'IP'		=> $IP,
			'Password'	=> $Password,
			'SchemeID'	=> $VPSScheme['ID'],
		);
#-------------------------------------------------------------------------------
if($VPSOrderID){
	#-------------------------------------------------------------------------------
	$VPSOrder = DB_Select('VPSOrders','OrderID',Array('UNIQ','ID'=>$VPSOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($VPSOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('VPS_ORDER_NOT_FOUND','Заказ на VPS не найден');
		break;
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID'],'Params'=>Array('DiskTemplate'=>$DiskTemplate)),Array('ID'=>$VPSOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('VPSOrders',$IVPSOrder,Array('ID'=>$VPSOrderID));
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
	$OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID'],'ServiceID'=>30000,'IsPayed'=>TRUE,'Params'=>Array('DiskTemplate'=>$DiskTemplate)));
	if(Is_Error($OrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IVPSOrder['OrderID'] = $OrderID;
	#-------------------------------------------------------------------------------
	$VPSOrderID = DB_Insert('VPSOrders',$IVPSOrder);
	if(Is_Error($VPSOrderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$VPSScheme['CostDay']);
	#-------------------------------------------------------------------------------
	$OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
	if(Is_Error($OrdersConsiderID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$VPSOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на VPS добавлен'));
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
