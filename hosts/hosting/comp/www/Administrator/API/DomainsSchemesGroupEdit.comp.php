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
$DomainsSchemesGroupID = (integer) @$Args['DomainsSchemesGroupID'];
$Name                  =  (string) @$Args['Name'];
#-------------------------------------------------------------------------------
$IDomainsSchemesGroup = Array(
  #-----------------------------------------------------------------------------
  'Name' => $Name
);
#-------------------------------------------------------------------------------
$Answer = Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
if($DomainsSchemesGroupID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('DomainsSchemesGroups',$IDomainsSchemesGroup,Array('ID'=>$DomainsSchemesGroupID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $DomainsSchemesGroupID = DB_Insert('DomainsSchemesGroups',$IDomainsSchemesGroup);
  if(Is_Error($DomainsSchemesGroupID))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $Answer['DomainsSchemesGroupID'] = $DomainsSchemesGroupID;
}
#-------------------------------------------------------------------------------
return $Answer;
#-------------------------------------------------------------------------------

?>
