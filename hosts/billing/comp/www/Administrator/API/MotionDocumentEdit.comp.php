<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Null($Args)){
  #-----------------------------------------------------------------------------
  if(Is_Error(System_Load('modules/Authorisation.mod')))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$TypeID     =  (string) @$Args['TypeID'];
$ContractID = (integer) @$Args['ContractID'];
$AjaxCall   =   (array) @$Args['AjaxCall'];
$UniqID     =  (string) @$Args['UniqID'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Types = $Config['MotionDocuments']['Types'];
#-------------------------------------------------------------------------------
if(!IsSet($Types[$TypeID]))
  return new gException('WRONG_TYPE_ID','Неверный тип документа');
#---------------------------------TRANSACTION-----------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('MotionDocumentEdit'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IMotionDocument = Array(
  #-----------------------------------------------------------------------------
  'TypeID'     => $TypeID,
  'ContractID' => $ContractID,
  'AjaxCall'   => $AjaxCall,
  'UniqID'     => $UniqID
);
#-------------------------------------------------------------------------------
$MotionDocumentID = DB_Insert('MotionDocuments',$IMotionDocument);
if(Is_Error($MotionDocumentID))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'MotionDocuments','StatusID'=>'Waiting','RowsIDs'=>$MotionDocumentID));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    if(Is_Error(DB_Commit($TransactionID)))
      return ERROR | @Trigger_Error(500);
    #-----------------------------END TRANSACTION-------------------------------
    return Array('ID'=>$MotionDocumentID);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
