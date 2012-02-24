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
$Login          =  (string) @$Args['Login'];
$Password       =  (string) @$Args['Password'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$IsCreate       = (boolean) @$Args['IsCreate'];
#-------------------------------------------------------------------------------
$Count = DB_Count('VPSServers',Array('ID'=>$ServerID));
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
$Server = DB_Select('VPSServers',Array('ID','ServersGroupID'),Array('UNIQ','ID'=>$ServerID));
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
    #---------------------------------------------------------------------------
    if($VPSScheme['ServersGroupID'] != $Server['ServersGroupID'])
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
$IVPSOrder = Array(
  #-----------------------------------------------------------------------------
  'ServerID' => $Server['ID'],
  'Domain'   => $Domain,
  'Login'    => $Login,
  'Password' => $Password,
  'SchemeID' => $VPSScheme['ID']
);
#-------------------------------------------------------------------------------
if($VPSOrderID){
  #-----------------------------------------------------------------------------
  $VPSOrder = DB_Select('VPSOrders','OrderID',Array('UNIQ','ID'=>$VPSOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('VPS_ORDER_NOT_FOUND','Заказ на VPS не найден');
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Orders',Array('ContractID'=>$ContractID),Array('ID'=>$VPSOrder['OrderID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('VPSOrders',$IVPSOrder,Array('ID'=>$VPSOrderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $OrderID = DB_Insert('Orders',Array('ContractID'=>$ContractID,'ServiceID'=>30000,'IsPayed'=>TRUE));
  if(Is_Error($OrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IVPSOrder['OrderID'] = $OrderID;
  #-----------------------------------------------------------------------------
  $VPSOrderID = DB_Insert('VPSOrders',$IVPSOrder);
  if(Is_Error($VPSOrderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IOrdersConsider = Array('OrderID'=>$OrderID,'DaysReserved'=>$DaysReserved,'Cost'=>$VPSScheme['CostDay']);
  #-----------------------------------------------------------------------------
  $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
  if(Is_Error($OrdersConsiderID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>($IsCreate?'OnCreate':'Active'),'RowsIDs'=>$VPSOrderID,'IsNoTrigger'=>!$IsCreate,'Comment'=>'Заказ на VPS успешно добавлен'));
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
