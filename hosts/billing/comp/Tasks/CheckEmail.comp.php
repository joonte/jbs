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
$GLOBALS['TaskReturnInfo'] = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
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
$Headers = Explode("\n", Trim(Imap_FetchHeader($MBox, 1)));
if (Is_Array($Headers) && Count($Headers)){
	foreach($Headers as $Line){
		list($HeaderName,$HeaderContent) = Explode(" ",$Line);
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
$Structure = Imap_FetchStructure($MBox, 1);
if(!empty($Structure->parts)){
	for ($i = 0, $j = Count($Structure->parts); $i < $j; $i++){
		$Part = $Structure->parts[$i];
		if($Part->subtype == 'PLAIN'){
			$Body = Imap_FetchBody($MBox, 1, $i+1);
		}
	}
}else{
	$Body = Imap_Body($MBox, 1);
}
#$GLOBALS['TaskReturnInfo'][] = print_r($Body,true);


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
