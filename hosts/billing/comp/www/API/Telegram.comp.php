<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$Secret		= (string) @$Args['Secret'];	// секретный ключ
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php','libs/Server.php','libs/Telegram.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByTemplate('Telegram');
#-------------------------------------------------------------------------------
switch(ValueOf($Settings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NO_TELEGRAM_SERVERS','Отсуствует настроенный сервер Telegram');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем секретный ключ, убеждаемся что сообщение пришло от телеграмма
if(!$Secret || $Secret != $Settings['Params']['Secret'])
	return new gException('SECRET_KEY_NOT_MATCH','Секретный ключ не совпадает с ключом из настроек');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Telegram = new Telegram($Settings['Params']['Token'],$Settings['Params']['Secret']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// телега на вебхук шлёт данные постом, json. читаем их.
$Data = Json_Decode(File_Get_Contents('php://input'));
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/Telegram]: Data = %s',print_r($Data,true)));
#-------------------------------------------------------------------------------
if(!IsSet($Data->{'message'})){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/Telegram]: нет сообщения'));
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Data = $Data->{'message'};
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём сообщение или примечание, если оно есть
if(IsSet($Data->{'text'})){
	#-------------------------------------------------------------------------------
	$Message = $Data->{'text'};
	#-------------------------------------------------------------------------------
}elseif(IsSet($Data->{'caption'})){
	#-------------------------------------------------------------------------------
	$Message = $Data->{'caption'};
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Message = 'сообщение без текста';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ChatID = IntVal($Data->{'chat'}->{'id'}); // вернет ID отправителя
#-------------------------------------------------------------------------------
if(!$ChatID)
	return new gException('NO_CHAT_ID','Не удалось определить отправителя сообщения');
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/Telegram]: входящее сообщение от ChatID = %u',$ChatID));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если в сообщении написано /start - выводим подсказку
if($Message == '/start'){
	#-------------------------------------------------------------------------------
	// TODO надо проверить есть ли такой телеграмм у кого-то. если есть - другое сообщение надо слать - справку или рассказ что и почему
	if(!$Telegram->MessageSend($ChatID,SPrintF($Settings['Params']['StartMessage'],$Settings['Params']['BotName'])))
		return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки стартового сообщения на сервер Telegram');
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// возможно это - ответ на сообщение. тогда проверяем идентфикатор на который ответ
if(IsSet($Data->{'reply_to_message'}->{'message_id'})){
	#-------------------------------------------------------------------------------
	//$Message = SPrintF("%s\n\n[hidden]Data = %s[/hidden]",Trim($Message),print_r($Data,true));
	#-------------------------------------------------------------------------------
	$Message = SPrintF("%s\n\n[hidden]posted via Telegram, chat_id = %s[/hidden]",Trim($Message),$ChatID);
	#-------------------------------------------------------------------------------
	$ReplyToID = $Data->{'reply_to_message'}->{'message_id'};
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/Telegram]: ответ на сообщение = %u',$ReplyToID));
	#-------------------------------------------------------------------------------
	// ищем тикет
	$Attribs = $Telegram->FindThreadID($ReplyToID);
	#-------------------------------------------------------------------------------
	// проверяем соответствие телеграммовского идентфикатора и нашего
	if($Attribs && $Attribs['TicketID'] != 0){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/API/Telegram]: reply_to_message->message_id = %u; UserID = %s; TicketID = %s; MessageID = %u',$ReplyToID,$Attribs['UserID'],$Attribs['TicketID'],$Attribs['MessageID']));
		#-------------------------------------------------------------------------------
		// проверяем наличие такого треда
		$Count = DB_Count('Edesks',Array('ID'=>$Attribs['TicketID']));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// TODO проверить по сообщению - может тикеты объединены (хотя такого функционала пока нет), сообщение есть а тикета старого нет
		/********************************************************
		// проверяем наличие такого тикета
		$Columns = Array('*','(SELECT `UserID` FROM `Edesks` WHERE `EdesksMessagesOwners`.`EdeskID` = `Edesks`.`ID`) AS `EdeskUserID`');
		#-------------------------------------------------------------------------------
		$Edesk = DB_Select('EdesksMessagesOwners',$Columns,Array('UNIQ','ID'=>$MessageID));
		switch(ValueOf($Edesk)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/Telegram]: НЕ найдено: EdeskID = %s',$Edesk['EdeskID']));
			#-------------------------------------------------------------------------------
			if(!$Telegram->MessageSend($ChatID,$Settings['Params']['EdeskNotFound']))
				return new gException('ERROR_SEND_EdeskNotFound_MESSAGE','Ошибка отправки сообщения о не найденном тикете');
			#-------------------------------------------------------------------------------
			return Array('Status'=>'Ok');
			#-------------------------------------------------------------------------------
		case 'array':
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/Telegram]: найдено: EdeskID = %s',$Edesk['EdeskID']));
			#-------------------------------------------------------------------------------
		***********************************************************/
		// треда нет, шлём соответствующее сообщение
		if(!$Count){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/Telegram]: тикет НЕ найден: TicketID = %s',$Attribs['TicketID']));
			#-------------------------------------------------------------------------------
			// TODO надо создать новый тикет
			if($TgMessageIDs = $Telegram->MessageSend($ChatID,$Settings['Params']['EdeskNotFound'])){
				#-------------------------------------------------------------------------------
				// сохраняем сооветствие отправленного сообщения и кому оно ушло
				if(Is_Array($TgMessageIDs))
					foreach($TgMessageIDs as $TgMessageID)
						if(!$Telegram->SaveThreadID($Attribs['UserID'],0,0,$TgMessageID))
							return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				// выходим
				return Array('Status'=>'Ok');
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				return new gException('ERROR_SEND_EdeskNotFound_MESSAGE','Ошибка отправки сообщения о не найденном тикете');
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/Telegram]: тикет найден: UserID = %s; TicketID = %s',$Attribs['UserID'],$Attribs['TicketID']));
			#-------------------------------------------------------------------------------
			$UserID = $Attribs['UserID'];
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// проверяем существование юзера. может удалён уже
		$Count = DB_Count('Users',Array('ID'=>$Attribs['UserID']));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		if(!$Count){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/Telegram]: пользователь с идентфикатором (%s) не существует',$Attribs['UserID']));
			#-------------------------------------------------------------------------------
			// сообщаем об этом
			$Telegram->MessageSend($ChatID,'Пользователь от имени которого вы отправляете сообщение не существует');
				return new gException('ERROR_SEND_USER_NOT_FOUND_MESSAGE','Ошибка отправки сообщения о не найденном пользователе');
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// выходим
			return Array('Status'=>'Ok');
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/API/Telegram]: инициализируем пользователя (%s)',$UserID));
		// инициализируюем юзера, иначе статус не проставится
		$Init = Comp_Load('Users/Init',IsSet($UserID)?$UserID:100);
		if(Is_Error($Init))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// проверяем приложенные файлы и фотки $Data->{'text'}
		if(IsSet($Data->{'photo'})){
			#-------------------------------------------------------------------------------
			$Count = SizeOf($Data->{'photo'});
			#-------------------------------------------------------------------------------
			$FileID = $Data->{'photo'}[($Count - 1)]->{'file_id'};
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		if(IsSet($Data->{'document'})){
			$FileID   = $Data->{'document'}->{'file_id'};
			$FileName = $Data->{'document'}->{'file_name'};
		}
		#-------------------------------------------------------------------------------
		if(IsSet($FileID)){
			#-------------------------------------------------------------------------------
			// какой-то файл есть
			if($FileData = $Telegram->GetFile($FileID)){
				#-------------------------------------------------------------------------------
				// говорим что не интерактивно, чтоб размер не проверяло
				$GLOBALS['IsCron'] = TRUE;
				#-------------------------------------------------------------------------------
				// прописываем имя, если оно есть
				if(IsSet($FileName))
					$FileData['name'] = $FileName;
				#-------------------------------------------------------------------------------
				$_FILES = Array('Upload'=>$FileData);
				#-------------------------------------------------------------------------------
				global $_FILES;
				#-------------------------------------------------------------------------------
				$Hash = Comp_Load('www/API/Upload');
				if(Is_Error($Hash))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// постим сообщение в существующий тред
		#-------------------------------------------------------------------------------
		// снимаем флаг у треда
		$IsUpdate = DB_Update('Edesks',Array('Flags'=>'No'),Array('ID'=>$Attribs['TicketID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// цитируем предыдущее сообщение, если это не от администратора
		if(!$GLOBALS['__USER']['IsAdmin'] && IsSet($Data->{'reply_to_message'}->{'text'}))
			$Message = SPrintF('[quote]%s[/quote]%s',$Data->{'reply_to_message'}->{'text'},($Message)?$Message:'');
		#-------------------------------------------------------------------------------
		// текста может и не быть, даже от админа
		if(!$Message)
			$Message = 'текст сообщения отсуствует';
		#-------------------------------------------------------------------------------
		$Params = Array('Message'=>$Message,'TicketID'=>$Attribs['TicketID'],'UserID'=>$UserID,'IsInternal'=>TRUE);
		#-------------------------------------------------------------------------------
		if(IsSet($Hash))
			$Params['TicketMessageFile'] = Array($Hash);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// статус для тикета
		$StatusID = ($GLOBALS['__USER']['IsAdmin'])?'Opened':'Working';
		#-------------------------------------------------------------------------------
		// постим от админа, т.к. пост может идти от другого юзера в ответ на...
		$GLOBALS['__USER']['ID']	= 100;
		$GLOBALS['__USER']['IsAdmin']	= TRUE;
		$GLOBALS['__USER']['IsNoLogIP']	= 'Telegram';
		#-------------------------------------------------------------------------------
		$IsAdd = Comp_Load('www/API/TicketMessageEdit',$Params);
		if(Is_Error($IsAdd))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ставим статус, так как постили по факту от админа, статус не встаёт
		$IsStatus = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','IsNotNotify'=>TRUE,'IsNoTrigger'=>TRUE,'StatusID'=>$StatusID,'Comment'=>'Сообщение через Телеграмм','RowsIDs'=>$Attribs['TicketID']));
		if(Is_Error($IsAdd))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$GLOBALS['__USER']['ID']        = 100;
		$GLOBALS['__USER']['IsAdmin']   = FALSE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// не найдено соответсвтие идентификатора в телеграмме и номера сообщения в тикетнцие
		Debug(SPrintF('[comp/www/API/Telegram]: НЕ найдено соответствие сообщения в телеграмм и тикета: Data->reply_to_message->message_id = %s',$Data->{'reply_to_message'}->{'message_id'}));
		#-------------------------------------------------------------------------------
		if(!$Telegram->MessageSend($ChatID,$Settings['Params']['EdeskNotFound']))
			return new gException('ERROR_SEND_EdeskNotFound_MESSAGE','Ошибка отправки сообщения о не найденном тикете');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	// это не ответ на сообщение, возможно это код подтверждения
	#-------------------------------------------------------------------------------
	// разбираем текст на слова, слова проверяем как код подтверждения
	$Words = Explode(" ",$Message);
	$wCount = 0;
	#-------------------------------------------------------------------------------
	foreach($Words as $Word){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/www/API/Telegram]: Word = %s',$Word));
		#-------------------------------------------------------------------------------
		// удаляем дефисы
		$Word = Preg_Replace('/\D/','',$Word);
		#-------------------------------------------------------------------------------
		if(StrLen($Word) < 6 || StrLen($Word) > 10)
			continue;
		#-------------------------------------------------------------------------------
		// если это число - ищем по базе
		if(IntVal($Word) != 0){
			#-------------------------------------------------------------------------------
			// могут быть ведущие нули в коде
			$Count = DB_Count('Contacts',Array('Where'=>SPrintF('`Confirmation` = "%s"',$Word)));
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($Count){
				#-------------------------------------------------------------------------------
				$Contact = DB_Select('Contacts',Array('UserID','Address','ExternalID'),Array('UNIQ','Where'=>SPrintF('`Confirmation` = "%s"',$Word)));
				if(!Is_Array($Contact))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				// код найден в базе, проставляем что контакт подтверждён
				$IsUpdated = DB_Update('Contacts',Array('Confirmed'=>Time(),'Confirmation'=>'','ExternalID'=>$ChatID,'IsActive'=>TRUE),Array('Where'=>SPrintF('`Confirmation` = "%s"',$Word)));
				if(Is_Error($IsUpdated))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Event = Array('UserID'=>$Contact['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Контактный адрес (%s/%s) подтверждён через "%s"',$Contact['Address'],$ChatID,$Config['Notifies']['Methods']['Telegram']['Name']));
				#-------------------------------------------------------------------------------
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				// шлём сообщение о успешной активации
				if(!$Telegram->MessageSend($ChatID,$Settings['Params']['ConfirmSuccess']))
					return new gException('ERROR_SEND_SUCCESS_ACTIVATE_MESSAGE','Ошибка отправки сообщения о успешной активации на сервер Telegram');
				#-------------------------------------------------------------------------------
				// шлём сообщение со справочной информацией
				if(!$Telegram->MessageSend($ChatID,$Settings['Params']['StubMessage']))
					return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки сообщения-затычки на сервер Telegram');
				#-------------------------------------------------------------------------------
				// вываливаемся из скрипта, вообще
				return Array('Status'=>'Ok');
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// всё перебирать бессмысленно, достаточно первого десятка слов
		if($wCount > 9)
			break;
		#-------------------------------------------------------------------------------
		$wCount++;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// сюда мы попали если ничего не найдено
if(!$Telegram->MessageSend($ChatID,SPrintF($Settings['Params']['StartMessage'],$Settings['Params']['BotName'])))
	return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки стартового сообщения на сервер Telegram');
#-------------------------------------------------------------------------------
if(!$Telegram->MessageSend($ChatID,$Settings['Params']['StubMessage']))
	return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки сообщения-затычки на сервер Telegram');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
