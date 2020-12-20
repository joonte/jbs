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
$ProxyOrderID = (integer) @$Args['ProxyOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'*',
			'(SELECT `Name` FROM `ProxySchemes` WHERE `ProxySchemes`.`ID` = `ProxyOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `Name` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = (SELECT `ServersGroupID` FROM `ProxySchemes` WHERE `ProxySchemes`.`ID` = `ProxyOrdersOwners`.`SchemeID`)) as `ServersGroupName`',
			'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `ServerID`',
			'(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `Params`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `ProxyOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `UserNotice`',
			'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `AdminNotice`',
			'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ProxyOrdersOwners`.`ContractID`) AS `Customer`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `OrdersOwners`.`ServiceID` = `Services`.`ID`) FROM `OrdersOwners` WHERE `ProxyOrdersOwners`.`OrderID` = `OrdersOwners`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$ProxyOrder = DB_Select('ProxyOrdersOwners',$Columns,Array('UNIQ','ID'=>$ProxyOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyOrder)){
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
$IsPermission = Permission_Check('ProxyOrdersRead',(integer)$__USER['ID'],(integer)$ProxyOrder['UserID']);
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
$DOM->AddText('Title',SPrintF('Заказ Proxy %s',$ProxyOrder['Login']));
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Order/Number',$ProxyOrder['OrderID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Номер',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$ProxyOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ProxyOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$ProxyOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',SPrintF('%s (%s)',$ProxyOrder['Scheme'],$ProxyOrder['ServersGroupName']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры доступа';
#-------------------------------------------------------------------------------
$Server = DB_Select('ServersOwners',Array('Address','Params'),Array('UNIQ','ID'=>$ProxyOrder['ServerID']));
#-------------------------------------------------------------------------------
if(!Is_Array($Server))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Form/Input',Array('type'=>'button','onclick'=>SPrintF('OrderManage(%u,%u,%u);',$ProxyOrder['ID'],$ProxyOrder['OrderID'],$ProxyOrder['ServiceID']),'value'=>'Вход'));
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Адрес подключения',SPrintF('%s:%u',$ProxyOrder['Host'],$ProxyOrder['Port']));
#-------------------------------------------------------------------------------
$Table[] = Array('Тип подключения',(($ProxyOrder['ProtocolType'] == 'https')?'HTTPs':'SOCK5'));
#-------------------------------------------------------------------------------
$Table[] = Array('Логин',$ProxyOrder['Login']);
#-------------------------------------------------------------------------------
$Table[] = Array('Пароль',$ProxyOrder['Password']);
#-------------------------------------------------------------------------------
$Table[] = Array('Отображаемый IP',$ProxyOrder['IP']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$ProxyOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','ProxyOrders',$ProxyOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ProxyOrder['UserNotice'] || ($ProxyOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($ProxyOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ProxyOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($ProxyOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ProxyOrder['AdminNotice']));
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
