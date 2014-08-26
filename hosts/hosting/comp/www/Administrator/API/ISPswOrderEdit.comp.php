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
$ISPswOrderID   = (integer) @$Args['ISPswOrderID'];
$ContractID     = (integer) @$Args['ContractID'];
$ServerID	= (integer) @$Args['ServerID'];
$IP             =  (string) @$Args['IP'];
$LicenseID	= (integer) @$Args['LicenseID'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$IsCreate       = (boolean) @$Args['IsCreate'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID'),Array('UNIQ','ID'=>$ServerID));
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
$ISPswScheme = DB_Select('ISPswSchemes',Array('*'),Array('UNIQ','ID'=>$SchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswScheme)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('SCHEME_NOT_FOUND','Тарифный план не найден');
  break;
  case 'array':
    #---------------------------------------------------------------------------
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
if($ISPswOrderID){
  # ищщем старый контракт - сравниваем номерки
  $OldContractID = DB_Select('ISPswOrders','(SELECT `ContractID` FROM `Orders` WHERE `Orders`.`ID`=`ISPswOrders`.`OrderID`) AS `ContractID`',Array('UNIQ','ID'=>$ISPswOrderID));
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
$IISPswOrder = Array(
  #-----------------------------------------------------------------------------
  'IP'       => $IP,
  'SchemeID' => $ISPswScheme['ID'],
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($LicenseID){
	$License = DB_Count('ISPswLicenses',Array('ID'=>$LicenseID));
	if(Is_Error($License))
		return ERROR | @Trigger_Error(500);
	if(!$License)
		return new gException('SELECTED_LICENSE_NOT_FOUND','Указанный идентификатор лицензии не найден');
	$IISPswOrder['LicenseID'] = $LicenseID;
}else{
	$IISPswOrder['LicenseID'] = NULL;
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ISPswOrderID){
  #-----------------------------------------------------------------------------
  $ISPswOrder = DB_Select('ISPswOrders','OrderID',Array('UNIQ','ID'=>$ISPswOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ISPswOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на ПО ISPsystem не найден');
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID']),Array('ID'=>$ISPswOrder['OrderID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('ISPswOrders',$IISPswOrder,Array('ID'=>$ISPswOrderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServerID'=>$Server['ID'],'ServiceID'=>51000,'IsPayed'=>TRUE));
  if(Is_Error($OrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IISPswOrder['OrderID'] = $OrderID;
  #-----------------------------------------------------------------------------
  $ISPswOrderID = DB_Insert('ISPswOrders',$IISPswOrder);
  if(Is_Error($ISPswOrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$ISPswScheme['CostDay']);
  #-----------------------------------------------------------------------------
  $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
  if(Is_Error($OrdersConsiderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$ISPswOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ ПО ISPsystem добавлен'));
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
