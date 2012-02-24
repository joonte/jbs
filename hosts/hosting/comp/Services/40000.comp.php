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
$DSOrder = DB_Select('DSOrdersOwners',Array('ID','IP','UserID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Item['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DS_ORDER_NOT_FOUND','Заказ на аренду сервера не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/DSOrderPay',Array('DSOrderID'=>$DSOrder['ID'],'DaysPay'=>$Item['Amount']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('DS_ORDER_PAY_ERROR','Ошибка оплаты заказа выделенного сервера',$Comp);
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
