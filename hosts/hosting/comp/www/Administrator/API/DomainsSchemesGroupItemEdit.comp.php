<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$DomainsSchemesGroupItemID = (integer) @$Args['DomainsSchemesGroupItemID'];
$DomainsSchemesGroupID     = (integer) @$Args['DomainsSchemesGroupID'];
$SchemeID                  = (integer) @$Args['SchemeID'];
#-------------------------------------------------------------------------------
$IDomainsSchemesGroup = Array(
  #-----------------------------------------------------------------------------
  'SchemeID' => $SchemeID
);
#-------------------------------------------------------------------------------
if($DomainsSchemesGroupItemID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('DomainsSchemesGroupsItems',$IDomainsSchemesGroup,Array('ID'=>$DomainsSchemesGroupItemID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
}else{
  #-----------------------------------------------------------------------------
  $IDomainsSchemesGroup['DomainsSchemesGroupID'] = $DomainsSchemesGroupID;
  #-----------------------------------------------------------------------------
  $DomainsSchemesGroupID = DB_Insert('DomainsSchemesGroupsItems',$IDomainsSchemesGroup);
  if(Is_Error($DomainsSchemesGroupID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer['DomainsSchemesGroupID'] = $DomainsSchemesGroupID;
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
