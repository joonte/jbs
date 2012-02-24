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
$ExtraIPBonusID     = (integer) @$Args['ExtraIPBonusID'];
$UserID         = (integer) @$Args['UserID'];
$SchemeID       = (integer) @$Args['SchemeID'];
$DaysReserved   = (integer) @$Args['DaysReserved'];
$DaysRemainded  = (integer) @$Args['DaysRemainded'];
$Discont        = (integer) @$Args['Discont'];
$Comment        =  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if($Discont < 5 || $Discont > 100)
  return new gException('WRONG_DISCOUNT','Скидка должна принимать значение от 5 до 100');
#-------------------------------------------------------------------------------
$IExtraIPBonus = Array(
  #-----------------------------------------------------------------------------
  'UserID'        => $UserID,
  'SchemeID'      => ($SchemeID?$SchemeID:NULL),
  'DaysReserved'  => $DaysReserved,
  'DaysRemainded' => $DaysRemainded,
  'Discont'       => $Discont/100,
  'Comment'       => $Comment
);
#-------------------------------------------------------------------------------
if($ExtraIPBonusID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('ExtraIPBonuses',$IExtraIPBonus,Array('ID'=>$ExtraIPBonusID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('ExtraIPBonuses',$IExtraIPBonus);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
