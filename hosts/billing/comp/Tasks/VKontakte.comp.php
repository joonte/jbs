<?php
#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','Address','Message','Attribs');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
// возможно, параметры не заданы/требуется немедленная отправка - время не опредлеяем
if(!IsSet($Attribs['IsImmediately']) || !$Attribs['IsImmediately']){
	#-------------------------------------------------------------------------------
	// проверяем, можно ли отправлять в заданное время
	$TransferTime = Comp_Load('Formats/Task/TransferTime',$Attribs['Contact']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($TransferTime)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'integer':
		return $TransferTime;
	case 'false':
		break;
	default:
		return ERROR | @Trigger_Error(100);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(SPrintF('[comp/Tasks/VKontakte]: отправка VKontakte сообщения для (%s)', $Address));
#Debug(SPrintF('[comp/Tasks/VKontakte]: Attribs = %s',print_r($Attribs,true)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('libs/HTTP.php','libs/VK.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByTemplate('VKontakte');
#-------------------------------------------------------------------------------
switch(ValueOf($Settings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = 'server with template: VKontakte, params: IsActive, IsDefault not found';
	#-------------------------------------------------------------------------------
	if(IsSet($GLOBALS['IsCron']))
		return 3600;
	#-------------------------------------------------------------------------------
	return $Settings;
	#-------------------------------------------------------------------------------
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VK = new VK($Settings['Params']['Token'],$Settings['Params']['Secret']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// вырезаем некоторые теги, очень уж мешаются при просмотре сообщений
$Message = Preg_Replace('/\[size=([0-9]+)\](.+)\[\/size\]/sU','\\2',$Message);
// 
$Message = Preg_Replace('/\[color=([a-z]+)\](.+)\[\/color\]/sU','\\2',$Message);
// цитата, моноширинным
$Message = Preg_Replace('/\[quote\](.+)\[\/quote\]/sU',"\n--\n\\1\n--\n",$Message);
// код, моноширинный
$Message = Preg_Replace('/\[code\](.+)\[\/code\]/sU',"```\n\\1```\n",$Message);
// жирный
$Message = Preg_Replace('/\[b\](.+)\[\/b\]/sU',"\\1",$Message);
// наклонный
$Message = Preg_Replace('/\[i\](.+)\[\/i\]/sU',"\\1",$Message);
// ссылка с текстом
$Message = Preg_Replace("(\[link\=[\"']?((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-\|]*[\w@?^=%&amp;\/~+#-\|])?)[\"']?\](.+?)\[/link\])","[$5]($1)",$Message);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// добавляем привествие, если необходимо
if($Config['Notifies']['Methods']['VKontakte']['Greeting'])
	$Message = SPrintF("%s\n\n%s",SPrintF(Trim($Config['Notifies']['Methods']['VKontakte']['Greeting']),$Attribs['UserName']),Trim($Message));
#-------------------------------------------------------------------------------
// добавляем подпись, если необходимо
if(!$Config['Notifies']['Methods']['VKontakte']['CutSign'])
	$Message = SPrintF("%s\n\n--\n%s",Trim($Message),$GLOBALS['__USER']['Sign']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// параметры, нужны для базы отправленных сообщений
$Attribs['MessageID']	= IsSet($Attribs['MessageID'])?$Attribs['MessageID']:0;
$Attribs['TicketID']	= IsSet($Attribs['TicketID'])?$Attribs['TicketID']:0;
#-------------------------------------------------------------------------------
if($VkMessageIDs = $VK->MessageSend($Attribs['Contact']['ExternalID'],$Message,($Attribs['MessageID'])?TRUE:FALSE)){
	#-------------------------------------------------------------------------------
	// сохраняем сооветствие отправленного сообщения и кому оно ушло
	if(Is_Array($VkMessageIDs))
		foreach($VkMessageIDs as $VkMessageID)
			if(!$VK->SaveThreadID($Attribs['UserID'],$Attribs['TicketID'],$Attribs['MessageID'],$VkMessageID))
				return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	// если не отправилось, ждём час и пробуем снова
	return 3600;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём данные юзера которому идёт письмо
$User = DB_Select('Users',Array('ID','Params','Email'),Array('UNIQ','ID'=>$Attribs['UserID']));
if(!Is_Array($User))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// шлём файл, если он есть
$Attribs['Attachments'] = Is_Array($Attribs['Attachments'])?$Attribs['Attachments']:Array();
#-------------------------------------------------------------------------------
if(SizeOf($Attribs['Attachments']) > 0){
	#-------------------------------------------------------------------------------
	// шлём файл, если он есть
	if($VkMessageIDs = $VK->FileSend($Attribs['Contact']['ExternalID'],$Attribs['Attachments'])){
		#-------------------------------------------------------------------------------
		// сохраняем сооветствие отправленнго файла и кому он ушёл
		foreach($VkMessageIDs as $VkMessageID)
			if(!$VK->SaveThreadID($Attribs['UserID'],$Attribs['TicketID'],$Attribs['MessageID'],$VkMessageID))
				return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/VKontakte]: не удалось отправить файл в VKontakte'));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/VKontakte]: отсутствуют файлы приложенные к сообщению'));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Config['Notifies']['Methods']['VKontakte']['IsEvent'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Comp_Load('Events/EventInsert', Array('UserID'=>$Attribs['UserID'],'Text'=>SPrintF('Сообщение для (%s) через службу VKontakte отправлено', $Address)));
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'][$User['Email']]	= Array($Address,$Attribs['Contact']['ExternalID']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
