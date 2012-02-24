<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/

if(Is_Error(System_Load('libs/Upload.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DocCount = 10;
#-------------------------------------------------------------------------------
while ($DocCount > 0) {
  #-------------------------------------------------------------------------------
  $Files = DB_Select('Profiles',Array('ID','Document'),Array('Where'=>"`Document` IS NOT NULL",'Limits'=>Array(0,50)));
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
        if(!SaveUploadedFile('Profiles', $File['ID'], $File['Document']))
          Debug("[patches/billing/files/1000056]: cannot save file " . $File['ID']);
        #-------------------------------------------------------------------------
        $Erase = DB_Query("UPDATE Profiles SET `Document` = NULL WHERE ID = " . $File['ID']);
        if(Is_Error($Erase))
          return ERROR | @Trigger_Error('101');
      }
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
  #---------------------------------------------------------------------------
  $DocCount = DB_Count('Profiles',Array('Where'=>"`Document` IS NOT NULL"));
  if(Is_Error($DocCount))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
