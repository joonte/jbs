<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$EdeskID   = (integer) @$Args['EdeskID'];
$MessageID = (integer) @$Args['MessageID'];
$Message   =  (string) @$Args['Message'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Message = Trim($Message);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($GLOBALS['__USER']['IsEmulate']))
	return new gException('DENY_WRITE_MESSAGE_FROM_ANOTHER_USER','Нельзя писать сообщения от имени другого пользователя');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Message)
	return new gException('MESSAGE_IS_EMPTY','Введите сообщение');
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Tree.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['Edesks'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// усекаем сообщение на 32k символов
$Message = Mb_SubStr(Mb_Convert_Encoding($Message,'UTF-8'),0,32000);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
// если задан MessageID - это редактирование существующего сообжения
if($MessageID){
	#-------------------------------------------------------------------------------
	if(!$__USER['IsAdmin']){
		#-------------------------------------------------------------------------------
		# проверить, что номер сообщения максимальный в этом треде
		$LastMessage = DB_Select('EdesksMessages',Array('ID','UserID','CreateDate'),Array('UNIQ','Where'=>SPrintF('`EdeskID` = (SELECT `EdeskID` FROM `EdesksMessages` WHERE `ID` = %u)',$MessageID),'SortOn'=>'ID','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
		#-------------------------------------------------------------------------------
		switch(ValueOf($LastMessage)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		if($LastMessage['ID'] != $MessageID)
			return new gException('DENY_EDIT_NOT_LAST_MESSAGE','Нельзя отредактировать сообщение на которое уже был ответ');
		#-------------------------------------------------------------------------------
		# проверить, что редактируемое сообщение принадлежит этому юзеру
		if($LastMessage['UserID'] != $__USER['ID'])
			return ERROR | @Trigger_Error(700);
		#-------------------------------------------------------------------------------
		# проверить, что клиентам редактирование сообщений вообще разрешено
		if(!$Settings['AllowEditLastMessage'])
			return new gException('DENY_EDIT_MESSAGE','Редактировать сообщения запрещено');
		#-------------------------------------------------------------------------------
		# проверить что с момента создания сообщения не прошло суток
		if($LastMessage['CreateDate'] < Time() - 24*3600)
			return new gException('TOO_OLD_MESSAGE','Сообщение слишком старое, нельзя отредактировать');
		#-------------------------------------------------------------------------------
		# добавляем к сообщению текст когда оно отредактировано
		$Message = SPrintF("%s\n\n[size=10][color=gray]last edit: %s in %s[/color][/size]",$Message,Date('Y-m-d'),Date('H:i:s'));
		#return new gException('NOT_IMPLEMENTED','Редактирование сообщение находится в процессе реализации');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$UMessage = Array('Content'=>$Message);
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('EdesksMessages',$UMessage,Array('ID'=>$MessageID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return Array('Status'=>'Ok','MessageID'=>$MessageID);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Edesk = DB_Select('Edesks',Array('ID','TargetGroupID','Theme'),Array('UNIQ','ID'=>$EdeskID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Edesk)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Entrance = Tree_Entrance('Groups',(integer)$Edesk['TargetGroupID']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($Entrance)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		if(!In_Array($__USER['GroupID'],$Entrance))
			return ERROR | @Trigger_Error(700);
		#-------------------------------------------------------------------------------
		$IEdeskMessage = Array('UserID'=>$__USER['ID'],'EdeskID'=>$Edesk['ID'],'Content'=>$Message);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$Users = DB_Select('Users','ID',Array('Where'=>SPrintF('`ID` IN (SELECT `UserID` FROM `EdesksMessages` WHERE `EdeskID` = %u) AND `ID` != %u AND `ID` > 50',$Edesk['ID'],$__USER['ID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Users)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# No more...
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			foreach($Users as $User){
				#-------------------------------------------------------------------------------
				$IsSend = NotificationManager::sendMsg('EdeskMessageCreate',(integer)$User['ID'],Array('EdeskID'=>$Edesk['ID'],'Theme'=>$Edesk['Theme'],'Message'=>$Message));
				#-------------------------------------------------------------------------------
				switch(ValueOf($IsSend)){
				case 'error':
					return ERROR | @Trigger_Error(500);
				case 'exception':
					# No more...
				case 'true':
					# No more...
					 break;
				default:
					return ERROR | @Trigger_Error(101);
				}
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		$MessageID = DB_Insert('EdesksMessages',$IEdeskMessage);
		if(Is_Error($MessageID))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('Edesks',Array('UpdateDate'=>Time()),Array('ID'=>$Edesk['ID']));
		#-------------------------------------------------------------------------------
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok','MessageID'=>$MessageID);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
