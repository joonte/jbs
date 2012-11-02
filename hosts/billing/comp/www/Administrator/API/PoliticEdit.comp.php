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
$PoliticID	= (integer) @$Args['PoliticID'];
$GroupID	= (integer) @$Args['GroupID'];
$UserID		= (integer) @$Args['UserID'];
$ExpirationDate	= (integer) @$Args['ExpirationDate'];
$FromServiceID	= (integer) @$Args['FromServiceID'];
$FromSchemeID	= (integer) @$Args['SchemeID'];
$ToServiceID	= (integer) @$Args['ToServiceID'];
$ToSchemeID	= (integer) @$Args['ToSchemeID'];
$DaysPay	= (integer) @$Args['DaysPay'];
$Discont	= (integer) @$Args['Discont'];
$Comment	=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if($Discont < 5 || $Discont > 100)
  return new gException('WRONG_DISCOUNT','Скидка должна принимать значение от 5 до 100');
#-------------------------------------------------------------------------------
$IPolitic = Array(
	#-----------------------------------------------------------------------------
	'GroupID'	=> $GroupID,
	'UserID'	=> $UserID,
	'ExpirationDate'=> $ExpirationDate,
	'FromServiceID'	=> ($FromServiceID?$FromServiceID:NULL),
	'FromSchemeID'	=> ($FromSchemeID?$FromSchemeID:NULL),
	'ToServiceID'	=> ($ToServiceID?$ToServiceID:NULL),
	'ToSchemeID'	=> ($ToSchemeID?$ToSchemeID:NULL),
	'DaysPay'	=> $DaysPay,
	'Discont'	=> $Discont/100,
	'Comment'	=> $Comment
);
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
