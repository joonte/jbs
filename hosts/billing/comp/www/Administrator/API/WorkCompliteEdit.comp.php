<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$WorkCompliteID = (integer) @$Args['WorkCompliteID'];
$ContractID     = (integer) @$Args['ContractID'];
$Month          = (integer) @$Args['Month'];
$ServiceID      = (integer) @$Args['ServiceID'];
$Comment        =  (string) @$Args['Comment'];
$Amount         = (integer) @$Args['Amount'];
$Cost           =  (double) @$Args['Cost'];
$Discont        =  (double) @$Args['Discont'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Count = DB_Count('Contracts',Array('ID'=>$ContractID));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('CONTRACT_NOT_FOUND','Договор клиента не найден');
#-------------------------------------------------------------------------------
$IWorkComplite = Array(
  #-----------------------------------------------------------------------------
  'ContractID' => $ContractID,
  'Month'      => $Month,
  'ServiceID'  => $ServiceID,
  'Comment'    => $Comment,
  'Amount'     => $Amount,
  'Cost'       => $Cost,
  'Discont'    => $Discont/100
);
#-------------------------------------------------------------------------------
if($WorkCompliteID){
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('WorksComplite',$IWorkComplite,Array('ID'=>$WorkCompliteID));
  if(Is_Error($IsUpdate))
    return ERROR | @Trigger_Error(500);
}else{
  #-----------------------------------------------------------------------------
  $IsInsert = DB_Insert('WorksComplite',$IWorkComplite);
  if(Is_Error($IsInsert))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
