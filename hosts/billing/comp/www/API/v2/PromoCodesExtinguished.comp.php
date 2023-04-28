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
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
		'*',
		'(SELECT `Code` FROM `PromoCodes` WHERE `ID` = `PromoCodesExtinguishedOwners`.`PromoCodeID`) AS `PromoCode`',
		'(SELECT `SchemesGroupID` FROM `PromoCodes` WHERE `ID` = `PromoCodesExtinguishedOwners`.`PromoCodeID`) AS `SchemesGroupID`',
		'(SELECT `SchemeID` FROM `PromoCodes` WHERE `ID` = `PromoCodesExtinguishedOwners`.`PromoCodeID`) AS `SchemeID`',
		'(SELECT `ServiceID` FROM `PromoCodes` WHERE `ID` = `PromoCodesExtinguishedOwners`.`PromoCodeID`) AS `ServiceID`',
		);
$PromoCodes = DB_Select('PromoCodesExtinguishedOwners',$Columns,Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($PromoCodes)){
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
foreach($PromoCodes as $PromoCode){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/Administrator/API/SchemesGroupItemInfo',$PromoCode['ServiceID'],$PromoCode['SchemeID'],100,$PromoCode['SchemesGroupID']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$PromoCode['ApplyTo'] = $Comp;
	#-------------------------------------------------------------------------------
	$Out[$PromoCode['ID']] = $PromoCode;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
