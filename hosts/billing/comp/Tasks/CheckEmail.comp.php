<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['CheckEmail'];
#-------------------------------------------------------------------------------
# проверяем, есть ли функции для работы с IMAP
if(!Function_Exists('imap_open'))
	return 24*3600;
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return 3600;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/ImapMailbox.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$mailbox = new ImapMailbox(SPrintF("{%s/pop3:110/notls}INBOX",$Settings['CheckEmailServer']), $Settings['CheckEmailLogin'], $Settings['CheckEmailPassword']);
$mails = array();
#-------------------------------------------------------------------------------
foreach($mailbox->searchMailbox() as $mailId) {
	$mail = $mailbox->getMail($mailId);
	// $mailbox->setMailAsSeen($mail->mId);
	// $mailbox->deleteMail($mail->mId);
	$mails[] = $mail;
}
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/CheckEmail]: imap_num_msg = %s',SizeOf($mails)));
$GLOBALS['TaskReturnInfo'][] = SPrintF('%s messages',SizeOf($mails));
#-------------------------------------------------------------------------------
foreach ($mails as $mail){
	#-------------------------------------------------------------------------------
	$Subject = $mail->subject;
	$fromAddress = $mail->fromAddress;
	$textPlain = $mail->textPlain;
	#$GLOBALS['TaskReturnInfo'][] = SPrintF('fromAddress = %s',$fromAddress);
	#-------------------------------------------------------------------------------
	# достаём все заголовки
	$References = FALSE;
	#-------------------------------------------------------------------------------
	$Headers = Explode("\n", Trim($mailbox->fetchHeader($mail->mId)));
	#-------------------------------------------------------------------------------
	if(Is_Array($Headers) && Count($Headers)){
		foreach($Headers as $Line){
			$HeaderLine = Explode(" ",Trim($Line));
			#-------------------------------------------------------------------------------
			if(StrToLower($HeaderLine[0]) == 'in-reply-to:')
				$References = $HeaderLine[1];
			#-------------------------------------------------------------------------------
			if(StrToLower($HeaderLine[0]) == 'references:')
				$References = $HeaderLine[1];
			#-------------------------------------------------------------------------------
		}
	}
	#-------------------------------------------------------------------------------
	# проверяем наличие ссылки на тикет
	if($References){
		#-------------------------------------------------------------------------------
		$Address = MailParse_RFC822_Parse_Addresses($References);
		$Address = Explode("@",$Address[0]['address']);
		#-------------------------------------------------------------------------------
		if(IsSet($Address[1]) && $Address[1] == HOST_ID && IntVal($Address[0]) == $Address[0]){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/CheckEmail]: в Message-ID найдено число = %s',$Address[0]));
			# проверяем наличие такого тикета
			$Count = DB_Count('EdesksMessagesOwners',Array('ID'=>$Address[0]));
			if(Is_Error($Count))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			if($Count){
				$MessageID = $Address[0];
				Debug(SPrintF('[comp/Tasks/CheckEmail]: найден ответ на сообщение тикета %s',$MessageID));
			}
		}
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	# имеем 2 ситуации, задан или не задан $MessageID - соответственно, добавление в тикет или создание тикета
	if(IsSet($MessageID)){
		#-------------------------------------------------------------------------------
		# сообщение на www/API/TicketMessageEdit
		# перезадать $GLOBALS['__USER']['ID'], потом вернуть
	}else{
		#-------------------------------------------------------------------------------
		# сообщение на www/API/TicketEdit, от юзера "Гость" (проверить его существование)
	}
	#-------------------------------------------------------------------------------
	# ампутируем переменную, чтоб в один тикет не напостило все письма
	UnSet($MessageID);
}



#Debug(SPrintF('[comp/Tasks/CheckEmail]: %s',print_r($mails,true)));


#Debug(SPrintF('[comp/Tasks/CheckEmail]: %s',print_r($mailbox->fetchHeader(2),true)));






return 120;
$MBox = Imap_Open(SPrintF("{%s/pop3:110/notls}INBOX",$Settings['CheckEmailServer']), $Settings['CheckEmailLogin'], $Settings['CheckEmailPassword']);
#-------------------------------------------------------------------------------
$Count = imap_num_msg($MBox);
Debug(SPrintF('[comp/Tasks/CheckEmail]: imap_num_msg = %s',$Count));
#-------------------------------------------------------------------------------
# нет сообщений - нечего делать
if($Count == 0){
	$GLOBALS['TaskReturnInfo'][] = 'no messages';
	return 60;
}
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'][] = SPrintF('%s messages',$Count);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём заголовки сообщения
$MsgNum = 2;

$References = FALSE;

$Header = Imap_HeaderInfo($MBox, $MsgNum);
if(IsSet($Header->references))
	$References = $Header->references;
if(IsSet($Header->in_reply_to))
	$References = $Header->references;

$GLOBALS['TaskReturnInfo'][] = SPrintF('References = %s',$References);

Debug(print_r(imap_headerinfo($MBox, $MsgNum),true));
Debug(print_r(Imap_FetchHeader($MBox, $MsgNum),true));
return 20;


$Headers = Explode("\n", Trim(Imap_FetchHeader($MBox, $MsgNum)));
Debug('[comp/Tasks/CheckEmail]: after headers');
if (Is_Array($Headers) && Count($Headers)){
	foreach($Headers as $Line){
		list($HeaderName,$HeaderContent) = @Explode(" ",Trim($Line));
		# проверяем References:
		if(StrToLower($HeaderName) == 'references:'){
			Debug(SPrintF('[comp/Tasks/CheckEmail]: %s',Trim($Line)));
			$References = $HeaderContent;
			break;
		# проверяем In-Reply-To:
		}elseif(StrToLower($HeaderName) == 'in-reply-to:'){
			Debug(SPrintF('[comp/Tasks/CheckEmail]: %s',Trim($Line)));
			$References = $HeaderContent;
			break;
		}
	}
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём тушку сообщения
$Structure = Imap_FetchStructure($MBox, $MsgNum);
if(!empty($Structure->parts)){
	for ($i = 0, $j = Count($Structure->parts); $i < $j; $i++){
		$Part = $Structure->parts[$i];
		if($Part->subtype == 'PLAIN'){
			$Body = Imap_FetchBody($MBox, $MsgNum, $i+1);
		}
	}
}else{
	$Body = Imap_Body($MBox, $MsgNum);
}
#$GLOBALS['TaskReturnInfo'][] = print_r($Body,true);
$GLOBALS['TaskReturnInfo'][] = print_r(Imap_Body($MBox, $MsgNum),true);


#$MsgList = Imap_Headers($MBox);

#$header = imap_header($MBox, 1);

#$header = imap_fetchheader($MBox,1);

#$GLOBALS['TaskReturnInfo'] = print_r($header,true);

#Debug(print_r($MsgList,true));


#$Headers = Imap_Headers($MBox);

#if($Headers){
#	while(list($key,$val) = each($Headers)){
#		Debug(SPrintF('[comp/Tasks/CheckEmail]: key = %s; val = %s',$key,$val));
#	}

#}

# закрываем подключение
Imap_Close($MBox);


#-------------------------------------------------------------------------------
return 60;
#-------------------------------------------------------------------------------

?>
