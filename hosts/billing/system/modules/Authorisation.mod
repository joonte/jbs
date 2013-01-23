<?php
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['__USER']) || IsSet($Args['ReOpen'])){
  #-----------------------------------------------------------------------------
  if(IsSet($Args['Email']) && IsSet($Args['Password'])){
    #---------------------------------------------------------------------------
    $Logon = Comp_Load('www/API/Logon',Array('Email'=>(string)$Args['Email'],'Password'=>(string)$Args['Password'],'IsRemember'=>(boolean)@$Args['IsRemember']));
    #---------------------------------------------------------------------------
    switch(ValueOf($Logon)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        #-----------------------------------------------------------------------
        $Comp = Comp_Load('www/Logon',$Logon);
        if(Is_Error($Comp))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        Exit(Is_Array($Comp)?JSON_Encode($Comp):$Comp);
      case 'array':
        #-----------------------------------------------------------------------
        $IsUpdated = DB_Update('Users',Array('EnterDate'=>Time(),'EnterIP'=>$_SERVER['REMOTE_ADDR']),Array('ID'=>$GLOBALS['__USER']['ID']));
        if(Is_Error($IsUpdated))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        #return TRUE;
	break;
      default:
        return ERROR | @Trigger_Error(101);
    }
  }
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('www/Logon');
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  Exit(Is_Array($Comp)?JSON_Encode($Comp):$Comp);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-31: проверяем, не системный ли это юзер пришёл...
if($GLOBALS['__USER']['ID'] == 300){
  if(SubStr($GLOBALS['__URI'],0,5) != '/API/'){
    if(SubStr($GLOBALS['__URI'],0,27) != '/Administrator/API/SelectDB'){
      Debug('[system/modules/Authorisation.mod]: Этот пользователь не имеет прав на такие запросы, он предназначен для работы через API.');
      exit;
    }
  }
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
?>
