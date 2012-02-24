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
$DomainBonusID         = (integer) @$Args['DomainBonusID'];
$UserID                = (integer) @$Args['UserID'];
$SchemeID              = (integer) @$Args['SchemeID'];
$DomainsSchemesGroupID = (integer) @$Args['DomainsSchemesGroupID'];
$YearsReserved         = (integer) @$Args['YearsReserved'];
$YearsRemainded        = (integer) @$Args['YearsRemainded'];
$Discont               = (integer) @$Args['Discont'];
$Comment               =  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if($Discont < 5 || $Discont > 100)
  return new gException('WRONG_DISCOUNT','Скидка должна принимать значение от 5 до 100');
#-------------------------------------------------------------------------------
$IDomainBonus = Array(
  #-----------------------------------------------------------------------------
  'UserID'                => $UserID,
  'SchemeID'              => ($SchemeID?$SchemeID:NULL),
  'DomainsSchemesGroupID' => ($DomainsSchemesGroupID?$DomainsSchemesGroupID:NULL),
  'YearsReserved'         => $YearsReserved,
  'YearsRemainded'        => $YearsRemainded,
  'Discont'               => $Discont/100,
  'Comment'               => $Comment
);
#-------------------------------------------------------------------------------
if($DomainBonusID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('DomainsBonuses',$IDomainBonus,Array('ID'=>$DomainBonusID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('DomainsBonuses',$IDomainBonus);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
