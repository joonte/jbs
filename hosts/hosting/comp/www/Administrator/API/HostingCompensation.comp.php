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
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$HostingOrderID  = (integer) @$Args['HostingOrderID'];
$ServerID = (integer) @$Args['ServerID'];
$DaysReserved    = (integer) @$Args['DaysReserved'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DaysReserved)
	return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($HostingOrderID){
	#-------------------------------------------------------------------------------
	$HostingOrder = DB_Select('HostingOrdersOwners',Array('StatusID','OrderID'),Array('UNIQ','ID'=>$HostingOrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($HostingOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('HOSTING_ORDER_NOT_FOUND','Заказ на хостинг не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		if($HostingOrder['StatusID'] != 'Active')
			return new gException('HOSTING_ORDER_NOT_ACTIVE','Заказ хостинга не активен');
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
		return new gException('SERVER_NOT_FOUND','Сервер хостинга не найден');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$HostingOrders = DB_Select('HostingOrdersOwners',Array('ID','OrderID','(SELECT `CostDay` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `CostDay`'),$HostingOrderID?Array('ID'=>$HostingOrderID):Array('Where'=>SPrintF("(SELECT `ServerID` FROM `OrdersOwners` WHERE `HostingOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) = %u AND `StatusID` = 'Active'",$ServerID)));

#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('HOSTING_ORDERS_NOT_FOUND','Нет активных заказов на хостинг принадлежащих данному серверу');
case 'array':
	#-------------------------------------------------------------------------------
	foreach($HostingOrders as $HostingOrder){
		#-------------------------------------------------------------------------------
		$IOrdersConsider = Array(
					'OrderID'	=> $HostingOrder['OrderID'],
					'DaysReserved'	=> $DaysReserved,
					'Cost'		=> $HostingOrder['CostDay'],
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
	return Array('Status'=>'Ok','Orders'=>Count($HostingOrders));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
