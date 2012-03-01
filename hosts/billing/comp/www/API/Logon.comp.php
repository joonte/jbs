<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$Email      =  (string) @$Args['Email'];
$Password   =  (string) @$Args['Password'];
$IsRemember = (boolean) @$Args['IsRemember'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Session.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Email'],$Email))
  return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Password'],$Password))
  return new gException('WRONG_PASSWORD','Недопустимый пароль');
#-------------------------------------------------------------------------------
$Users = DB_Select('Users',Array('ID','Name','Email','Watchword','UniqID','IsActive','EnterDate'),Array('SortOn'=>'ID','Where'=>SPrintF("Email = '%s'",StrToLower($Email))));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('USER_NOT_FOUND','Пользователь не зарегистрирован в системе');
  case 'array':
    #---------------------------------------------------------------------------
    $User = Current($Users);
    #---------------------------------------------------------------------------
    if(!$User['IsActive'])
      return new gException('USER_UNACTIVE','Пользователь отключен');
    #---------------------------------------------------------------------------
    if($User['Watchword'] != Md5($Password) && $User['Watchword'] != Sha1($Password)){
      #-------------------------------------------------------------------------
      $UniqID = $User['UniqID'];
      #-------------------------------------------------------------------------
      if($UniqID == 'no' || $UniqID != $Password)
        return new gException('PASSWORD_NOT_MATCHED','Введен неверный пароль');
    }
    #---------------------------------------------------------------------------
    if(Time() - $User['EnterDate'] > 86400){
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Users',Array('UniqID'=>Md5(UniqID('ID'))),Array('ID'=>$User['ID']));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
    }
    #---------------------------------------------------------------------------
    $UserID = $User['ID'];
    #---------------------------------------------------------------------------
    $SessionID = UniqID(SPrintF('%s%s',$IsRemember?'REMEBMER':'SESSION',MD5($UserID)));
    #---------------------------------------------------------------------------
    $Session = new Session($SessionID);
    #---------------------------------------------------------------------------
    $Session->Data['UsersIDs'] = Array($UserID);
    $Session->Data['RootID'] = $UserID;
    #---------------------------------------------------------------------------
    if(Is_Error($Session->Save()))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!SetCookie('SessionID',$SessionID,Time() + ($IsRemember?2678400:86400),'/'))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $_COOKIE['SessionID'] = $SessionID;
    #---------------------------------------------------------------------------
    $User = Comp_Load('Users/Init',$User['ID']);
    if(Is_Error($User))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    if(!SetCookie('Email',$User['Email'],Time() + 31536000,'/'))
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    $Event = Array(
			'UserID'	=> $User['ID'],
			'Text'		=> SPrintF('Пользователь вошел в систему с IP-адреса (%s)',$_SERVER['REMOTE_ADDR'])
    		  );
    $Event = Comp_Load('Events/EventInsert',$Event);
    if(!$Event)
      return ERROR | @Trigger_Error(500);
    #---------------------------------------------------------------------------
    return Array('Status'=>'Ok','SessionID'=>$SessionID,'User'=>$User,'Home'=>SPrintF('/%s/Home',$User['InterfaceID']));
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
