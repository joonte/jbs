<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$Email		=  (string) @$Args['Email'];
$Password	=  (string) @$Args['Password'];
$Name		=  (string) @$Args['Name'];
$ICQ		=  (string) @$Args['ICQ'];
$Protect	= (integer) @$Args['Protect'];
$Message	=  (string) @$Args['Message'];
$IsInternal	= (boolean) @$Args['IsInternal'];
$Eval		=  (string) @$Args['Eval'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/Session.class.php')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$Email = StrToLower($Email);
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Email'],$Email))
  return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
#-------------------------------------------------------------------------------
$Count = DB_Count('Users',Array('Where'=>SPrintF("`Email` = '%s'",$Email)));
if(Is_Error($Count)){
  return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if($Count)
  return new gException('USER_EXISTS','Пользователь с таким электронным адресом уже существует в системе');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Password'],$Password))
  return new gException('WRONG_PASSWORD','Неверно указан новый пароль');
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Char'],$Name))
  return new gException('WRONG_USER_NAME','Неверно указано Ваше имя');
#-------------------------------------------------------------------------------
if($ICQ){
  #-----------------------------------------------------------------------------
  if(!Preg_Match($Regulars['ICQ'],$ICQ))
    return new gException('WRONG_ICQ','Неверно указан ICQ-номер');
}
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['__USER']) && $_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR']){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Protect',$Protect);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  if(!$Comp)
    return new gException('WRONG_PROTECT_CODE','Введенный Вами защитный код неверен, либо устарел. Пожалуйста, введите его заново.');
}
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Users']['Register'];
#-------------------------------------------------------------------------------
$IUser = Array(
  #-----------------------------------------------------------------------------
  'Name'            => $Name,
  'Sign'            => SPrintF('%s %s.',$Settings['Sign'],$Name),
  'Watchword'       => Md5($Password),
  'UniqID'          => Md5(UniqID()),
  'Email'           => $Email,
  'ICQ'             => $ICQ,
  'LayPayMaxDays'   => $Settings['LayPayMaxDays'],
  'LayPayMaxSumm'   => $Settings['LayPayMaxSumm'],
  'LayPayThreshold' => $Settings['LayPayThreshold'],
);
#-------------------------------------------------------------------------------
$Group = DB_Select('Groups','ID',Array('UNIQ','Where'=>"`IsDefault` = 'yes'"));
#-------------------------------------------------------------------------------
switch(ValueOf($Group)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return new gException('DEFAULT_GROUP_NOT_FOUND','Группа по умолчанию не найдена');
  case 'array':
    $IUser['GroupID'] = $Group['ID'];
  break;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('UserRegister'))))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$UserID = DB_Insert('Users',$IUser);
if(Is_Error($UserID))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$OwnerID = 0;
#-------------------------------------------------------------------------------
if(IsSet($_COOKIE['OwnerID']))
  $OwnerID = $_COOKIE['OwnerID'];
#-------------------------------------------------------------------------------
if($OwnerID){  #-----------------------------------------------------------------------------
  $Owner = DB_Select('Users',Array('ID','GroupID','IsInheritGroup'),Array('UNIQ','ID'=>$OwnerID));
  #-----------------------------------------------------------------------------
  switch(ValueOf($Owner)){
    case 'error':
      return ERROR | @Trigger_Error(500);
    case 'exception':
      # No more...
    break;
    case 'array':
      #-------------------------------------------------------------------------
      $UUpdate = Array(
        #-----------------------------------------------------------------------
        'OwnerID'   => $OwnerID,
        'IsManaged' => IsSet($_COOKIE['IsManaged'])
      );
      #-------------------------------------------------------------------------
      if($Owner['IsInheritGroup'])
        $UUpdate['GroupID'] = $Owner['GroupID'];
      #-------------------------------------------------------------------------
      $IsUpdate = DB_Update('Users',$UUpdate,Array('ID'=>$UserID));
      if(Is_Error($IsUpdate))
        return ERROR | @Trigger_Error(500);
      #-------------------------------------------------------------------------
    break;
    default:
      return ERROR | @Trigger_Error(101);
  }
}
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Users/Init',$UserID);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/ContractMake',Array('TypeID'=>'Default'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    #---------------------------------------------------------------------------
    $ContractID = $Comp['ContractID'];
    #---------------------------------------------------------------------------
    $IsSend = NotificationManager::sendMsg(new Message('UserRegister',(integer)$UserID,Array('Password'=>$Password)));
    #---------------------------------------------------------------------------
    switch(ValueOf($IsSend)){
      case 'error':
        return ERROR | @Trigger_Error(500);
      case 'exception':
        # No more...
      case 'true':
        #-----------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> 1,
			'Text'		=> SPrintF('%s (%s)',($Message)?$Message:'Зарегистрирован новый пользователь',$Email)
		      );
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        if(Is_Error(DB_Commit($TransactionID)))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
	if($IsInternal)
		return Array('Status'=>'Ok','ID'=>$UserID);
	#-----------------------------------------------------------------------
        $SessionID = (IsSet($_COOKIE['SessionID']) && StrLen($_COOKIE['SessionID'])?$_COOKIE['SessionID']:UniqID('SESSION'));
        #-----------------------------------------------------------------------
        $Session = new Session($SessionID);
        #-----------------------------------------------------------------------
        $IsLoad = $Session->Load();
        if(Is_Error($IsLoad))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        if(!$IsLoad){
          #---------------------------------------------------------------------
          $Session->Data['UsersIDs'] = Array();
          #---------------------------------------------------------------------
          if(!SetCookie('SessionID',$SessionID,Time() + 2678400,'/'))
            return ERROR | @Trigger_Error(500);
          #---------------------------------------------------------------------
          if(!SetCookie('Email',$Email,Time() + 2678400,'/'))
            return ERROR | @Trigger_Error(500);
        }
        #-----------------------------------------------------------------------
        $IsUpdated = DB_Update('Users',Array('EnterDate'=>Time(),'EnterIP'=>$_SERVER['REMOTE_ADDR']),Array('ID'=>$UserID));
        if(Is_Error($IsUpdated))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        Array_UnShift($Session->Data['UsersIDs'],$UserID);
        #-----------------------------------------------------------------------
        if(Is_Error($Session->Save()))
          return ERROR | @Trigger_Error(500);
        #-----------------------------------------------------------------------
        return Array('Status'=>'Ok','SessionID'=>$SessionID,'Home'=>($Eval?SPrintF('/Home?Eval=%s',$Eval):'/Home'),'ContractID'=>$ContractID,'UserID'=>$UserID);
      default:
        return ERROR | @Trigger_Error(101);
    }
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
