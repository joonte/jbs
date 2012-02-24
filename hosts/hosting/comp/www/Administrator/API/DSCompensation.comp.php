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
$DSOrderID  = (integer) @$Args['DSOrderID'];
$DSServerID = (integer) @$Args['DSServerID'];
$DaysReserved    = (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
if(!$DaysReserved)
  return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($DSOrderID){
  #-----------------------------------------------------------------------------
  $DSOrder = DB_Select('DSOrdersOwners','StatusID',Array('UNIQ','ID'=>$DSOrderID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($DSOrder)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      return new gException('DS_ORDER_NOT_FOUND','Заказ на DS не найден');
    case 'array':
      #-------------------------------------------------------------------------
      if($DSOrder['StatusID'] != 'Active')
        return new gException('DS_ORDER_NOT_ACTIVE','Заказ DS не активен');
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}else{
  #-----------------------------------------------------------------------------
  $Count = DB_Count('DSServers',Array('ID'=>$DSServerID));
  if(Is_Error($Count))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Count)
    return new gException('SERVER_NOT_FOUND','Сервер [хост-машина] DS не найден');
}
#-------------------------------------------------------------------------------
$DSOrders = DB_Select('DSOrders',Array('ID','OrderID','(SELECT `CostDay` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `DSOrders`.`SchemeID`) as `CostDay`'),$DSOrderID?Array('ID'=>$DSOrderID):Array('Where'=>SPrintF("`ServerID` = %u AND `StatusID` = 'Active'",$DSServerID)));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DS_ORDERS_NOT_FOUND','Нет активных заказов на DS принадлежащих данному серверу');
  case 'array':
    #---------------------------------------------------------------------------
    foreach($DSOrders as $DSOrder){
      #-------------------------------------------------------------------------
      $IOrdersConsider = Array(
        #-----------------------------------------------------------------------
        'OrderID'        => $DSOrder['OrderID'],
        'DaysReserved'   => $DaysReserved,
        'Cost'           => $DSOrder['CostDay'],
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
    return Array('Status'=>'Ok','Orders'=>Count($DSOrders));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
