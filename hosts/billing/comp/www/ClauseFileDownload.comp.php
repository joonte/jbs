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
$ClauseFileID = (integer) @$Args['ClauseFileID'];
$IsPrintOut   = (boolean) @$Args['IsPrintOut'];
#-------------------------------------------------------------------------------
$ClauseFile = DB_Select('ClausesFiles','*',Array('UNIQ','ID'=>$ClauseFileID));
#-------------------------------------------------------------------------------
switch(ValueOf($ClauseFile)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $FileData = $ClauseFile['FileData'];
    #---------------------------------------------------------------------------
    if(!$IsPrintOut){
      #-------------------------------------------------------------------------
      $Length = Mb_StrLen($FileData,'ASCII');
      #-------------------------------------------------------------------------
      Header('Content-Type: application/octetstream; charset=utf-8');
      Header(SPrintF('Content-Length: %u',$Length));
      Header(SPrintF('Content-Disposition: attachment; filename="%s";',$ClauseFile['FileName']));
      Header('Pragma: nocache');
    }
    #---------------------------------------------------------------------------
    return $FileData;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
