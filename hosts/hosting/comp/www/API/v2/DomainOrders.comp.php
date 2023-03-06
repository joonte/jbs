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
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список колонок которые юзеру не показываем
$Config = Config();
#-------------------------------------------------------------------------------
$Exclude = Array_Keys($Config['APIv2ExcludeColumns']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
$Columns = Array(
		'*',
		'(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`',
		'(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`)) as `Params`',
		'(SELECT `IsAutoProlong` FROM `Orders` WHERE `DomainOrdersOwners`.`OrderID` = `Orders`.`ID`) AS `IsAutoProlong`',
		'(SELECT `UserNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DomainOrdersOwners`.`OrderID`) AS `UserNotice`',
		'(SELECT `AdminNotice` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `DomainOrdersOwners`.`OrderID`) AS `AdminNotice`',
		'(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DomainOrdersOwners`.`ContractID`) AS `Customer`',
		);
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Out;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($DomainOrders as $DomainOrder){
	#-------------------------------------------------------------------------------
	UnSet($DomainOrder['Params']);
	#-------------------------------------------------------------------------------
	// выпиливаем колонки
	foreach(Array_Keys($DomainOrder) as $Column)
		if(In_Array($Column,$Exclude))
			UnSet($DomainOrder[$Column]);
	#-------------------------------------------------------------------------------
	// полное имя домена
	$DomainOrder['Domain'] = SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone']);
	#-------------------------------------------------------------------------------
	$Out[$DomainOrder['ID']] = $DomainOrder;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

