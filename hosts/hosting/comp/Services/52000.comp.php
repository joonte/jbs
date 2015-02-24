<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Item');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('ID','Login','UserID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Item['OrderID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DNS_ORDER_NOT_FOUND','Заказ вторичного DNS не найден');
case 'array':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/DNSmanagerOrderPay',Array('DNSmanagerOrderID'=>$DNSmanagerOrder['ID'],'DaysPay'=>$Item['Amount']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('DNS_ORDER_PAY_ERROR','Ошибка оплаты заказа вторичного DNS',$Comp);
	case 'array':
		return TRUE;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
