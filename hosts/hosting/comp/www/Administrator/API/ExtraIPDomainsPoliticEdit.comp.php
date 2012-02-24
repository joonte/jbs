<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
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
$ExtraIPDomainsPoliticID   = (integer) @$Args['ExtraIPDomainsPoliticID'];
$GroupID               = (integer) @$Args['GroupID'];
$UserID                = (integer) @$Args['UserID'];
$SchemeID              = (integer) @$Args['SchemeID'];
$DomainsSchemesGroupID = (integer) @$Args['DomainsSchemesGroupID'];
$DaysPay               = (integer) @$Args['DaysPay'];
$Discont               = (integer) @$Args['Discont'];
#-------------------------------------------------------------------------------
if($Discont < 5 || $Discont > 100)
  return new gException('WRONG_DISCOUNT','Скидка должна принимать значение от 5 до 100');
#-------------------------------------------------------------------------------
$IExtraIPDomainsPolitic = Array(
  #-----------------------------------------------------------------------------
  'GroupID'               => $GroupID,
  'UserID'                => $UserID,
  'SchemeID'              => ($SchemeID?$SchemeID:NULL),
  'DomainsSchemesGroupID' => $DomainsSchemesGroupID,
  'DaysPay'               => $DaysPay,
  'Discont'               => $Discont/100
);
#-------------------------------------------------------------------------------
if($ExtraIPDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('ExtraIPDomainsPolitics',$IExtraIPDomainsPolitic,Array('ID'=>$ExtraIPDomainsPoliticID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('ExtraIPDomainsPolitics',$IExtraIPDomainsPolitic);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
