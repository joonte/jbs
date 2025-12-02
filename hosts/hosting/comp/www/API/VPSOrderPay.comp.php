<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$DaysPay        = (integer) @$Args['DaysPay'];
$IsNoBasket     = (boolean) @$Args['IsNoBasket'];
$IsUseBasket    = (boolean) @$Args['IsUseBasket'];
$PayMessage     =  (string) @$Args['PayMessage'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$VPSOrderID)
	return new gException('VPS_ORDER_NOT_SET','Не выбран заказ виртуального сервера');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','OrderID','ServiceID','ContractID','StatusID','UserID','Login','Domain','DaysRemainded','SchemeID','(SELECT `GroupID` FROM `Users` WHERE `VPSOrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`','(SELECT `Balance` FROM `Contracts` WHERE `VPSOrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`','(SELECT `IsPayed` FROM `Orders` WHERE `Orders`.`ID` = `VPSOrdersOwners`.`OrderID`) as `IsPayed`','(SELECT SUM(`DaysReserved`*`Cost`*(1-`Discont`)) FROM `OrdersConsider` WHERE `OrderID`=`VPSOrdersOwners`.`OrderID`) AS PayedSumm');
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('VPS_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UserID = (integer)$VPSOrder['UserID'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('VPSOrdersPay',(integer)$GLOBALS['__USER']['ID'],$UserID);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$StatusID = $VPSOrder['StatusID'];
#-------------------------------------------------------------------------------
if($VPSOrder['StatusID'] == 'Deleted')
	return new gException('DELETED_ORDER_CAN_NOT_PAY','Заказ на виртуальный сервер не может быть оплачен, так как он уже удалён. Обратитесь в службу поддержки, возможно сохранился архив ваших данных и его можно будет восстановить (потребуется сделать новый заказ на виртуальный сервер)');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!In_Array($StatusID,Array('Waiting','Active','Suspended')))
	return new gException('VPS_ORDER_CAN_NOT_PAY','Заказ не может быть оплачен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UserID = $VPSOrder['UserID'];
#-------------------------------------------------------------------------------
$VPSScheme = DB_Select('VPSSchemes',Array('ID','Name','CostDay','CostInstall','IsActive','IsProlong','MinDaysPay','MinDaysProlong','MaxDaysPay'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSScheme)){
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
# проверяем, это первая оплата или нет? если не первая, то минимальное число дней MinDaysProlong
Debug(SPrintF('[comp/www/API/VPSOrderPay]: ранее оплачено за заказ %s',$VPSOrder['PayedSumm']));
#-------------------------------------------------------------------------------
if($VPSOrder['IsPayed']){
	#-------------------------------------------------------------------------------
	if(!$VPSScheme['IsProlong'])
		return new gException('SCHEME_NOT_ALLOW_PROLONG','Тарифный план заказа виртуального сервера не позволяет продление');
	#-------------------------------------------------------------------------------
	$MinDaysPay = $VPSScheme['MinDaysProlong'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	if(!$VPSScheme['IsActive'])
		return new gException('SCHEME_NOT_ACTIVE','Тарифный план заказа виртуального сервера не активен');
	#-------------------------------------------------------------------------------
	$MinDaysPay = $VPSScheme['MinDaysPay'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/VPSOrderPay]: минимальное число дней %s',$MinDaysPay));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DaysPay < $MinDaysPay || $DaysPay > $VPSScheme['MaxDaysPay'])
	return new gException('WRONG_DAYS_PAY','Неверное кол-во дней оплаты');
#-------------------------------------------------------------------------------
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSOrderPay'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Politics',$VPSOrder['UserID'],$VPSOrder['GroupID'],$VPSOrder['ServiceID'],$VPSScheme['ID'],$DaysPay,SPrintF('VPS/%s',$VPSOrder['Login']));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSOrderID = (integer)$VPSOrder['ID'];
#-------------------------------------------------------------------------------
$CostPay = 0.00;
#-------------------------------------------------------------------------------
$DaysRemainded = $DaysPay;
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Bonuses',$DaysRemainded,$VPSOrder['ServiceID'],$VPSScheme['ID'],$UserID,$CostPay,$VPSScheme['CostDay'],$VPSOrder['OrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$CostPay = $Comp['CostPay'];
$Bonuses = $Comp['Bonuses'];
#-------------------------------------------------------------------------------
$CostPay = Round($CostPay,2);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($VPSScheme['CostInstall'] > 0){
	#-------------------------------------------------------------------------------
	# need give installation payment
	if(!$VPSOrder['IsPayed']){
		#-------------------------------------------------------------------------------
		# if it not prolongation
		$CostPay += $VPSScheme['CostInstall'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($IsUseBasket || (!$IsNoBasket && $CostPay > $VPSOrder['ContractBalance'])){
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$DaysRemainded = $VPSOrder['DaysRemainded'];
	#-------------------------------------------------------------------------------
	$sDate = Comp_Load('Formats/Date/Simple',Time() + $DaysRemainded*86400);
	if(Is_Error($sDate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$tDate = Comp_Load('Formats/Date/Simple',Time() + ($DaysRemainded + $DaysPay)*86400);
	if(Is_Error($tDate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$IBasket = Array('OrderID'=>$VPSOrder['OrderID'],'Comment'=>SPrintF('Тариф: %s, с %s по %s',$VPSScheme['Name'],$sDate,$tDate),'Amount'=>$DaysPay,'Summ'=>$CostPay);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('Basket',Array('Where'=>SPrintF('`OrderID` = %u',$VPSOrder['OrderID'])));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count){
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Update('Basket',$IBasket,Array('Where'=>SPrintF('`OrderID` = %u',$VPSOrder['OrderID'])));
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$IsInsert = DB_Insert('Basket',$IBasket);
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Basket/Update',$VPSOrder['UserID'],$VPSOrder['OrderID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	return Array('Status'=>'UseBasket');
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Order/Number',$VPSOrder['OrderID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$VPSOrder['Number'] = $Comp;
	#-------------------------------------------------------------------------------
	$IsUpdate = Comp_Load('www/Administrator/API/PostingMake',Array('ContractID'=>$VPSOrder['ContractID'],'Summ'=>-$CostPay,'ServiceID'=>$VPSOrder['ServiceID'],'Comment'=>SPrintF('№%s на %s дн.',$Comp,$DaysPay)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsUpdate)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return $IsUpdate;
	case 'array':
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Orders',Array('IsPayed'=>TRUE),Array('ID'=>$VPSOrder['OrderID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		switch($StatusID){
		case 'Waiting':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'OnCreate','RowsIDs'=>$VPSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return new gException($Comp->CodeID,$Comp->String);
			case 'array':
				# No more...
				break 2;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		case 'Active':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',Array('IsNotNotify'=>TRUE,'ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				# No more...
				break 2;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		case 'Suspended':
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'Active','RowsIDs'=>$VPSOrderID,'Comment'=>($PayMessage)?$PayMessage:'Заказ оплачен и будет активирован'));
			#-------------------------------------------------------------------------------
			switch(ValueOf($Comp)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				# No more...
				break 2;
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $VPSOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ VPS логин (%s), тариф (%s) оплачен на период %u дн.',$VPSOrder['Login'],$VPSScheme['Name'],$DaysPay)
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(Is_Error(DB_Commit($TransactionID)))
			return ERROR | @Trigger_Error(500);
		#-------------------END TRANSACTION---------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
