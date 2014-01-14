<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$Email   =  (string) @$Args['Email'];
$Protect = (integer) @$Args['Protect'];
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Email'], $Email))
  return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
#-----------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','IsProtected'),Array('UNIQ','Where'=>SPrintF("`Email` = '%s'",StrToLower($Email))));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('USER_NOT_FOUND','Пользователь не зарегистрирован в системе');
  case 'array':
    #---------------------------------------------------------------------------
    if($User['IsProtected'])
      return new gException('PASSWORD_RESTORE_DISABLED_FOR_USER','Для данного пользователя запрещена функция восстановления пароля');
    #---------------------------------------------------------------------------
    $Comp = Comp_Load('Protect',$Protect);
    if(Is_Error($Comp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!$Comp)
      return new gException('WRONG_PROTECT_CODE','Введенный Вами защитный код неверен, либо устарел. Пожалуйста, введите его заново.');
    #---------------------------------------------------------------------------
    $Password = SubStr(Md5(UniqID(Rand(), true)), 0, 8);
    #---------------------------------------------------------------------------
    $IsUpdated = DB_Update('Users',Array('Watchword'=>Md5($Password)),Array('ID'=>$User['ID']));
    if(Is_Error($IsUpdated))
      return ERROR | @Trigger_Error(500);
    #-------------------------------------------------------------------------------
    $Tmp = System_Element('tmp');
    if(Is_Error($Tmp))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    #-----------------------------------------------------------------------------
    # часть JBS-757 - грохаем старые сессии юзера
    $Path = SPrintF('%s/sessions',$Tmp);
    #---------------------------------------------------------------------------
    if(File_Exists($Path)){
      #-------------------------------------------------------------------------
      $SessionIDsIDs = IO_Scan($Path);
      if(Is_Error($SessionIDsIDs))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
      foreach($SessionIDsIDs as $SessionID){
        #-----------------------------------------------------------------------
        if(Preg_Match(SPrintF('/^(REMEBMER|SESSION)%s/',MD5($User['ID'])),$SessionID)){
          #-------------------------------------------------------------------
          if(!@UnLink(SPrintF('%s/%s',$Path,$SessionID)))
            return ERROR | @Trigger_Error(500);
        }
      }
    }
    #-----------------------------------------------------------------------------
    #---------------------------------------------------------------------------
    $IsSend = NotificationManager::sendMsg(new Message('UserPasswordRestore',(integer)$User['ID'],Array('Password'=>$Password,'ChargeFree'=>TRUE,'IsImmediately'=>TRUE)));
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSend)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        return ERROR | @Trigger_Error(400);
      case 'true':
        return Array('Status'=>'Ok');
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
