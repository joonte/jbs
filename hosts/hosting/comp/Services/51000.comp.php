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
$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('ID','UserID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Item['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('HOSTING_ORDER_NOT_FOUND','Заказ лицензии ISPsystem не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/ISPswOrderPay',Array('ISPswOrderID'=>$ISPswOrder['ID'],'DaysPay'=>$Item['Amount']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('HOSTING_ORDER_PAY_ERROR','Ошибка оплаты заказа лицензии ISPsystem',$Comp);
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
