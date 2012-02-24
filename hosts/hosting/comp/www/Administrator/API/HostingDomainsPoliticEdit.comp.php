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
$HostingDomainsPoliticID      = (integer) @$Args['HostingDomainsPoliticID'];
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
$IHostingDomainsPolitic = Array(
  #-----------------------------------------------------------------------------
  'GroupID'               => $GroupID,
  'UserID'                => $UserID,
  'SchemeID'              => ($SchemeID?$SchemeID:NULL),
  'DomainsSchemesGroupID' => $DomainsSchemesGroupID,
  'DaysPay'               => $DaysPay,
  'Discont'               => $Discont/100
);
#-------------------------------------------------------------------------------
if($HostingDomainsPoliticID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('HostingDomainsPolitics',$IHostingDomainsPolitic,Array('ID'=>$HostingDomainsPoliticID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('HostingDomainsPolitics',$IHostingDomainsPolitic);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
