<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/

if(Is_Error(System_Load('libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Files = DB_Select('EdesksMessages',Array('ID','FileData'),Array('Where'=>"`FileData` IS NOT NULL"));
#-------------------------------------------------------------------------------
switch(ValueOf($Files)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Files as $File){
      #-------------------------------------------------------------------------
      if(!SaveUploadedFile('EdesksMessages', $File['ID'], $File['FileData']))
        Debug("[patches/billing/files/1000055]: cannot save file " . $File['ID']);
      #-------------------------------------------------------------------------
      $Erase = DB_Query("UPDATE EdesksMessages SET `FileData` = NULL WHERE ID = " . $File['ID']);
      if(Is_Error($Erase))
        return ERROR | @Trigger_Error('101');
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
