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
$OrderID		=  (integer) @$Args['OrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$OrderID)
	return new gException('ORDER_NOT_SET','Заказ не задан');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UserID = $GLOBALS['__USER']['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// заказ
$Columns = Array(
		'ID','ServiceID','UserID',
		'(SELECT `Balance` FROM `Contracts` WHERE `OrdersOwners`.`ContractID` = `Contracts`.`ID`) as `ContractBalance`',
		'(SELECT `GroupID` FROM `Users` WHERE `OrdersOwners`.`UserID` = `Users`.`ID`) as `GroupID`',
		'(SELECT `Code` FROM `Services` WHERE `ID` = `OrdersOwners`.`ServiceID`) AS `Code`'
		);
#-------------------------------------------------------------------------------
$Order = DB_Select('OrdersOwners',$Columns,Array('UNIQ','ID'=>$OrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
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
$IsPermission = Permission_Check('HostingOrdersRead',(integer)$GLOBALS['__USER']['ID'],(integer)$Order['UserID']);
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
// тариф
$Columns = Array(
		'`SchemeID` AS `ID`',
		SPrintF('(SELECT `CostDay` FROM `%sSchemes` WHERE `ID` = `%sOrdersOwners`.`SchemeID`) AS `CostDay`',$Order['Code'],$Order['Code'])
		);
#-------------------------------------------------------------------------------
$Scheme = DB_Select(SPrintF('%sOrdersOwners',$Order['Code']),$Columns,Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$OrderID)));
#-------------------------------------------------------------------------------
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
if($Scheme['CostDay'] > 0){
	#-------------------------------------------------------------------------------
	$DaysFromBallance = Floor($Order['ContractBalance'] / $Scheme['CostDay']);
	#-------------------------------------------------------------------------------
	// если дней ноль - считаем что их один - так будут учитываться бонусы на 100% оплату
	$DaysFromBallance = Comp_Load('Bonuses/DaysCalculate',($DaysFromBallance)?$DaysFromBallance:1,$Scheme,$Order);
	if(Is_Error($DaysFromBallance))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$DaysFromBallance = 365;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('DaysFromBallance'=>$DaysFromBallance);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
