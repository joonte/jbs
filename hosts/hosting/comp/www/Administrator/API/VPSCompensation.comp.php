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
$VPSOrderID  = (integer) @$Args['VPSOrderID'];
$VPSServerID = (integer) @$Args['VPSServerID'];
$DaysReserved    = (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
if(!$DaysReserved)
  return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($VPSOrderID){
  #-----------------------------------------------------------------------------
  $VPSOrder = DB_Select('VPSOrdersOwners','StatusID',Array('UNIQ','ID'=>$VPSOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($VPSOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('VPS_ORDER_NOT_FOUND','Заказ на VPS не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($VPSOrder['StatusID'] != 'Active')
        return new gException('VPS_ORDER_NOT_ACTIVE','Заказ VPS не активен');
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Count = DB_Count('VPSServers',Array('ID'=>$VPSServerID));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    return new gException('SERVER_NOT_FOUND','Сервер [хост-машина] VPS не найден');
}
#-------------------------------------------------------------------------------
$VPSOrders = DB_Select('VPSOrders',Array('ID','OrderID','(SELECT `CostDay` FROM `VPSSchemes` WHERE `VPSSchemes`.`ID` = `VPSOrders`.`SchemeID`) as `CostDay`'),$VPSOrderID?Array('ID'=>$VPSOrderID):Array('Where'=>SPrintF("`ServerID` = %u AND `StatusID` = 'Active'",$VPSServerID)));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('VPS_ORDERS_NOT_FOUND','Нет активных заказов на VPS принадлежащих данному серверу');
  case 'array':
    #---------------------------------------------------------------------------
    foreach($VPSOrders as $VPSOrder){
      #-------------------------------------------------------------------------
      $IOrdersConsider = Array(
        #-----------------------------------------------------------------------
        'OrderID' 	 => $VPSOrder['OrderID'],
        'DaysReserved'   => $DaysReserved,
        'Cost'           => $VPSOrder['CostDay'],
        'Discont'        => 1
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
    return Array('Status'=>'Ok','Orders'=>Count($VPSOrders));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
