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
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php','libs/Server.php','libs/VK.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByTemplate('VKontakte');
#-------------------------------------------------------------------------------
switch(ValueOf($Settings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NO_VKONTAKTE_SERVERS','Отсуствует настроенный сервер ВКонтакте');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// VK на вебхук шлёт данные постом, json. читаем их.
$Data = Json_Decode(File_Get_Contents('php://input'));
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/www/API/VK]: Data = %s',print_r($Data,true)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// сравниваем ключи
if($Settings['Params']['Secret'] != $Data->{'secret'}){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/VK]: Присланный секретный ключ (%s) не совпадает с ключом из настроек (%s)',$Data->{'group_id'},$Settings['Params']['Secret']));
	#-------------------------------------------------------------------------------
	return new gException('SECRET_KEY_NOT_MATCH','Секретный ключ не совпадает с ключом из настроек');
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Type = $Data->{'type'};
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это какая-то ненужная информация - выходим
if(!In_Array($Type,Array('confirmation','message_new'))){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/VK]: нет необходимости что-то делать, type = %s',$Type));
	#-------------------------------------------------------------------------------
	return 'ok';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// если это проверка, шлём подтверждение
if($Type == 'confirmation'){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/VK]: подтверждение группы %s',$Data->{'group_id'}));
	#-------------------------------------------------------------------------------
	return $Settings['Params']['Confirmation'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VK = new VK($Settings['Params']['Token']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// далее везде юзается $Data->{'object'}, поэтому сокращаем
$Data = $Data->{'object'};
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VkUserID = IntVal($Data->{'from_id'}); // вернет ID отправителя
#-------------------------------------------------------------------------------
if(!$VkUserID)
        return new gException('NO_USER_ID','Не удалось определить отправителя сообщения');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём сообщение или примечание, если оно есть
if(IsSet($Data->{'body'}) && $Data->{'body'}){
	#-------------------------------------------------------------------------------
	$Message = $Data->{'body'};
	#-------------------------------------------------------------------------------
}elseif(IsSet($Data->{'text'}) && $Data->{'text'}){
	#-------------------------------------------------------------------------------
	// ответ на сообщение
	$Message = $Data->{'text'};
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Message = 'сообщение без текста';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// ищем в базе - есть ли такой отправитель в контактах хоть у кого-то
$Count = DB_Count('Contacts',Array('Where'=>SPrintF('`ExternalID` = %u',$VkUserID)));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// если отправитель есть - тихий режим отключаем, этот юзер тока с ботом общается
if($Count)
	$Settings['Params']['IsSilent'] = FALSE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// возможно это - ответ на сообщение. тогда проверяем идентфикатор на который ответ
if(IsSet($Data->{'reply_message'}->{'id'})){
	#-------------------------------------------------------------------------------
	//$Message = SPrintF("%s\n\n[hidden]Data = %s[/hidden]",Trim($Message),print_r($Data,true));
	#-------------------------------------------------------------------------------
	$Message = SPrintF("%s\n\n[hidden]posted via VKontakte, user_id = %s[/hidden]",Trim($Message),$VkUserID);
	#-------------------------------------------------------------------------------
	$ReplyToID = $Data->{'reply_message'}->{'id'};
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/www/API/VK]: ответ на сообщение = %u',$ReplyToID));
	#-------------------------------------------------------------------------------
	// ищем тикет
	$Attribs = $VK->FindThreadID($ReplyToID);
	#-------------------------------------------------------------------------------
	// проверяем соответствие телеграммовского идентфикатора и нашего
	if($Attribs && $Attribs['TicketID'] != 0){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/API/VK]: reply_message->message_id = %u; UserID = %s; TicketID = %s; MessageID = %u',$ReplyToID,$Attribs['UserID'],$Attribs['TicketID'],$Attribs['MessageID']));
		#-------------------------------------------------------------------------------
		// проверяем наличие такого треда
		$Count = DB_Count('Edesks',Array('ID'=>$Attribs['TicketID']));
		if(Is_Error($Count))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		// треда нет, шлём соответствующее сообщение
		if(!$Count){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/VK]: тикет НЕ найден: TicketID = %s',$Attribs['TicketID']));
			#-------------------------------------------------------------------------------
			// в тихом режиме - просто уходим
			if($Settings['Params']['IsSilent'])
				return 'ok';
			#-------------------------------------------------------------------------------
			// TODO надо создать новый тикет
			if($VkMessageIDs = $VK->MessageSend($VkUserID,$Settings['Params']['EdeskNotFound'])){
				#-------------------------------------------------------------------------------
				// сохраняем сооветствие отправленного сообщения и кому оно ушло
				if(Is_Array($VkMessageIDs))
					foreach($VkMessageIDs as $VkMessageID)
						if(!$VK->SaveThreadID($Attribs['UserID'],0,0,$VkMessageID))
							return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				// выходим
				return 'ok';
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				return new gException('ERROR_SEND_EdeskNotFound_MESSAGE','Ошибка отправки сообщения о не найденном тикете');
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/VK]: тикет найден: UserID = %s; TicketID = %s',$Attribs['UserID'],$Attribs['TicketID']));
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
			Debug(SPrintF('[comp/www/API/VK]: пользователь с идентфикатором (%s) не существует',$Attribs['UserID']));
			#-------------------------------------------------------------------------------
                        if(!$Settings['Params']['IsSilent']){
				#-------------------------------------------------------------------------------
				// сообщаем об этом
				$VK->MessageSend($VkUserID,'Пользователь от имени которого вы отправляете сообщение не существует');
					return new gException('ERROR_SEND_USER_NOT_FOUND_MESSAGE','Ошибка отправки сообщения о не найденном пользователе');
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			// выходим
			return 'ok';
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/www/API/VK]: инициализируем пользователя (%s)',$UserID));
		// инициализируюем юзера, иначе статус не проставится
		$Init = Comp_Load('Users/Init',IsSet($UserID)?$UserID:100);
		if(Is_Error($Init))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// проверяем приложенные файлы и фотки, выковыриваем их
		if(IsSet($Data->{'attachments'}) && SizeOf($Data->{'attachments'}) > 0){
			#-------------------------------------------------------------------------------
			$Count = SizeOf($Data->{'attachments'}) - 1;
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/www/API/VK]: в сообщении (%s) приложенных файлов',$Count+1));
			#-------------------------------------------------------------------------------
			$Hash = Array();
			#-------------------------------------------------------------------------------
			// перебираем файлы
			foreach($Data->{'attachments'} as $File){
				#-------------------------------------------------------------------------------
				$FileType = $File->{'type'};
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/API/VK]: тип файла: %s',$FileType));
				#-------------------------------------------------------------------------------
				if($FileType == 'doc'){	// документ прислали
					#-------------------------------------------------------------------------------
					$Url = $File->{'doc'}->{'url'};
					$Name= $File->{'doc'}->{'title'};
					#-------------------------------------------------------------------------------
				}elseif($FileType == 'photo'){
					#-------------------------------------------------------------------------------
					// с фотами проблема, надо выковыривать с самым большим разрешением (при тестировании самая большая была - самая последняя)
					$Max = SizeOf($File->{'photo'}->{'sizes'}) - 1;
					#-------------------------------------------------------------------------------
					$Url = $File->{'photo'}->{'sizes'}[$Max]->{'url'};
					#-------------------------------------------------------------------------------
					$Name= BaseName($Url);
					#-------------------------------------------------------------------------------
				}else{
					#-------------------------------------------------------------------------------
					// х.з. чего это...
					Debug(SPrintF('[comp/www/API/VK]: тип файла неизвестен: %s',$FileType));
					#-------------------------------------------------------------------------------
					continue;
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[comp/www/API/VK]: Url: %s',$Url));
				#-------------------------------------------------------------------------------
				// какой-то файл есть
				if($FileData = $VK->GetFile($Url,$Name)){
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
					$Hash1 = Comp_Load('www/API/Upload');
					if(Is_Error($Hash1))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
					$Hash[] = $Hash1;
				}
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
		if(!$GLOBALS['__USER']['IsAdmin'])
			$Message = SPrintF('[quote]%s[/quote]%s',$Data->{'reply_message'}->{'text'},($Message)?$Message:'');
		#-------------------------------------------------------------------------------
		// текста может и не быть, даже от админа
		if(!$Message)
			$Message = 'текст сообщения отсуствует';
		#-------------------------------------------------------------------------------
		$Params = Array('Message'=>$Message,'TicketID'=>$Attribs['TicketID'],'UserID'=>$UserID,'IsInternal'=>TRUE);
		#-------------------------------------------------------------------------------
		if(IsSet($Hash) && SizeOf($Hash))
			$Params['TicketMessageFile'] = $Hash;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// статус для тикета
		$StatusID = ($GLOBALS['__USER']['IsAdmin'])?'Opened':'Working';
		#-------------------------------------------------------------------------------
		// постим от админа, т.к. пост может идти от другого юзера в ответ на...
		$GLOBALS['__USER']['ID']	= 100;
		$GLOBALS['__USER']['IsAdmin']	= TRUE;
		$GLOBALS['__USER']['IsNoLogIP']	= 'VKontakte';
		#-------------------------------------------------------------------------------
		$IsAdd = Comp_Load('www/API/TicketMessageEdit',$Params);
		if(Is_Error($IsAdd))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// ставим статус, так как постили по факту от админа, статус не встаёт
		$IsStatus = Comp_Load('www/API/StatusSet',Array('ModeID'=>'Edesks','IsNotNotify'=>TRUE,'IsNoTrigger'=>TRUE,'StatusID'=>$StatusID,'Comment'=>'Сообщение через ВКонтакте','RowsIDs'=>$Attribs['TicketID']));
		if(Is_Error($IsAdd))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$GLOBALS['__USER']['ID']        = 100;
		$GLOBALS['__USER']['IsAdmin']   = FALSE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return 'ok';
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		// не найдено соответсвтие идентификатора в телеграмме и номера сообщения в тикетнцие
		Debug(SPrintF('[comp/www/API/VK]: НЕ найдено соответствие сообщения в VKontakte  и тикета: Data->reply_message->id = %s',$Data->{'reply_message'}->{'id'}));
		#-------------------------------------------------------------------------------
		if(!$Settings['Params']['IsSilent'])
			if(!$VK->MessageSend($VkUserID,$Settings['Params']['EdeskNotFound']))
				return new gException('ERROR_SEND_EdeskNotFound_MESSAGE','Ошибка отправки сообщения о не найденном тикете');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return 'ok';
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}elseif($Type == 'message_new'){
	#-------------------------------------------------------------------------------
	// это новое сообщение, возможно это код подтверждения
	#-------------------------------------------------------------------------------
	// разбираем текст на слова, слова проверяем как код подтверждения
	$Words = Explode(" ",$Message);
	$wCount = 0;
	#-------------------------------------------------------------------------------
	foreach($Words as $Word){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/www/API/VK]: Word = %s',$Word));
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
				$IsUpdated = DB_Update('Contacts',Array('Confirmed'=>Time(),'Confirmation'=>'','ExternalID'=>$VkUserID,'IsActive'=>TRUE),Array('Where'=>SPrintF('`Confirmation` = "%s"',$Word)));
				if(Is_Error($IsUpdated))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				$Event = Array('UserID'=>$Contact['UserID'],'PriorityID'=>'Billing','Text'=>SPrintF('Контактный адрес (%s/%s) подтверждён через "%s"',$Contact['Address'],$VkUserID,$Config['Notifies']['Methods']['VKontakte']['Name']));
				#-------------------------------------------------------------------------------
				$Event = Comp_Load('Events/EventInsert',$Event);
				if(!$Event)
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
				#-------------------------------------------------------------------------------
				// шлём сообщение о успешной активации
				if(!$VK->MessageSend($VkUserID,$Settings['Params']['ConfirmSuccess']))
					return new gException('ERROR_SEND_SUCCESS_ACTIVATE_MESSAGE','Ошибка отправки сообщения о успешной активации на сервер VKontakte');
				#-------------------------------------------------------------------------------
				// шлём сообщение со справочной информацией
				if(!$VK->MessageSend($VkUserID,$Settings['Params']['StubMessage']))
					return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки сообщения-затычки на сервер VKontakte');
				#-------------------------------------------------------------------------------
				// вываливаемся из скрипта, вообще
				return 'ok';
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
// если в сообщении написано /start - выводим подсказку
if($Message == '/start'){
	#-------------------------------------------------------------------------------
	// TODO надо проверить есть ли такой телеграмм у кого-то. если есть - другое сообщение надо слать - справку или рассказ что и почему
	if(!$VK->MessageSend($VkUserID,SPrintF($Settings['Params']['StartMessage'],$Settings['Params']['BotName'])))
		return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки стартового сообщения на сервер Telegram');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// в тихом режиме - просто уходим
if($Settings['Params']['IsSilent'])
	return 'ok';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// сюда мы попали если ничего не найдено
if(!$VK->MessageSend($VkUserID,SPrintF($Settings['Params']['StartMessage'],$Settings['Params']['BotName'])))
	return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки стартового сообщения на сервер VKontakte');
#-------------------------------------------------------------------------------
if(!$VK->MessageSend($VkUserID,$Settings['Params']['StubMessage']))
	return new gException('ERROR_SEND_START_MESSAGE','Ошибка отправки сообщения-затычки на сервер VKontakte');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return 'ok';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


?>
