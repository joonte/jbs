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
$TicketID		= (integer) @$Args['TicketID'];
$Message		=  (string) @$Args['Message'];
$Flags			=  (string) @$Args['Flags'];
$OpenTicketUserID	= (integer) @$Args['OpenTicketUserID'];
$UserID			= (integer) @$Args['UserID'];
$MaxID			= (integer) @$Args['MaxID'];
$IsInternal		= (boolean) @$Args['IsInternal'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Message = Trim($Message);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// усекаем сообщение на 32k символов
$Message = Mb_SubStr(Mb_Convert_Encoding($Message,'UTF-8'),0,32000);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['Edesks'];
#-------------------------------------------------------------------------------
if(IsSet($__USER['IsEmulate']) && $__USER['ID'] != $OpenTicketUserID)
	return new gException('DENY_WRITE_MESSAGE_FROM_ANOTHER_USER','Нельзя писать сообщения от имени другого пользователя');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// для админов, не внтренний постинг - те. через веб интерфейс. всё что внутри с флажком - не проверяем
if($__USER['IsAdmin'] && !$IsInternal){
	#-------------------------------------------------------------------------------
	if(!IsSet($GLOBALS['IsCron'])){
		#-------------------------------------------------------------------------------
		$Columns = Array(
				'MAX(`ID`) AS `MaxMessageID`',
				SPrintF('(SELECT `UserID` FROM `EdesksMessagesOwners` WHERE `EdeskID` = %u ORDER BY `ID` DESC LIMIT 1) AS `UserID`',$TicketID)
				);
		#-------------------------------------------------------------------------------
		$MaxMessageID = DB_Select('EdesksMessagesOwners',$Columns,Array('UNIQ','Where'=>SPrintF('`EdeskID` = %u',$TicketID)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($MaxMessageID)){
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
		#Debug(SPrintF('[comp/www/API/TicketMessageEdit]: MaxMessageID = %s; UserID = %s; __USER = %s',$MaxMessageID['MaxMessageID'],$MaxMessageID['UserID'],$__USER['ID']));
		if($MaxID != $MaxMessageID['MaxMessageID'] && $MaxMessageID['UserID'] != $__USER['ID'])
			return new gException('TICKET_HAVE_NEW_MESSAGES','С момента открытия, в тикет были добавлены новые сообщения. Скопируйте сообщение, откройте тикет заново, вставьте сообщение. Если были добавлены аттачменты - не забудте снова их добавить ');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# truncate $Theme & $Message
$Message	= Mb_SubStr($Message, 0, 62000);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsClose = ($Flags == "Closed" || $Flags == "DenyAddMessage")?TRUE:FALSE;
#-------------------------------------------------------------------------------
if($Flags == 'NotVisible'){
	#-------------------------------------------------------------------------------
	$Flags = 'No';
	#-------------------------------------------------------------------------------
	$NotVisible = TRUE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if(StrLen($Flags) < 2)
	$Flags = 'No';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(($Settings['DenyFoulLanguage']['IsActive'] && !IsSet($NotVisible) && IsSet($_SERVER["REMOTE_PORT"])) || ($Settings['DenyFoulLanguage']['IsEmailActive'] && !IsSet($_SERVER["REMOTE_PORT"]))){
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
$Ticket = DB_Select('Edesks',Array('ID','UserID','TargetUserID','Theme','StatusID','StatusDate','Flags'),Array('UNIQ','ID'=>$TicketID));
#-------------------------------------------------------------------------------
switch(ValueOf($Ticket)){
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
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('TicketEdit',(integer)$__USER['ID'],(integer)$Ticket['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
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
#-------------------------------------------------------------------------------
# реализация JBS-965 - запрещаем писать в старые тикеты юзерам
if($Settings['EdeskOldDays'] && !$__USER['IsAdmin'] && !IsSet($GLOBALS['IsCron']))
	if($Ticket['StatusDate'] < Time() - IntVal($Settings['EdeskOldDays']) * 24 * 3600)
		return new gException('EDESK_TOO_OLD',SPrintF('Вы пишете в слишком старый тикет (%u дн.), скопируйте ваше сообщение и создайте новый тикет',Ceil((Time() - $Ticket['StatusDate']))/(24*3600)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Flags == "No" && !$Message)
	return new gException('MESSAGE_IS_EMPTY','Введите сообщение');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Ticket['UserID'] == $__USER['ID'] && $Ticket['Flags'] == "DenyAddMessage")
	return new gException('DENY_ADD_MESSAGE','Тема содержит очень большое количество сообщений. У сотрудников технической поддержки возникают затруднения с перечитыванием истории переписки. Пожалуйста, опишите вашу проблему и создайте новый запрос.');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# реализация JBS-1093 - ограничение на максимальное число сообщений в тикете
$Count = DB_Count('EdesksMessages',Array('Where'=>SPrintF('`EdeskID` = %d', $Ticket['ID'])));
if(Is_Error($Count))
	return ERROR | Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count + 1 > IntVal($Settings['MaxMessages']))
	return new gException('TOO_MANY_MESSAGES',SPrintF('Тема содержит слишком большое количество сообщений. Максимальное число сообщений в одном тикете равно %u. Пожалуйста, опишите вашу проблему и создайте новый запрос.',IntVal($Settings['MaxMessages'])));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Message){
	#-------------------------------------------------------------------------------
	$ITicketMessage = Array(
				'UserID'	=> ($UserID > 0 && $__USER['IsAdmin'])?$UserID:$__USER['ID'],
				'EdeskID'	=> $Ticket['ID'],
				'Content'	=> $Message,
				);
	#-------------------------------------------------------------------------------
	if(IsSet($NotVisible))
		$ITicketMessage['IsVisible'] = FALSE;
	#-------------------------------------------------------------------------------
	$MessageID = DB_Insert('EdesksMessages',$ITicketMessage);
	if(Is_Error($MessageID))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// логгируем IP и UA пользователя
	$Comp = Comp_Load('Users/LogIP',($UserID > 0 && $__USER['IsAdmin'])?$UserID:$__USER['ID'],IsSet($GLOBALS['_SERVER']['REMOTE_ADDR'])?$GLOBALS['_SERVER']['REMOTE_ADDR']:'127.0.0.1',IsSet($GLOBALS['_SERVER']['HTTP_USER_AGENT'])?$GLOBALS['_SERVER']['HTTP_USER_AGENT']:'',$MessageID);
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
	if($__USER['ID'] != (integer)$Ticket['UserID']){
		#-------------------------------------------------------------------------------
		$Count = DB_Count('EdesksMessages',Array('Where'=>SPrintF('`EdeskID` = %d AND `UserID` != %u', $Ticket['ID'], (integer)$Ticket['UserID'])));
		if(Is_Error($Count))
			return ERROR | Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// First message from support and ticket hasn't assigned to somebody yet.
		if($Count == 1 && $Ticket['TargetUserID'] == 100){
			#-------------------------------------------------------------------------------
			$IsUpdate = DB_Update('Edesks',Array('UpdateDate' => Time(),'TargetUserID'=>(integer)$__USER['ID']), Array('ID'=>$Ticket['ID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if($__USER['ID'] != (integer)$Ticket['UserID']){
		#-------------------------------------------------------------------------------
		$u_array = Array('UpdateDate'=>Time(),'Flags'=>$Flags);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$u_array = Array('UpdateDate'=>Time());
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Edesks',$u_array,Array('ID'=>$Ticket['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> ($UserID > 0)?$UserID:$__USER['ID'],
			'PriorityID'	=> 'Billing',
			'Text'		=> SPrintF('Добавлено новое сообщение к запросу в службу поддержки с темой (%s)',$Ticket['Theme'])
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	// если это админ, настроено назначать на последнего ответившего, и это не системный юзер - то назначаем на ответившего
	if($__USER['IsAdmin'] && $Settings['AutoAssignToLastAnswerEmployee'] && $__USER['ID'] != 100){
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Edesks',Array('UpdateDate'=>Time(),'TargetUserID'=>(integer)$__USER['ID']),Array('ID'=>$Ticket['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	// no message, but, may be need change flag?
	if ($__USER['ID'] != (integer)$Ticket['UserID']) {
		#-------------------------------------------------------------------------------
		$u_array = Array('UpdateDate'=>Time(),'Flags'=>$Flags);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$u_array = Array('UpdateDate'=>Time());
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Edesks',$u_array,Array('ID'=>$Ticket['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$StatusID = $Ticket['StatusID'];
#-------------------------------------------------------------------------------
$StatusID = ($IsClose?'Closed':($Ticket['UserID'] != $__USER['ID']?'Opened':($StatusID != 'Newest'?'Working':'Newest')));
#-------------------------------------------------------------------------------
if($StatusID != $Ticket['StatusID']){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','StatusID'=>$StatusID,'IsNotNotify'=>TRUE,'RowsIDs'=>$Ticket['ID']));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		#return ERROR | @Trigger_Error(400);
		return $Comp;
	case 'array':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-641: load task
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
