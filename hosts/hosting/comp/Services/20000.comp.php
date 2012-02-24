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
$DomainOrder = DB_Select('DomainsOrdersOwners',Array('ID','UserID','DomainName','(SELECT `Name` FROM `DomainsSchemes` WHERE `DomainsSchemes`.`ID` = `DomainsOrdersOwners`.`SchemeID`) as `DomainZone`'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Item['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOMAIN_ORDER_NOT_FOUND','Заказ домена не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('www/API/DomainOrderPay',Array('DomainOrderID'=>$DomainOrder['ID'],'YearsPay'=>$Item['Amount']));
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    switch(ValueOf($Comp)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return new gException('DOMAIN_ORDER_PAY_ERROR','Ошибка оплаты заказа домена',$Comp);
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
