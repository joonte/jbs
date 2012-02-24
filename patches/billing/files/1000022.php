<?php
#-------------------------------------------------------------------------------
$Config = IO_Read($Path = SPrintF('%s/hosts/%s/host.ini',SYSTEM_PATH,HOST_ID));
if(Is_Error($Config))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Str_Replace('JBsServer','jbs-server',$Config);
#-------------------------------------------------------------------------------
$IsWrite = IO_Write($Path,$Config,TRUE);
if(Is_Error($IsWrite))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>