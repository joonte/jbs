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
$DNSmanagerOrderID = (integer) @$Args['DNSmanagerOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `DNSmanagerSchemes` WHERE `DNSmanagerSchemes`.`ID` = `DNSmanagerOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `ServerID`',
			'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `Params`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DNSmanagerOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `UserNotice`',
			'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DNSmanagerOrdersOwners`.`OrderID`) AS `AdminNotice`',
			'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DNSmanagerOrdersOwners`.`ContractID`) AS `Customer`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID`) FROM `OrdersOwners` WHERE `DNSmanagerOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$DNSmanagerOrder = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('UNIQ','ID'=>$DNSmanagerOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrder)){
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
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('DNSmanagerOrdersRead',(integer)$__USER['ID'],(integer)$DNSmanagerOrder['UserID']);
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
$DOM = new DOM();
#-------------------------------------------------------------------------------
$Links = &Links();
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Заказ DNSmanager %s',$DNSmanagerOrder['Login']));
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Order/Number',$DNSmanagerOrder['OrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Номер',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$DNSmanagerOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$DNSmanagerOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$DNSmanagerOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',SPrintF('%s (%s)',$DNSmanagerOrder['Scheme'],$DNSmanagerOrder['ServersGroupName']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Server = DB_Select('ServersOwners',Array('Address','Params'),Array('UNIQ','ID'=>$DNSmanagerOrder['ServerID']));
#-------------------------------------------------------------------------------
if(!Is_Array($Server))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>SPrintF('OrderManage(%u,%u);',$DNSmanagerOrder['ID'],$DNSmanagerOrder['ServiceID']),'value'=>'Вход'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Div = new Tag('DIV',new Tag('SPAN',Array('class'=>'Standard'),$Server['Params']['Url']),$Comp);
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес панели управления',$Div);
#-------------------------------------------------------------------------------
$Table[] = Array('Логин',$DNSmanagerOrder['Login']);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль',$DNSmanagerOrder['Password']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$DNSmanagerOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','DNSmanagerOrders',$DNSmanagerOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DNSmanagerOrder['UserNotice'] || ($DNSmanagerOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($DNSmanagerOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$DNSmanagerOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($DNSmanagerOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$DNSmanagerOrder['AdminNotice']));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Tables/Standard',$Table);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
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
