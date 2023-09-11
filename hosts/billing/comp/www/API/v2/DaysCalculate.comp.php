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
$OrderID		= (integer) @$Args['OrderID'];
$ContractID		= (integer) @$Args['ContractID'];
$ServiceID		= (integer) @$Args['ServiceID'];
$SchemeID		= (integer) @$Args['SchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$OrderID){
	#-------------------------------------------------------------------------------
	//return new gException('ORDER_NOT_SET','Заказ не задан');
	#-------------------------------------------------------------------------------
	if(!$ServiceID)
		return new gException('SERVICE_NOT_SET','Сервис не задан');
	#-------------------------------------------------------------------------------
	if(!$ContractID)
		return new gException('CONTRACT_NOT_SET','Договор не задан');
	#-------------------------------------------------------------------------------
	if(!$SchemeID)
		return new gException('SCHEME_NOT_SET','Тариф не задан');
	#-------------------------------------------------------------------------------
	// достаём даныне сервиса
	$Service = DB_Select('ServicesOwners',Array('ID','Code','IsActive'),Array('UNIQ','ID'=>$ServiceID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		if(!$Service['IsActive'])
			return new gException('SERICE_NOT_ACTIVE','Сервис неактивен');
		#-------------------------------------------------------------------------------
		if(In_Array($Service['Code'],Array('Domain','Default')))
			return new gException('SERVCE_INCORRECT','Некорректный сервис');
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// запускаем транзакцию, делаем заказ
	if(Is_Error(DB_Transaction($TransactionID = UniqID('DaysCalculate'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Path = SPrintF('www/API/%sOrder',$Service['Code']);
	#-------------------------------------------------------------------------------
	if(Is_Error(System_Element(SPrintF('comp/%s.comp.php',$Path))))
		return new gException(SPrintF('API для заказа сервиса не найдено: %s',$Path));
	#-------------------------------------------------------------------------------
	$Array = Array(
			'ContractID'				=> $ContractID,
			SPrintF('%sSchemeID',$Service['Code'])	=> $SchemeID,
			'Comment'				=> 'Расчёт дней с балланса',
			);
	#-------------------------------------------------------------------------------
	$AddOrder = Comp_Load($Path,$Array);
	#-------------------------------------------------------------------------------
	switch(ValueOf($AddOrder)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		new gException('CANNOT_EMULATE_ORDER','Не удалось эмулировать заказ услуги');
	case 'array':
		#-------------------------------------------------------------------------------
		//$OrderID = SPrintF('%sOrderID',$Service['Code']);
		$OrderID = $AddOrder['OrderID'];
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
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
// если была траназкия - откатываем
if(IsSet($TransactionID))
	if(Is_Error(DB_Roll($TransactionID)))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('DaysFromBallance'=>$DaysFromBallance);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
