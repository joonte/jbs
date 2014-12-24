<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$DNSmanagerOrderID	= (integer) @$Args['DNSmanagerOrderID'];
$ServerID		= (integer) @$Args['ServerID'];
$DaysReserved		= (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DaysReserved)
	return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($DNSmanagerOrderID){
	#-------------------------------------------------------------------------------
	$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',Array('StatusID','OrderID'),Array('UNIQ','ID'=>$DNSmanagerOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($DNSmanagerOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на вторичный DNS не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		if($DNSmanagerOrder['StatusID'] != 'Active')
			return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ вторичного DNS не активен');
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Servers',Array('ID'=>$ServerID));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!$Count)
		return new gException('SERVER_NOT_FOUND','Сервер DNS не найден');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DNSmanagerOrders = DB_Select('DNSmanagerOrdersOwners',Array('ID','OrderID','(SELECT `CostDay` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`) as `CostDay`'),$DNSmanagerOrderID?Array('ID'=>$DNSmanagerOrderID):Array('Where'=>SPrintF("(SELECT `ServerID` FROM `OrdersOwners` WHERE `DNSmanagerOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) = %u AND `StatusID` = 'Active'",$ServerID)));

#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('HOSTING_ORDERS_NOT_FOUND','Нет активных заказов на вторичный DNS принадлежащих данному серверу');
case 'array':
	#-------------------------------------------------------------------------------
	foreach($DNSmanagerOrders as $DNSmanagerOrder){
		#-------------------------------------------------------------------------------
		$IOrdersConsider = Array(
					'OrderID'	=> $DNSmanagerOrder['OrderID'],
					'DaysReserved'	=> $DaysReserved,
					'Cost'		=> $DNSmanagerOrder['CostDay'],
					'Discont'	=> 1
					);
		#-------------------------------------------------------------------------------
		$OrdersConsiderID = DB_Insert('OrdersConsider',$IOrdersConsider);
		#-------------------------------------------------------------------------------
		if(Is_Error($OrdersConsiderID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('OrdersConsider',Array('DaysConsidered'=>0),Array('ID'=>$OrdersConsiderID));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','Orders'=>Count($DNSmanagerOrders));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
