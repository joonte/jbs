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
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$ServerID       =  (string) @$Args['ServerID'];
$Domain         =  (string) @$Args['Domain'];
$Login          =  (string) @$Args['Login'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$IsCreate       = (boolean) @$Args['IsCreate'];
$OrderType	=  (string) @$Args['OrderType'];
$DependOrder	=  (string) @$Args['DependOrder'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
#if(!Preg_Match($Regulars['Domain'],$Domain))
#  return new gException('WRONG_DOMAIN','Неверный домен');
#-------------------------------------------------------------------------------
if(!$Login)
  return new gException('IP_NOT_FILLED','Не указан IP адрес');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ExtraIPScheme = DB_Select('ExtraIPSchemes',Array('ID','CostDay'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
  break;
  case 'array':
    #---------------------------------------------------------------------------
#    if($ExtraIPScheme['sGroupID'] != $Server['sGroupID'])
#      return new gException('SERVERS_GROUP_NOT_EQUAL','Группа серверов сервера размещения и тарифного плана не совпадают');
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
# ищщем старый контракт - сравниваем номерки
$OldContractID = DB_Select('ExtraIPOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`ExtraIPOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$ExtraIPOrderID));
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
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# find DependOrderID
if($OrderType == "DS"){
	$Where = "`IP` = '" . $DependOrder . "' AND `ContractID` = '" . $ContractID . "'";
}else{
	$Where = "`Login` = '" . $DependOrder . "' AND `ContractID` = '" . $ContractID . "'";
}
if($OrderType == "Manual"){
	$DependOrderInfo	= array('ID' => 0);
}else{
	$DependOrderInfo = DB_Select($OrderType . 'OrdersOwners',Array('*'),Array('UNIQ','Where'=>$Where));
	switch(ValueOf($DependOrderInfo)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('DependOrder_ORDER_NOT_FOUND','Не найден заказ к которому прикреплять IP адрес [' . $DependOrder . ']');
		break;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#-------------------------------------------------------------------------------
$IExtraIPOrder = Array(
	'Domain'	=> $Domain,
	'Login'		=> $Login,
	'SchemeID'	=> $ExtraIPScheme['ID'],
	'OrderType'	=> $OrderType,
	'DependOrderID'	=> $DependOrderInfo['ID']
);
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
  #-----------------------------------------------------------------------------
  $ExtraIPOrder = DB_Select('ExtraIPOrders','OrderID',Array('UNIQ','ID'=>$ExtraIPOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ExtraIP_ORDER_NOT_FOUND','Заказ на ExtraIP не найден');
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID),Array('ID'=>$ExtraIPOrder['OrderID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('ExtraIPOrders',$IExtraIPOrder,Array('ID'=>$ExtraIPOrderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>50000,'IsPayed'=>TRUE));
  if(Is_Error($OrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IExtraIPOrder['OrderID'] = $OrderID;
  #-----------------------------------------------------------------------------
  $ExtraIPOrderID = DB_Insert('ExtraIPOrders',$IExtraIPOrder);
  if(Is_Error($ExtraIPOrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$ExtraIPScheme['CostDay']);
  #-----------------------------------------------------------------------------
  $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
  if(Is_Error($OrdersConsiderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$ExtraIPOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на ExtraIP успешно добавлен'));
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
