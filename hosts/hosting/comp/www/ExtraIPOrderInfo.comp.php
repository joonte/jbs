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
if(Is_Null($Args))
	if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php')))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ExtraIPOrderID = (integer) @$Args['ExtraIPOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `ExtraIPOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ExtraIPOrdersOwners`.`OrderID`) AS `UserNotice`',
			'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ExtraIPOrdersOwners`.`OrderID`) AS `AdminNotice`',
			'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ExtraIPOrdersOwners`.`ContractID`) AS `Customer`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `ExtraIPOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',$Columns,Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
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
# Select Depend Order Info
if($ExtraIPOrder['OrderType'] == "Hosting" || $ExtraIPOrder['OrderType'] == "VPS"){
	#-------------------------------------------------------------------------------
	$Columns = Array('*',SPrintF('(SELECT `Address` FROM `Servers` WHERE `%sOrdersOwners`.`ServerID` = `Servers`.`ID`) AS Address',$ExtraIPOrder['OrderType']));
	#-------------------------------------------------------------------------------
	$ExtraIPDepend = DB_Select(SPrintF('%sOrdersOwners',$ExtraIPOrder['OrderType']),$Columns,Array('UNIQ','ID'=>$ExtraIPOrder['DependOrderID']));
	switch(ValueOf($ExtraIPDepend)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('IP_DESTINATION_DELETED','Заказ к которому был прикреплен IP адрес уже удалён');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ExtraIPDepend = Array('Login'=>'','Address'=>'');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ExtraIPOrdersRead',(integer)$__USER['ID'],(integer)$ExtraIPOrder['UserID']);
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
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
#-------------------------------------------------------------------------------
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Заказ IP адреса %s',$ExtraIPOrder['Login']));
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Order/Number',$ExtraIPOrder['OrderID']);
#-------------------------------------------------------------------------------
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Номер',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$ExtraIPOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ExtraIPOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$ExtraIPOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Информация';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('IP адрес',$ExtraIPOrder['Login']);
#-------------------------------------------------------------------------------
if($ExtraIPOrder['OrderType'] == "Hosting"){
	#-------------------------------------------------------------------------------
	$OrderType = "Хостинг";
	#-------------------------------------------------------------------------------
}elseif($ExtraIPOrder['OrderType'] == "VPS"){
	#-------------------------------------------------------------------------------
	$OrderType = $ExtraIPOrder['OrderType'];
	#-------------------------------------------------------------------------------
}elseif($ExtraIPOrder['OrderType'] == "DS"){
	#-------------------------------------------------------------------------------
	$OrderType = "Выделенный сервер";
	#-------------------------------------------------------------------------------
}elseif($ExtraIPOrder['OrderType'] == "Manual"){
	#-------------------------------------------------------------------------------
	$OrderType = "Без сервисов";
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	return new gException('UNKNOWN_IP_DESTINATION','Не удалось определить с каким сервисом связан IP адрес.');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Table[] = Array('Тип заказа',$OrderType);
#-------------------------------------------------------------------------------
$Table[] = Array('Сервер',$ExtraIPDepend['Address']);
#-------------------------------------------------------------------------------
$Table[] = Array('Аккаунт',$ExtraIPDepend['Login']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$ExtraIPOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','ExtraIPOrders',$ExtraIPOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ExtraIPOrder['UserNotice'] || ($ExtraIPOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($ExtraIPOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ExtraIPOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($ExtraIPOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ExtraIPOrder['AdminNotice']));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddChild('Into',$Comp);
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Build(FALSE)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','DOM'=>$DOM->Object);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
