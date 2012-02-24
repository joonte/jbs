<?php
#-------------------------------------------------------------------------------
$NoTypesDB = &Link_Get('NoTypesDB','boolean');
#-------------------------------------------------------------------------------
$NoTypesDB = TRUE;
#-------------------------------------------------------------------------------
$HostingServers = DB_Select('HostingServers',Array('ID','Password'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    # No more...
  break;
  case 'array':
    #---------------------------------------------------------------------------
    foreach($HostingServers as $HostingServer){
      #-------------------------------------------------------------------------
      $Password = Crypt_Encode($HostingServer['Password']);
      if(Is_Error($Password))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('HostingServers',Array('Password'=>$Password),Array('ID'=>$HostingServer['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Registrators = DB_Select('Registrators',Array('ID','Password'));
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
      $Password = Crypt_Encode($Registrator['Password']);
      if(Is_Error($Password))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Registrators',Array('Password'=>$Password),Array('ID'=>$Registrator['ID']));
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