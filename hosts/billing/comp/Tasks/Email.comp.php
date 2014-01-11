<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','Email','Theme','Message','Heads','ID','Attachments');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Debug(SPrintF('[comp/Tasks/Email]: отправка письма для (%s), тема (%s)',$Email,$Theme));
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = $Email;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Boundary = "\r\n\r\n------==--" . HOST_ID;
#-------------------------------------------------------------------------------
$Message = SPrintF("%s\r\nContent-Transfer-Encoding: 8bit\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n%s",$Boundary,$Message);
#-------------------------------------------------------------------------------
# достаём вложения, если они есть, и прикладываем к сообщению
if(IsSet($Attachments) && SizeOf($Attachments) && Is_Array($Attachments)){
	#-------------------------------------------------------------------------------
	# достаём данные юзера которому идёт письмо
	$User = DB_Select('Users', Array('ID','Params'), Array('UNIQ', 'ID' => $ID));
	if(!Is_Array($User))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if(!IsSet($User['Params']['NotSendEdeskFilesToEmail']) || !$User['Params']['NotSendEdeskFilesToEmail']){
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/Tasks/Email]: письмо содержит %u вложений',SizeOf($Attachments)));
		#-------------------------------------------------------------------------------
		foreach ($Attachments as $Attachment){
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/Email]: обработка вложения (%s), размер (%s), тип (%s)',$Attachment['Name'],$Attachment['Size'],$Attachment['Mime']));
			#-------------------------------------------------------------------------------
			$Message = SPrintF("%s%s\r\nContent-Disposition: attachment;\r\n\tfilename=\"%s\"\r\nContent-Transfer-Encoding: base64\r\nContent-Type: %s;\r\n\tname=\"%s\"\r\n\r\n%s",$Message,$Boundary,Mb_Encode_MimeHeader($Attachment['Name']),$Attachment['Mime'],Mb_Encode_MimeHeader($Attachment['Name']),$Attachment['Data']);
			Debug(SPrintF('[comp/Tasks/Email]: %s',$Attachment['Data']));
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
# закрываем сообщение
$Message = SPrintF("%s\r\n\r\n%s--",$Message,$Boundary);
#-------------------------------------------------------------------------------
$IsMail = @Mail($Email,Mb_Encode_MimeHeader($Theme),$Message,$Heads);
if(!$IsMail)
	return ERROR | @Trigger_Error('[comp/Tasks/Email]: ошибка отправки сообщения, проверьте работу функции mail в PHP');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(!$Config['Notifies']['Methods']['Email']['IsEvent'])
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'=> $ID,
		'Text'	=> SPrintF('Сообщение для (%s) по электронной почте с темой (%s) успешно отправлено',$Email,$Theme)
		);
$Event = Comp_Load('Events/EventInsert',$Event);
#-------------------------------------------------------------------------------
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
