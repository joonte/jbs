<?php
#-------------------------------------------------------------------------------
$NoTypesDB = &Link_Get('NoTypesDB','boolean');
#-------------------------------------------------------------------------------
$NoTypesDB = TRUE;
#-------------------------------------------------------------------------------
$ClausesFiles = DB_Select('ClausesFiles',Array('ID','FileData'));
#-------------------------------------------------------------------------------
switch(ValueOf($ClausesFiles)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($ClausesFiles as $ClauseFile){
      #-------------------------------------------------------------------------
      $FileData = $ClauseFile['FileData'];
      #-------------------------------------------------------------------------
      $FileData = GzCompress($FileData);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('ClausesFiles',Array('FileData'=>$FileData),Array('ID'=>$ClauseFile['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>