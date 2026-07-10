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
$OrderID	= (integer) @$Args['OrderID'];
$ServerID	= (integer) @$Args['ServerID'];
$DaysReserved	= (integer) @$Args['DaysReserved'];
$ChargeFree	= (boolean) @$Args['ChargeFree'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DaysReserved)
	return new gException('DAYS_NOT_DEFINED','Кол-во дней компенсации не указано');
#-------------------------------------------------------------------------------
if($OrderID){
	#-------------------------------------------------------------------------------
	$Order = DB_Select('OrdersOwners',Array('StatusID','ID','(SELECT `Code` FROM `Services` WHERE `ID` = `OrdersOwners`.`ServiceID`) AS `Code`'),Array('UNIQ','ID'=>$OrderID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Order)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('ORDER_NOT_FOUND','Заказ не найден');
	case 'array':
		#-------------------------------------------------------------------------------
		if($Order['StatusID'] != 'Active')
			return new gException('ORDER_NOT_ACTIVE','Заказ неактивен');
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Code = $Order['Code'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Server = DB_Select(Array('Servers','ServersGroups'),Array('(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServersGroups`.`ServiceID`) AS `Code`'),Array('UNIQ','Where'=>Array('`Servers`.`ServersGroupID` = `ServersGroups`.`ID`',SPrintF('`Servers`.`ID` = %u',$ServerID))));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Server)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVER_NOT_FOUND','Сервер не найден');
	case 'array':
		$Code = $Server['Code'];
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём список заказов
$Where = ($OrderID)?SPrintF('`OrderID` = %u',$OrderID):SPrintF('(SELECT `ServerID` FROM `OrdersOwners` WHERE `%sOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) = %u AND `StatusID` = "Active"',$Code,$ServerID);
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ContractID','ServiceID',SPrintF('(SELECT `CostDay` FROM `%sSchemes` WHERE `%sSchemes`.`ID` = `%sOrdersOwners`.`SchemeID`) as `CostDay`',$Code,$Code,$Code));
$Orders = DB_Select(SPrintF('%sOrdersOwners',$Code),$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('ORDERS_NOT_FOUND','Нет активных заказов принадлежащих данному серверу');
case 'array':
	#-------------------------------------------------------------------------------
	#-------------------------TRANSACTION-------------------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('OrderCompensation'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	foreach($Orders as $Order){
		#-------------------------------------------------------------------------------
		$IOrdersConsider = Array(
					'OrderID'	=> $Order['OrderID'],
					'DaysReserved'	=> $DaysReserved,
					'Cost'		=> $Order['CostDay'],
					'Discont'	=> ($ChargeFree)?1:0
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
		#-------------------------------------------------------------------------------
		// если это за денежку, списываем эту самую денежку
		if(!$ChargeFree){
			#-------------------------------------------------------------------------------
			$Contract = DB_Select('Contracts',Array('ID','Balance','UserID'),Array('UNIQ','ID'=>$Order['ContractID']));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Contract)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return new gException('CONTRACT_NOT_FOUND','Договор не найден');
			case 'array':
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('Formats/Order/Number',$Order['OrderID']);
			if(Is_Error($Comp))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$Order['Number'] = $Comp;
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			$Before = (double)$Contract['Balance'];
			$After	= $Contract['Balance'] - $DaysReserved*$Order['CostDay'];
			#-------------------------------------------------------------------------------
			// проставляем новый балланс
			$IsUpdated = DB_Update('Contracts',Array('Balance'=>$After),Array('ID'=>$Contract['ID']));
			if(Is_Error($IsUpdated))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			// примечание операции
			$IPosting = Array(
					'ContractID' => $Contract['ID'],
					'ServiceID'  => $Order['ServiceID'],
					'Comment'    => SPrintF('ручное продление №%s на %s дн.',$Comp,$DaysReserved),
					'Before'     => $Before,
					'After'      => $After
					);
			#-------------------------------------------------------------------------------
			$PostingID = DB_Insert('Postings',$IPosting);
			if(Is_Error($PostingID))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/Administrator/API/OrderCompensation]: ручное продление заказа %s на %s',$Comp,$DaysReserved));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------END TRANSACTION---------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','Orders'=>Count($Orders));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
