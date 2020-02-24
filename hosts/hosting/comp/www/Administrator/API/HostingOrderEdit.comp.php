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
$HostingOrderID = (integer) @$Args['HostingOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$ServerID       =  (string) @$Args['ServerID'];
$Domain         =  (string) @$Args['Domain'];
$Login          =  (string) @$Args['Login'];
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
$HostingScheme = DB_Select('HostingSchemes',Array('ID','ServersGroupID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
  break;
  case 'array':
    #---------------------------------------------------------------------------
    if($HostingScheme['ServersGroupID'] != $Server['ServersGroupID'])
      return new gException('SERVERS_GROUP_NOT_EQUAL','Группа серверов сервера размещения и тарифного плана не совпадают');
    #---------------------------------------------------------------------------
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$ContractID)
  return new gException('CONTRACT_NOT_FILLED','Договор клиента не указан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# ищщем старый контракт - сравниваем номерки, при условии что старый был
if($HostingOrderID){
  $OldContractID = DB_Select('HostingOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`HostingOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$HostingOrderID));
  #-------------------------------------------------------------------------------
  #-------------------------------------------------------------------------------
  switch(ValueOf($OldContractID)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    if($OldContractID['ContractID'] != $ContractID){
      # проверяем есть ли профиль у нового контракта
      $Count = DB_Count('Contracts',Array('Where'=>SPrintF('`ID` = %u AND `ProfileID` IS NOT NULL',$ContractID)));
      if(Is_Error($Count))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------------
      if(!$Count)
        return new gException('CONTRACT_WITHOUT_PROFILE','У выбранного договора отсутствует профиль. Выберите другой договор, или, пусть клиент создаст и назначит профиль для этого договора.');
      #-------------------------------------------------------------------------------
    }
    break;
  default:
    return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IHostingOrder = Array(
			'Domain'	=> $Domain,
			'Login'		=> $Login,
			'Password'	=> $Password,
			'SchemeID'	=> $HostingScheme['ID']
			);
#-------------------------------------------------------------------------------
if($HostingOrderID){
	#-------------------------------------------------------------------------------
	$HostingOrder = DB_Select('HostingOrders','OrderID',Array('UNIQ','ID'=>$HostingOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на хостинг не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID']),Array('ID'=>$HostingOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('HostingOrders',$IHostingOrder,Array('ID'=>$HostingOrderID));
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
  #-----------------------------------------------------------------------------
  $OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>10000,'IsPayed'=>TRUE,'ServerID'=>$Server['ID'],'Params'=>''));
  if(Is_Error($OrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IHostingOrder['OrderID'] = $OrderID;
  #-----------------------------------------------------------------------------
  $HostingOrderID = DB_Insert('HostingOrders',$IHostingOrder);
  if(Is_Error($HostingOrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$HostingScheme['CostDay']);
  #-----------------------------------------------------------------------------
  $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
  if(Is_Error($OrdersConsiderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$HostingOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ хостинга добавлен'));
  #-----------------------------------------------------------------------------
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
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
