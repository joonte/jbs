<?php
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(IsSet($Config['Hosting']['Servers'])){
  #-----------------------------------------------------------------------------
  $Servers = $Config['Hosting']['Servers'];
  #-----------------------------------------------------------------------------
  $ServersGroupID = DB_Insert('HostingServersGroups',Array('Name'=>'Россия','Comment'=>'Россия, датацентр M9'));
  if(Is_Error($ServersGroupID))
    return ERROR | Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsUpdate = DB_Update('HostingSchemes',Array('ServersGroupID'=>$ServersGroupID));
  if(Is_Error($IsUpdate))
    return ERROR | Trigger_Error(500);
  #-----------------------------------------------------------------------------
  $IsDefault = 'yes';
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Servers) as $ServerID){
    #---------------------------------------------------------------------------
    $Server = $Servers[$ServerID];
    #---------------------------------------------------------------------------
    $Http = $Server['Http'];
    #---------------------------------------------------------------------------
    switch($Server['System']){
      case 'Cpanel':
        #-----------------------------------------------------------------------
        $Array    = Explode(':',$Http['Auth']);
        $Login    = Current($Array);
        $Password = Next($Array);
        #-----------------------------------------------------------------------
      break;
      case 'DirectAdmin':
        #-----------------------------------------------------------------------
        $Array    = Explode(':',$Http['Auth']);
        $Login    = Current($Array);
        $Password = Next($Array);
        #-----------------------------------------------------------------------
      break;
      case 'IspManager':
        #-----------------------------------------------------------------------
        $Login    = $Server['User'];
        $Password = $Server['Password'];
        #-----------------------------------------------------------------------
      break;
      case 'Plesk':
        #-----------------------------------------------------------------------
        $Login    = $Server['User'];
        $Password = $Server['Password'];
        #-----------------------------------------------------------------------
      break;
      default:
        return ERROR | Trigger_Error(101);
    }
    #---------------------------------------------------------------------------
    $NewServerID = DB_Insert('HostingServers',Array('SystemID'=>$Server['System'],'ServersGroupID'=>$ServersGroupID,'IsDefault'=>$IsDefault,'Address'=>$Http['Host'],'Port'=>$Http['Port'],'Protocol'=>$Http['Protocol'],'Url'=>$Server['CpAddress'],'Login'=>$Login,'Password'=>$Password,'IP'=>GetHostByName($Http['Host'])));
    if(Is_Error($NewServerID))
      return ERROR | Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('HostingOrders',Array('ServerID'=>$NewServerID),Array('Where'=>SPrintF("`ServerID` = '%s'",$ServerID)));
    if(Is_Error($IsUpdate))
      return ERROR | Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IsDefault = 'no';
  }
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>