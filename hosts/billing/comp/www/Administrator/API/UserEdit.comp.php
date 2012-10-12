<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Args = Args();
#-------------------------------------------------------------------------------
$UserID          = (integer) @$Args['UserID'];
$Name            =  (string) @$Args['Name'];
$Email           =  (string) @$Args['Email'];
$Password        =  (string) @$Args['Password'];
$GroupID         = (integer) @$Args['GroupID'];
$OwnerID         = (integer) @$Args['OwnerID'];
$IsManaged       = (boolean) @$Args['IsManaged'];
$IsInheritGroup  = (boolean) @$Args['IsInheritGroup'];
$LayPayMaxDays   = (integer) @$Args['LayPayMaxDays'];
$LayPayMaxSumm   =  (double) @$Args['LayPayMaxSumm'];
$LayPayThreshold =  (double) @$Args['LayPayThreshold'];
$Rating          =  (double) @$Args['Rating'];
$IsActive        = (boolean) @$Args['IsActive'];
$IsNotifies      = (boolean) @$Args['IsNotifies'];
$IsHidden        = (boolean) @$Args['IsHidden'];
$IsProtected     = (boolean) @$Args['IsProtected'];
$AdminNotice     =  (string) @$Args['AdminNotice'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Name = Trim($Name);
#-------------------------------------------------------------------------------
if(StrLen($Name) < 2)
  return new gException('TOO_SHORT_NAME','Имя пользователя не может быть короче двух символов');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
$Email = StrToLower($Email);
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['Email'],$Email))
  return new gException('WRONG_EMAIL','Неверно указан электронный адрес');
#-------------------------------------------------------------------------------
$User = DB_Select('Users',Array('ID','Email'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#---------------------------------------------------------------------------
	if($User['Email'] != $Email){
	#-------------------------------------------------------------------------
	$Count = DB_Count('Users',Array('Where'=>SPrintF("`Email` = '%s'",$Email)));
	if(Is_Error($Count)){
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------
	if($Count)
		return new gException('USER_EXISTS','Пользователь с таким электронным адресом уже существует в системе');
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Password = Trim($Password);
#-------------------------------------------------------------------------------
if(StrLen($Password) < 8 && $Password != 'Default')
  return new gException('TOO_SHORT_PASSWORD','Пароль не может быть короче 8 символов');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$GroupID));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('GROUP_NOT_FOUND','Группа пользователя не найдена');
#-------------------------------------------------------------------------------
$Count = DB_Count('Users',Array('ID'=>$OwnerID));
if(Is_Error($Count))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
  return new gException('OWNER_NOT_FOUND','Владелец не найден');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IUser = Array(
  #-----------------------------------------------------------------------------
  'Name'            => $Name,
  'Email'           => $Email,
  'GroupID'         => $GroupID,
  'OwnerID'         => $OwnerID,
  'IsManaged'       => $IsManaged,
  'IsInheritGroup'  => $IsInheritGroup,
  'LayPayMaxDays'   => $LayPayMaxDays,
  'LayPayMaxSumm'   => $LayPayMaxSumm,
  'LayPayThreshold' => $LayPayThreshold,
  'Rating'          => $Rating,
  'IsActive'        => $IsActive,
  'IsNotifies'      => $IsNotifies,
  'IsHidden'        => $IsHidden,
  'IsProtected'     => $IsProtected,
  'AdminNotice'     => $AdminNotice
);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($User['Email'] != $Email){
	# ставим мыл как неподтверждённый
	$IUser['EmailConfirmed'] = 0;
	# добавляем событие - смена мыла
	$Event = Array(
			'UserID'        => $UserID,
			'PriorityID'    => 'Billing',
			'Text'          => SPrintF('Сотрудником (%s, #%u) изменён почтовый адрес пользователя с (%s) на (%s)',$GLOBALS['__USER']['Name'],$GLOBALS['__USER']['ID'],$User['Email'],$Email)
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Password != 'Default')
  $IUser['Watchword'] = Md5($Password);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('Users',$IUser,Array('ID'=>$UserID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
