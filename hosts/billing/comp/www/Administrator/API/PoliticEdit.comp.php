<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$PoliticID		= (integer) @$Args['PoliticID'];
$GroupID		= (integer) @$Args['GroupID'];
$UserID			= (integer) @$Args['UserID'];
$ExpirationDate		= (integer) @$Args['ExpirationDate'];
$FromServiceID		= (integer) @$Args['FromServiceID'];
$FromSchemeID		= (integer) @$Args['SchemeID'];
$FromSchemesGroupID	= (integer) @$Args['FromSchemesGroupID'];
$ToServiceID		= (integer) @$Args['ToServiceID'];
$ToSchemeID		= (integer) @$Args['ToSchemeID'];
$ToSchemesGroupID	= (integer) @$Args['ToSchemesGroupID'];
$DaysPay		= (integer) @$Args['DaysPay'];
$DaysDiscont		= (integer) @$Args['DaysDiscont'];
$Discont		= (integer) @$Args['Discont'];
$Comment		=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$FromServiceID && !$FromSchemesGroupID)
	return new gException('WRONG_SERVICE_OR_GROUP','Выберите сервис или группу, для которой назначается политика');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($FromServiceID && $FromSchemesGroupID)
	return new gException('WRONG_SERVICE_AND_GROUP','Политика назначается либо на сервис либо на группу');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ToServiceID && !$ToSchemesGroupID)
	return new gException('WRONG_SERVICE_OR_GROUP','Выберите сервис или группу, для которой назначается политика');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ToServiceID && $ToSchemesGroupID)
	return new gException('WRONG_SERVICE_AND_GROUP','Политика назначается либо на сервис либо на группу');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ExpirationDate < Time())
	return new gException('WRONG_EXPIRATION_DATE','Дата окончания действия находится в прошлом');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($DaysDiscont < 0)
	return new gException('WRONG_DAYS_DICONT','Неверное число дней скидки');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Discont < 5 || $Discont > 100)
	return new gException('WRONG_DISCOUNT','Скидка должна принимать значение от 5 до 100');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IPolitic = Array(
	#-----------------------------------------------------------------------------
	'GroupID'		=> $GroupID,
	'UserID'		=> $UserID,
	'ExpirationDate'	=> $ExpirationDate,
	'FromServiceID'		=> ($FromServiceID?$FromServiceID:NULL),
	'FromSchemeID'		=> ($FromSchemeID?$FromSchemeID:NULL),
	'FromSchemesGroupID'	=> ($FromSchemesGroupID?$FromSchemesGroupID:NULL),
	'ToServiceID'		=> ($ToServiceID?$ToServiceID:NULL),
	'ToSchemeID'		=> ($ToSchemeID?$ToSchemeID:NULL),
	'ToSchemesGroupID'	=> ($ToSchemesGroupID?$ToSchemesGroupID:NULL),
	'DaysPay'		=> $DaysPay,
	'DaysDiscont'		=> $DaysDiscont,
	'Discont'		=> $Discont/100,
	'Comment'		=> $Comment
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($PoliticID){
	#-----------------------------------------------------------------------------
	$IsUpdate = DB_Update('Politics',$IPolitic,Array('ID'=>$PoliticID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
}else{
	#-----------------------------------------------------------------------------
	$IsInsert = DB_Insert('Politics',$IPolitic);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
