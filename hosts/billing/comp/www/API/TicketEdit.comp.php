<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Theme         =  (string) @$Args['Theme'];
$TargetGroupID = (integer) @$Args['TargetGroupID'];
$TargetUserID  = (integer) @$Args['TargetUserID'];
$PriorityID    =  (string) @$Args['PriorityID'];
$Message       =  (string) @$Args['Message'];
$UserID        = (integer) @$Args['UserID'];
$Flags         =  (string) @$Args['Flags'];
$NotifyEmail   =  (string) @$Args['NotifyEmail'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// усекаем сообщение на 32k символов
$Theme	= Mb_SubStr(Mb_Convert_Encoding($Theme,'UTF-8'), 0, 127);
$Message= Mb_SubStr(Mb_Convert_Encoding($Message,'UTF-8'),0,32000);
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(!$Theme)
	return new gException('THEME_IS_EMPTY','Введите тему запроса');
#-------------------------------------------------------------------------------
if(Mb_StrLen(Count_Chars($Theme,3)) < $Config['Interface']['Edesks']['ThemeMinimumLength'])
	return new gException('THEME_IS_TOO_SHORT','Некорректная тема запроса');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Priorities = $Config['Edesks']['Priorities'];
#-------------------------------------------------------------------------------
if(!In_Array($PriorityID,Array_Keys($Priorities)))
	return new gException('WRONG_PRIORITY','Неверный приоритет запроса');
#-------------------------------------------------------------------------------
if(!$Message)
	return new gException('MESSAGE_IS_EMPTY','Введите сообщение запроса');
#-------------------------------------------------------------------------------
$Count = DB_Count('Groups',Array('ID'=>$TargetGroupID));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Count)
	return new gException('DEPARTAMENT_NOT_FOUND','Отдел запроса не найден');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['Edesks']['DenyFoulLanguage'];
#-------------------------------------------------------------------------------
if(($Settings['IsActive'] && IsSet($_SERVER["REMOTE_PORT"])) || ($Settings['IsEmailActive'] && !IsSet($_SERVER["REMOTE_PORT"]))){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Edesk/Message/CheckFoul',$Theme);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		return new gException('FoulLanguageDetected',SPrintF('В теме сообщения содержится нецензурное слово: %s',$Comp['Word']));
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/Edesk/Message/CheckFoul',$Message);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		return new gException('FoulLanguageDetected',SPrintF('В тексте сообщения содержится нецензурное слово: %s',$Comp['Word']));
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ITicket = Array(
		'TargetGroupID'	=> $TargetGroupID,
		'PriorityID'	=> $PriorityID,
		'Theme'		=> $Theme,
		'UpdateDate'	=> Time(),
		'Flags'		=> $Flags,
		);
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
if($UserID){
	#-------------------------------------------------------------------------------
	$User = DB_Select('Users','ID',Array('UNIQ','ID'=>$UserID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($User)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('USER_NOT_FOUND','Пользователь не найден');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Permission = Permission_Check('UsersRead',(integer)$__USER['ID'],(integer)$User['ID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Permission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$ITicket['UserID'] = $User['ID'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ITicket['UserID'] = $__USER['ID'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('TicketEdit'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($TargetUserID){
	#-------------------------------------------------------------------------------
	$User = DB_Select('Users','ID',Array('UNIQ','ID'=>$TargetUserID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($User)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('WORKER_NOT_FOUND','Сотрудник не найден');
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Permission = Permission_Check('UsersRead',(integer)$__USER['ID'],(integer)$User['ID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Permission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		break;  
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$ITicket['TargetUserID'] = $User['ID'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$ITicket['TargetUserID'] = ($UserID?$__USER['ID']:100);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($NotifyEmail))
	$ITicket['NotifyEmail'] = $NotifyEmail;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$TicketID = DB_Insert('Edesks',$ITicket);
if(Is_Error($TicketID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','IsNotNotify'=>TRUE,'IsNoTrigger'=>TRUE,'StatusID'=>($UserID?'Opened':'Newest'),'RowsIDs'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$ITicketMessage = Array(
			'UserID'	=> $__USER['ID'],
			'EdeskID'	=> $TicketID,
			'Content'	=> $Message,
			);
#-------------------------------------------------------------------------------
$MessageID = DB_Insert('EdesksMessages',$ITicketMessage);
if(Is_Error($MessageID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// логгируем IP и UA пользователя
$Comp = Comp_Load('Users/LogIP',$__USER['ID'],IsSet($GLOBALS['_SERVER']['REMOTE_ADDR'])?$GLOBALS['_SERVER']['REMOTE_ADDR']:'127.0.0.1',IsSet($GLOBALS['_SERVER']['HTTP_USER_AGENT'])?$GLOBALS['_SERVER']['HTTP_USER_AGENT']:'',$MessageID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём файлы, если они есть
$Files = Upload_Get('TicketMessageFile',(IsSet($Args['TicketMessageFile'])?$Args['TicketMessageFile']:FALSE));
#-------------------------------------------------------------------------------
switch(ValueOf($Files)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	// сохраняем файлы в таблицу
	if(Is_Error(SaveUploadedFile($Files,'EdesksMessages',$MessageID)))
		return new gException('CANNOT_SAVE_UPLOADED_FILES','Не удалось сохранить загруженные файлы');
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$UserID){
	#-------------------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> $__USER['ID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Создан запрос в службу поддержки с темой (%s)',$Theme)
			);
	#-------------------------------------------------------------------------------
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# JBS-641: generate messages
if($Config['Tasks']['Types']['TicketsMessages']['IsImmediately']){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Tasks/TicketsMessages');
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
