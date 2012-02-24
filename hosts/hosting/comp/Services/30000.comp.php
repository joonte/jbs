<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Item');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$VPSOrder = DB_Select('VPSOrdersOwners',Array('ID','Login','Domain','UserID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Item['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('VPS_ORDER_NOT_FOUND','Заказ VPS не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/VPSOrderPay',Array('VPSOrderID'=>$VPSOrder['ID'],'DaysPay'=>$Item['Amount']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('VPS_ORDER_PAY_ERROR','Ошибка оплаты заказа VPS',$Comp);
      case 'array':
        return TRUE;
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
