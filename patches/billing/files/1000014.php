<?php
#-------------------------------------------------------------------------------
$NoTypesDB = &Link_Get('NoTypesDB','boolean');
#-------------------------------------------------------------------------------
$NoTypesDB = TRUE;
#-------------------------------------------------------------------------------
$MotionDocuments = DB_Select('MotionDocuments',Array('ID','Document'));
#-------------------------------------------------------------------------------
switch(ValueOf($MotionDocuments)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($MotionDocuments as $MotionDocument){
      #-------------------------------------------------------------------------
      $Document = $MotionDocument['Document'];
      #-------------------------------------------------------------------------
      if(!$Document)
        continue;
      #-------------------------------------------------------------------------
      $Document = @GzUnCompress($Document);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('MotionDocuments',Array('Link'=>$Document),Array('ID'=>$MotionDocument['ID']));
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