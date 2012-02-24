<?php
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$NsServers = @$Config['Domains']['NsServers'];
#-------------------------------------------------------------------------------
$Registrators = DB_Select('Registrators','ID');
#-------------------------------------------------------------------------------
switch(ValueOf($Registrators)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($Registrators as $Registrator){
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Registrators',Array('Ns1Name'=>@$NsServers['Ns1']['Address'],'Ns2Name'=>@$NsServers['Ns2']['Address']),Array('ID'=>$Registrator['ID']));
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