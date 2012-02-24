<?php
#-------------------------------------------------------------------------------
$Enclosures = DB_Select('ContractsEnclosures','*');
#-------------------------------------------------------------------------------
switch(ValueOf($Enclosures)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Enclosures as $Enclosure){
      #-------------------------------------------------------------------------
      if(@GzUnCompress($Enclosure['Document']) !== FALSE)
        continue;
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('ContractsEnclosures',Array('Document'=>GzCompress($Enclosure['Document'])),Array('ID'=>$Enclosure['ID']));
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