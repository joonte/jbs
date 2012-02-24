<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$ExtraIPOrderID  = (integer) @$Args['ExtraIPOrderID'];
$ExtraIPServerID = (integer) @$Args['ExtraIPServerID'];
$DaysReserved    = (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
if(!$DaysReserved)
  return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($ExtraIPOrderID){
  #-----------------------------------------------------------------------------
  $ExtraIPOrder = DB_Select('ExtraIPOrdersOwners','StatusID',Array('UNIQ','ID'=>$ExtraIPOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($ExtraIPOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('ExtraIP_ORDER_NOT_FOUND','Заказ на ExtraIP не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($ExtraIPOrder['StatusID'] != 'Active')
        return new gException('ExtraIP_ORDER_NOT_ACTIVE','Заказ ExtraIP не активен');
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Count = DB_Count('ExtraIPs',Array('ID'=>$ExtraIPServerID));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    return new gException('SERVER_NOT_FOUND','Сервер [хост-машина] ExtraIP не найден');
}
#-------------------------------------------------------------------------------
$ExtraIPOrders = DB_Select('ExtraIPOrders',Array('ID','OrderID','(SELECT `CostDay` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrders`.`SchemeID`) as `CostDay`'),$ExtraIPOrderID?Array('ID'=>$ExtraIPOrderID):Array('Where'=>SPrintF("`ServerID` = %u AND `StatusID` = 'Active'",$ExtraIPServerID)));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('ExtraIP_ORDERS_NOT_FOUND','Нет активных заказов на ExtraIP принадлежащих данному серверу');
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ExtraIPOrders as $ExtraIPOrder){
      #-------------------------------------------------------------------------
      $IOrdersConsider = Array(
        #-----------------------------------------------------------------------
        'OrderID'      => $ExtraIPOrder['OrderID'],
        'DaysReserved' => $DaysReserved,
        'Cost'         => $ExtraIPOrder['CostDay'],
        'Discont'      => 1
      );
      #-------------------------------------------------------------------------
      $OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
      if(Is_Error($OrdersConsiderID))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','Orders'=>Count($ExtraIPOrders));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
