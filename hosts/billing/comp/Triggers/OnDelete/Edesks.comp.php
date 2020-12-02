<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Edesk');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# выбираем из базы все сообщения тикета
$Messages = DB_Select('EdesksMessages','ID',Array('Where'=>SPrintF('`EdeskID` = %u',$Edesk['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Messages)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Messages as $Message){
		#-------------------------------------------------------------------------------
		// достаём все файлы и удаляем
		$Files = GetUploadedFilesInfo('EdesksMessages',$Message['ID']);
		#-------------------------------------------------------------------------------
		foreach($Files as $File)
			if(!DeleteUploadedFile($File['ID']))
				return new gException('CANNOT_DELETE_FILE','Не удалось удалить связанный файл');
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		// удаляем логи IP адресов в связанной таблице
		$Where = SPrintF('`UserID` = %u',$User['ID']);
		#-------------------------------------------------------------------------------
		$IsDelete = DB_Delete('UsersIPs',Array('Where'=>SPrintF('`EdesksMessageID` = %u',$Message['ID'])));
		if(Is_Error($IsDelete))
			return new gException('CANNOT_DELETE_UsersIPs_RECORDS',SPrintF('Не удалось удалить лог IP адресов для сообщения %u',$Message['ID']));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
