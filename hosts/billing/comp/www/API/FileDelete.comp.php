<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$FileID = (integer) @$Args['FileID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$FileID)
	return new gException('MESSAGE_ID_IS_EMPTY','Не указан файл который необходимо удалить');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём данные файла
$File = DB_Select('FilesOwners','*',Array('UNIQ','ID'=>$FileID));
#-------------------------------------------------------------------------------
switch(ValueOf($File)){
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
// есть типы файлов с которыми работа через свой интерфейс
if(In_Array($File['TableID'],Array('Contracts','Invoices')))
	return new gException('NOT_FOR_THIS_FILE_TYPE','Этот интерфейс не предназначен для файлов этого типа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$FileData = DB_Select(SPrintF('%sOwners',$File['TableID']),'*',Array('UNIQ','ID'=>$File['RowID']));
#-------------------------------------------------------------------------------
switch(ValueOf($FileData)){
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
$Permission = Permission_Check(SPrintF('%sEdit',$File['TableID']),(integer)$__USER['ID'],(integer)$FileData['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($Permission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return new gException('NOT_FILE_OWNER','Это не ваш файл, вы не можете его удалить');
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// проверяем разрешения - как-то откуда можно удлять файлы, откуда нельзя
if(!$__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	$Config = Config();
	#-------------------------------------------------------------------------------
	$Settings = $Config['Interface']['User']['Files'][$File['TableID']];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// для тикетницы - ещё надо проверить не было ли ответов после этого сообщения
	if($File['TableID'] == 'EdesksMessages'){
		#-------------------------------------------------------------------------------
		// если запрещено редактировать сообщения - то удаляьть файлы тоже нельзя
		if(!$Config['Interface']['Edesks']['AllowEditLastMessage'])
			return new gException('TICKET_EDIT_DENY','Запрещено редактировать сообщения');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// надо проверить что это последнее сообщение в тикете
		$LastMessage = DB_Select('EdesksMessages',Array('MAX(`ID`) AS `ID`','CreateDate'),Array('UNIQ','Where'=>SPrintF('`EdeskID` = (SELECT `EdeskID` FROM `EdesksMessages` WHERE `ID` = %u)',$FileData['ID'])));
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
		if($LastMessage['ID'] != $FileData['ID'] || $LastMessage['CreateDate'] - 24*3600 > Time())
			return new gException('TICKET_HAVE_ANSWER','Удалять файлы можно только из последнего сообщения');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	if(!$Settings['AllowDelete'])
        	return new gException('NO_DELETE_PERMISSIONS','Отсутствуют разрешения на удаление файла');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!DeleteUploadedFile($FileID))
	return new gException('CANNOT_DELETE_FILE','Не удалось удалить файл');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
