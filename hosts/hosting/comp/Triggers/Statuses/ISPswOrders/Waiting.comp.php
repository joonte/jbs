<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ISPswOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Debug(SPrintF('[comp/Triggers/Statuses/ISPswOrders/Waiting]: ISPswOrder = %s',print_r($ISPswOrder,true)));
#-------------------------------------------------------------------------------
$UserID = $GLOBALS['__USER']['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Service = DB_Select('Services',Array('Code','Name'),Array('UNIQ','ID'=>51000));
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Scheme = DB_Select(SPrintF('%sSchemes',$Service['Code']),Array('*'),Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
switch(ValueOf($Scheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# масив для вставки/обновления таблицы StatusesHistory
$IOrdersHistory = Array('StatusDate'=>Time());
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, есть ли такой заказ в таблице с историей
$Where = Array(SPrintF('`UserID` = %u',$UserID),'`ServiceID` = 51000',SPrintF('`SchemeID` = %u',$ISPswOrder['SchemeID']),SPrintF('`OrderID` = %u',$ISPswOrder['OrderID']));
#-------------------------------------------------------------------------------
$OrdersHistory = DB_Select('OrdersHistory',Array('ID'),Array('UNIQ','Where'=>$Where));
switch(ValueOf($OrdersHistory)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	# проверяем, были ли такие же заказы
	$Count = DB_Count('OrdersHistory',Array('Where'=>Array(SPrintF('`UserID` = %u',$UserID),'`ServiceID` = 51000',SPrintF('`SchemeID` = %u',$ISPswOrder['SchemeID']))));
	if(Is_Error($Count))
		return ERROR | Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# проверяем, как много таких заказов можно делать
	if($Scheme['MaxOrders'] > 0 && $Count >= $Scheme['MaxOrders'])
		return new gException('TOO_MANY_ORDERS',SPrintF('Для данного тарифного плана существует ограничение на максимальное число заказов, равное %s. Ранее, вы уже делали заказы по данному тарифу, и больше сделать не можете. Выберите другой тарифный план.',$Scheme['MaxOrders']));
	#-------------------------------------------------------------------------------
	# вносим заказ в таблицу, если его там нет
	$IOrdersHistory['UserID']	= $UserID;
	$IOrdersHistory['Email']	= $GLOBALS['__USER']['Email'];
	$IOrdersHistory['ServiceID']	= 51000;
	$IOrdersHistory['ServiceName']	= $Service['Name'];
	$IOrdersHistory['SchemeID']	= $ISPswOrder['SchemeID'];
	$IOrdersHistory['SchemeName']	= $Scheme['Name'];
	$IOrdersHistory['OrderID']	= $ISPswOrder['OrderID'];
	$IOrdersHistory['CreateDate']	= Time();
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('OrdersHistory',$IOrdersHistory);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	# это вторичная проставка статуса для заказа. просто обновляем StatusDate
	$IsUpdate = DB_Update('OrdersHistory',$IOrdersHistory,Array('ID'=>$OrdersHistory['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------



?>
