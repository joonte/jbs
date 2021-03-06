<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ServiceOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(!In_Array($ServiceOrder['StatusID'],Array('Waiting','Deleted'))){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Order/Number',$ServiceOrder['ID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return new gException('ORDER_CAN_NOT_DELETED',SPrintF('Заказ #%s не может быть удален, удалить можно только заказы в статусе "Удалён" или "Ожидает оплаты"',$Comp));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$ServiceOrder['ID']));
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Comp;
case 'array':
	#-------------------------------------------------------------------------------
	$Service = DB_Select('Services',Array('Name','Params'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
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
