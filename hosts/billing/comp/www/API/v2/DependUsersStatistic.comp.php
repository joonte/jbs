<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Percent = $Config['Tasks']['Types']['CaclulatePartnersReward']['PartnersRewardPercent'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// выбираем список рефералов
$Referals = DB_Select('Users',Array('ID'),Array('Where'=>'`OwnerID` = @local.__USER_ID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Referals)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	// нет рефералов
	return $Out;
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	// есть рефералы
	$TableName = SPrintF('InvoicesOwners%s',UniqID($GLOBALS['__USER']['ID']));
	#-------------------------------------------------------------------------------
	$Array = Array();
	#-------------------------------------------------------------------------------
	foreach($Referals as $Referal)
		$Array[] = (integer)$Referal['ID'];
	#-------------------------------------------------------------------------------
	$ReferalsIDs = Implode(',',$Array);
	#-------------------------------------------------------------------------------
	$Result = DB_Query(SPrintF("CREATE TEMPORARY TABLE `%s` SELECT * FROM `InvoicesOwners` WHERE `StatusID`='Payed' AND `UserID` IN (%s);",$TableName,$ReferalsIDs));
	if(Is_Error($Result))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Columns = Array(
			"FROM_UNIXTIME(`StatusDate`,'%Y') AS `Year`",
			"FROM_UNIXTIME(`StatusDate`,'%m') AS `Month`",
			'COUNT(DISTINCT(`UserID`)) AS `NumUsers`',
			'COUNT(*) AS `NumPayments`',
			SPrintF('ROUND(SUM(`Summ`) * %u / 100, 2) AS `MonthSum`',$Percent),
			);
	#-------------------------------------------------------------------------------
	$Payments = DB_Select($TableName,$Columns,Array('GroupBy'=>Array('Year','Month')));
	switch(ValueOf($Payments)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#-------------------------------------------------------------------------------
		// нет платежей от рефералов
		$Out['Message'] = 'Ваши рефералы не оплачивали никаких услуг.';
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	case 'array':
		#-------------------------------------------------------------------------------
		foreach($Payments as $Payment)
			$Out[] = $Payment;
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
