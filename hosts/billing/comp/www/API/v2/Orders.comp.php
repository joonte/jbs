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
// все колонки + Services.Params под именем AjaxCall
$Columns = Array(
		'*',
		'(SELECT `Params` FROM `Services` WHERE `ID` = `OrdersOwners`.`ServiceID`) AS `AjaxCall`',
		'(SELECT `Code` FROM `Services` WHERE `ID` = `OrdersOwners`.`ServiceID`) AS `Code`',
		);
$Orders = DB_Select('OrdersOwners',$Columns,Array('Where'=>SPrintF("`UserID` = %u",$GLOBALS['__USER']['ID']),'SortOn'=>Array('ServiceID','ID')));
#-------------------------------------------------------------------------------
switch(ValueOf($Orders)){
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
foreach($Orders as $Order){
	#-------------------------------------------------------------------------------
	// выпиливаем колонки
	foreach(Array_Keys($Order) as $Column)
		if(In_Array($Column,$Exclude))
			UnSet($Order[$Column]);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// достаём поля кастомных услуг
	$OrdersFields = DB_Select('OrdersFieldsOwners',Array('*'),Array('Where'=>Array(SPrintF("`UserID` = %u",$GLOBALS['__USER']['ID']),SPrintF('`OrderID` = %u',$Order['ID'])),'SortOn'=>Array('ID')));
	#-------------------------------------------------------------------------------
	switch(ValueOf($OrdersFields)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		$OrdersFields = Array();
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Order['OrdersFields'] = $OrdersFields;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// достаём тариф
	$Comp = Comp_Load('Services/Orders/SchemeWrapper',$Order['Code'],$Order['ID'],TRUE);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Order['SchemeName'] = $Comp;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// для доменов, надо поправить в выхлопе DaysRemainded
	if($Order['Code'] == 'Domain'){
		#-------------------------------------------------------------------------------
		// дата окончания
		$ExpirationDate = Max($Order['ExpirationDate'],Time());
		#-------------------------------------------------------------------------------
		$Order['DaysRemainded'] = Ceil(($ExpirationDate-Time())/86400);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	/* смысла наверное в этом нет....
	// скармливаем Tags, проверяем выхлоп
	$Options = Comp_Load('Services/Orders/TagsExplain',$Order['AjaxCall']['Tags']);
	if(Is_Error($Options))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Order['DependOrders'] = $Options['Orders'];
	#-------------------------------------------------------------------------------
	*/
	// убираем поле
	UnSet($Order['AjaxCall']);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Out[$Order['ID']] = $Order;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

