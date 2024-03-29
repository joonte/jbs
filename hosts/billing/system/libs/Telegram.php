<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/

class Telegram
{
	#-------------------------------------------------------------------------------
	// параметры
	public $Address	= 'api.telegram.org';
	public $Host	= 'api.telegram.org';
	public $Secret	= 'XXXXXXXXXXXXXX';
	public $Token	= '00-000-00';
	#-------------------------------------------------------------------------------
	public function __construct($Token,$Secret) {
		$this->Token	= $Token;
		$this->Secret	= $Secret;
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// отправка сообщения
	public function MessageSend($ChatID,$Text = 'not set',$IsReply = FALSE){
		#-------------------------------------------------------------------------------
		// вырезаем неразрешённые теги из сообщения
		$Text = Strip_Tags($Text,'<b><i><a><code><pre>');
		#-------------------------------------------------------------------------------
		$Query = Array('chat_id'=>$ChatID,'text'=>Mb_SubStr($Text,0,4096),'disable_web_page_preview'=>'TRUE','parse_mode'=>'None');
		#-------------------------------------------------------------------------------
		// если надо показать меню что возможен ответ на сообщение
		if($IsReply)
			$Query['reply_markup'] = Json_Encode(Array('force_reply'=>TRUE));
		#-------------------------------------------------------------------------------
		$Result = $this->API('sendMessage',$Query);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(IsSet($Result['ok']) && $Result['ok']){
			#-------------------------------------------------------------------------------
			// возвращаем внутренний идентфикатор сообщения в телеграмме
			return Array($Result['result']['message_id']);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
			#-------------------------------------------------------------------------------
			// TODO по идее там есть человекочитемое сообщение о ошибке. надо словить и выдать в ответе
			if(IsSet($Result['error_code']) && $Result['error_code'] == 400){
				#-------------------------------------------------------------------------------
				// ругается на cущности. когда мусор типа <http://ya.ru/> воспринимается как тег
				return TRUE;
				#-------------------------------------------------------------------------------
			}elseif(IsSet($Result['error_code']) && $Result['error_code'] == 403){
				#-------------------------------------------------------------------------------
				// юзер залочил бота. по уму, надо бы куда-то деть ChatID или сразу выпилить из оповещений его
				return TRUE;
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				return FALSE;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// отправка файла
	public function FileSend($ChatID,$Attachments = Array(),$IsReply = FALSE){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[FileSend]: Attachments = %s',print_r($Attachments,true)));
		#-------------------------------------------------------------------------------
		// массив под идентифкаторы отправленных сообщений
		$Array = Array();
		#-------------------------------------------------------------------------------
		foreach ($Attachments as $Attachment){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/libs/Telegram]: обработка вложения (%s), размер (%s), тип (%s)',$Attachment['Name'],$Attachment['Size'],$Attachment['Mime']));
			#-------------------------------------------------------------------------------
			$Query = Array('chat_id'=>$ChatID);
			#-------------------------------------------------------------------------------
			// если надо показать меню что возможен ответ на сообщение
			if($IsReply)
				$Query['reply_markup'] = Json_Encode(Array('force_reply'=>TRUE));
			#-------------------------------------------------------------------------------
			// по дефолту, метод sendDocument
			$Method = 'sendDocument';
			#-------------------------------------------------------------------------------
			// если это картинка - меняем метод
			$Mime = Explode('/',$Attachment['Mime']);
			if($Mime[0] == 'image')
				$Method = 'sendPhoto';
			#-------------------------------------------------------------------------------
			$Result = $this->API($Method,$Query,$Attachment);
			#-------------------------------------------------------------------------------
			if(IsSet($Result['ok']) && $Result['ok']){
				#-------------------------------------------------------------------------------
				$Array[] = $Result['result']['message_id'];
				#-------------------------------------------------------------------------------
				continue;
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
				#-------------------------------------------------------------------------------
				// TODO по идее там есть человекочитемое сообщение о ошибке. надо словить и выдать в ответе
				return FALSE;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return $Array;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// скачиваем файл во временную директорию, отдаём его данные
	public function GetFile($FileID){
		#-------------------------------------------------------------------------------
		$Query = Array('file_id'=>$FileID);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Result = $this->API('getFile',$Query);
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(IsSet($Result['ok']) && $Result['ok']){
			#-------------------------------------------------------------------------------
			$Tmp = System_Element('tmp');
			if(Is_Error($Tmp))
				return ERROR | @Trigger_Error('[system/libs/Telegram]: не удалось найти временную папку');
			#-------------------------------------------------------------------------------
			// скачиваем файл во временную директорию
			$Context= Stream_Context_Create(Array('http'=>Array('timeout'=>2)));
			#-------------------------------------------------------------------------------
			$File	= @File_Get_Contents(SPrintF('https://%s/file/bot%s/%s',$this->Host,$this->Token,$Result['result']['file_path']),FALSE,$Context);
			#-------------------------------------------------------------------------------
			$FilePath = SPrintF('%s/files/%s',$Tmp,$Result['result']['file_id']);
			#-------------------------------------------------------------------------------
			$IsWrited = IO_Write($FilePath,$File,TRUE);
			if(Is_Error($IsWrited))
				return ERROR | @Trigger_Error('[system/libs/Telegram->GetFile]: не удалось сохранить файл');
			#-------------------------------------------------------------------------------
			if(FileSize($FilePath)){
				#-------------------------------------------------------------------------------
				return Array('size'=>FileSize($FilePath),'error'=>0,'tmp_name'=>$FilePath,'name'=>BaseName($Result['result']['file_path']));
				#-------------------------------------------------------------------------------
			}else{
				#-------------------------------------------------------------------------------
				return FALSE;
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// TODO по идее там есть человекочитемое сообщение о ошибке. надо словить и выдать в ответе
			return FALSE;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// регистрируем WEB-hook для бота
	public function SetWebHook(){
		#-------------------------------------------------------------------------------
		// строим URL
		$Url = SPrintF('https://%s/API/Telegram?Secret=%s',HOST_ID,$this->Secret);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// проверяем веб-хук
		$Result = $this->API('getWebhookInfo');
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
		#-------------------------------------------------------------------------------
		if(IsSet($Result['ok']) && $Result['ok'] && $Result['result']['url'] == $Url){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/libs/Telegram]: Веб-хук установлен и соответствует заданнному в настройках: %s',$Url));
			#-------------------------------------------------------------------------------
			return TRUE;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		// если не установле или ошибка - устанавливаем заново
		$Query = Array('url'=>$Url);
		#-------------------------------------------------------------------------------
		$Result = $this->API('setWebhook',$Query);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[system/libs/Telegram]: $Result = %s',print_r($Result,true)));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		if(IsSet($Result['ok']) && $Result['ok']){
			#-------------------------------------------------------------------------------
			return TRUE;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// TODO по идее там есть человекочитемое сообщение о ошибке. надо словить и выдать в ответе
			Debug(SPrintF('[SetWebHook]: $Result = %s',print_r($Result,true)));
			#-------------------------------------------------------------------------------
			return FALSE;
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
        // АПИ
        private function API($Method,$Query = Array(),$Attachment = Array()){
		#-------------------------------------------------------------------------------
		$HTTP = Array(
				'Address'	=> $this->Address,
				'Port'		=> 443,
				'Host'		=> $this->Host,
				'Protocol'	=> 'ssl',
				);
		#-------------------------------------------------------------------------------
		$Url = SPrintF('/bot%s/%s',$this->Token,$Method);
		#-------------------------------------------------------------------------------
		if($Method == 'sendDocument' || $Method == 'sendPhoto'){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[system/libs/Telegram]: обработка вложения (%s), размер (%s), тип (%s)',$Attachment['Name'],$Attachment['Size'],$Attachment['Mime']));
			#-------------------------------------------------------------------------------
			// имя поля в форме
			$FieldName = ($Method == 'sendPhoto')?'photo':'document';
			#-------------------------------------------------------------------------------
			$HTTP['Charset'] = '';
			#-------------------------------------------------------------------------------
			$Boundary = SPrintF('----%s',Md5(Rand()));
			#-------------------------------------------------------------------------------
			$Body = SPrintF("--%s\r\n",$Boundary);
			$Body = SPrintF("%sContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"\r\n",$Body,$FieldName,$Attachment['Name']);
			$Body = SPrintF("%sContent-Type: %s\r\n",$Body,$Attachment['Mime']);
			$Body = SPrintF("%s\r\n%s",$Body,Base64_Decode($Attachment['Data']));
			$Body = SPrintF("%s\r\n--%s--\r\n\r\n",$Body,$Boundary);
			#-------------------------------------------------------------------------------
			$Headers = Array(SPrintF('Content-Type: multipart/form-data; boundary=%s',$Boundary)/*,'Connection: keep-alive','Keep-Alive: 300'*/);
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			// без файла передаём через POST
			$Headers	= Array();
			$Body		= $Query;
			$Query		= Array();
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		$Result = HTTP_Send($Url,$HTTP,$Query,$Body,$Headers);
		#-------------------------------------------------------------------------------
		if(Is_Error($Result))
			return ERROR | @Trigger_Error('[API]: не удалось выполнить запрос к серверу');
		#-------------------------------------------------------------------------------
		$Result = Trim($Result['Body']);
		#-------------------------------------------------------------------------------
		$Result = Json_Decode($Result,TRUE);
		#-------------------------------------------------------------------------------
		// вообще, надо разобраться на этом этапе с результатом, и вернуть уже итог, и в случае ошибки - параметры
		#-------------------------------------------------------------------------------
		// при слишком быстрой отправке, оповещения не успевают на телефоне звучать =)
		Sleep(2);
		#-------------------------------------------------------------------------------
		return $Result;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// сохраняем MessageID, просто в файлик, нефига ради такого колонку в базе держать. пока, по крайней мере
	public function SaveThreadID($UserID,$TicketID,$MessageID,$TgMessageID){
		#-------------------------------------------------------------------------------
		// если нет номера тикета - сохранять не надо
		if($TicketID == 0)
			return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// сохраняем переданные данные
		$IsInsert = DB_Insert('TmpData',Array('UserID'=>$UserID,'AppID'=>'Telegram','Col1'=>$TicketID,'Col2'=>$MessageID,'Col3'=>$TgMessageID));
		if(Is_Error($IsInsert))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	}

	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// поиск тикета по номеру сообщения в телеграмме
	public function FindThreadID($TgMessageID){
		#-------------------------------------------------------------------------------
                #-------------------------------------------------------------------------------
		$Thread = DB_Select('TmpData','*',Array('UNIQ','Where'=>Array('`AppID` = "Telegram"',SPrintF('`Col3` = "%s"',$TgMessageID)),'SortOn'=>'CreateDate','Limits'=>Array(0,1)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Thread)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return FALSE;
		case 'array':
			#-------------------------------------------------------------------------------
			return Array('UserID'=>$Thread['UserID'],'TicketID'=>$Thread['Col1'],'MessageID'=>$Thread['Col2']);
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}       
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
