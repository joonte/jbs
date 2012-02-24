<?php
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(IsSet($Config['Domains']['Registrators'])){
  #-----------------------------------------------------------------------------
  $Registrators = $Config['Domains']['Registrators'];
  #-----------------------------------------------------------------------------
  foreach(Array_Keys($Registrators) as $TypeID){
    #---------------------------------------------------------------------------
    $Type = $Registrators[$TypeID];
    #---------------------------------------------------------------------------
    if(!$Type['Login'])
      continue;
    #---------------------------------------------------------------------------
    $Http = $Type['Http'];
    #---------------------------------------------------------------------------
    $IRow = Array('Name'=>$TypeID,'TypeID'=>$TypeID,'Address'=>$Http['Address'],'Port'=>$Http['Port'],'Protocol'=>$Http['Protocol'],'Login'=>$Type['Login'],'Password'=>$Type['Password']);
    #---------------------------------------------------------------------------
    $RegistratorID = DB_Insert('Registrators',$IRow);
    if(Is_Error($RegistratorID))
      return ERROR | Trigger_Error(500);
    #---------------------------------------------------------------------------
    $IsUpdate = DB_Update('DomainsSchemes',Array('RegistratorID'=>$RegistratorID),Array('Where'=>SPrintF("`RegistratorID` = '%s'",$TypeID)));
    if(Is_Error($IsUpdate))
      return ERROR | Trigger_Error(500);
  }
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>