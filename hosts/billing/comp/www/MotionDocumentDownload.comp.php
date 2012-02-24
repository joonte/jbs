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
$MotionDocumentID = (integer) @$Args['MotionDocumentID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/HTMLDoc.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$MotionDocument = DB_Select('MotionDocuments','AjaxCall',Array('UNIQ','ID'=>$MotionDocumentID));
#-------------------------------------------------------------------------------
switch(ValueOf($MotionDocument)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DOCUMENT_NOT_FOUND','Документ не найден');
  case 'array':
    #---------------------------------------------------------------------------
    $AjaxCall = $MotionDocument['AjaxCall'];
    #---------------------------------------------------------------------------
    $IsPermission = Permission_Check('/Administrator/',(integer)$GLOBALS['__USER']['ID']);
    #---------------------------------------------------------------------------
    switch(ValueOf($IsPermission)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'false':
        $AjaxCall['Args']['IsStamp'] = 1;
      break;
      case 'true':
        $AjaxCall['Args']['IsStamp'] = 0;
      break;
      default:
        return ERROR | @Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','AjaxCall'=>$AjaxCall);
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
