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
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$ISPswOrderID = (integer) @$Args['ISPswOrderID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
			'ID','OrderID','UserID','OrderDate','ContractID','LicenseID','StatusID','StatusDate','IP','DependOrderID',
			'(SELECT `Name` FROM `ISPswSchemes` WHERE `ISPswSchemes`.`ID` = `ISPswOrdersOwners`.`SchemeID`) as `Scheme`',
			'(SELECT `elid` FROM `ISPswLicenses` WHERE `ISPswOrdersOwners`.`LicenseID`=`ISPswLicenses`.`ID`) AS `elid`',
			'(SELECT `IP` FROM `ISPswLicenses` WHERE `ISPswOrdersOwners`.`LicenseID`=`ISPswLicenses`.`ID`) AS `LicenseIP`',
			'(SELECT `pricelist_id` FROM `ISPswLicenses` WHERE `ISPswOrdersOwners`.`LicenseID`=`ISPswLicenses`.`ID`) AS `pricelist_id`',
			'(SELECT `IsAutoProlong` FROM `Orders` WHERE `ISPswOrdersOwners`.`OrderID`=`Orders`.`ID`) AS `IsAutoProlong`',
			'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ISPswOrdersOwners`.`OrderID`) AS `UserNotice`',
			'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ISPswOrdersOwners`.`OrderID`) AS `AdminNotice`',
			'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ISPswOrdersOwners`.`ContractID`) AS `Customer`',
			'(SELECT (SELECT `Code` FROM `Services` WHERE `Orders`.`ServiceID` = `Services`.`ID`) FROM `Orders` WHERE `ISPswOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',$Columns,Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
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
$IsPermission = Permission_Check('ISPswOrdersRead',(integer)$__USER['ID'],(integer)$ISPswOrder['UserID']);
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
#-------------------------------------------------------------------------------
# Коллекция ссылок
$Links['DOM'] = &$DOM;
#-------------------------------------------------------------------------------
if(Is_Error($DOM->Load('Window')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$ISPswOrder['OrderID']);
if(Is_Error($Number))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DOM->AddText('Title',SPrintF('Заказ ПО ISPsystem #%s/%s',$Number,$ISPswOrder['IP']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table = Array('Общая информация');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Номер',$Number);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Date/Extended',$ISPswOrder['OrderDate']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Дата заказа',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Contract/Number',$ISPswOrder['ContractID']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/String',SPrintF('%s / %s',$Comp,$ISPswOrder['Customer']),35);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Договор',new Tag('TD',Array('class'=>'Standard'),$Comp));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('Тарифный план',$ISPswOrder['Scheme']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ISPswOrder['DependOrderID']){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Services/Orders/SelectDependOrder',$ISPswOrder['UserID'],$ISPswOrder['OrderID'],$ISPswOrder['DependOrderID'],TRUE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Относится к заказу', $Comp);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = 'Параметры лицензии';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Table[] = Array('IP лицензии',$ISPswOrder['IP']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!Is_Null($ISPswOrder['LicenseID'])){
	#-------------------------------------------------------------------------------
	$Table[] = Array('Внутренний идентификатор',$ISPswOrder['LicenseID']);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Номер лицензии ISPsystem (elid)',$ISPswOrder['elid']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Pattern = ($ISPswOrder['pricelist_id'] > 1000)?'http://%s:1500/%s':'http://%s/manager/%s';
	# TODO: надо по типу панели определять окончание панели управления
	$Mgr = 'ispmgr';
	#-------------------------------------------------------------------------------
	$Url = SPrintF($Pattern,Long2IP(IP2Long($ISPswOrder['LicenseIP'])),$Mgr);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/String',$Url,35,$Url);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Table[] = Array('Панель управления',new Tag('TD',Array('class'=>'Standard'),$Comp));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Table[] = 'Прочее';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Formats/Logic',$ISPswOrder['IsAutoProlong']);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table[] = Array('Автопродление',$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Statuses/State','ISPswOrders',$ISPswOrder);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Table = Array_Merge($Table,$Comp);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ISPswOrder['UserNotice'] || ($ISPswOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])){
	#-------------------------------------------------------------------------------
	$Table[] = 'Примечания к заказу';
	#-------------------------------------------------------------------------------
	if($ISPswOrder['UserNotice'])
		$Table[] = Array('Примечание',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ISPswOrder['UserNotice']));
	#-------------------------------------------------------------------------------
	if($ISPswOrder['AdminNotice'] && $GLOBALS['__USER']['IsAdmin'])
		$Table[] = Array('Примечание администратора',new Tag('PRE',Array('class'=>'Standard','style'=>'width:260px; overflow:hidden;'),$ISPswOrder['AdminNotice']));
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
