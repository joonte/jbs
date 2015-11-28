<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','Mobile','Message','ID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
Debug(SPrintF('[comp/Tasks/WhatsApp]: отправка WhatsApp сообщения для (%u)',$Mobile));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
if(!$Config['Notifies']['Methods']['WhatsApp']['IsActive']){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/WhatsApp]: уведомления через WhatsApp отключены'));
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = $Mobile;
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('classes/WhatsApp/whatsprot.class.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Settings = SelectServerSettingsByTemplate('WhatsApp');
#-------------------------------------------------------------------------------
switch(ValueOf($Settings)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = 'server with template: WhatsApp, params: IsActive, IsDefault not found';
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
$Tmp = System_Element('tmp');
if(Is_Error($Tmp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# кастыли, поскольку класс хочет директорию для базы внутри своей директории
$waPath = SPrintF('%s/hosts/billing/system/classes/WhatsApp',SYSTEM_PATH);
#-------------------------------------------------------------------------------
$wadata = SPrintF('%s/wadata',$waPath);
#-------------------------------------------------------------------------------
if(!Is_Link($wadata) && Is_Dir($wadata)){
	#-------------------------------------------------------------------------------
	if(Is_Error(IO_RmDir(SPrintF('%s///wadata',$waPath))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$DataFolder = SPrintF('%s/WhatsApp',$Tmp);
#-------------------------------------------------------------------------------
$LogFile = SPrintF('%s/WhatsApp.%s.log',$DataFolder,Date('Y-m-d'));
#-------------------------------------------------------------------------------
if(!Is_Dir(SPrintF('%s/logs',$DataFolder)))
	if(!MkDir(SPrintF('%s/logs',$DataFolder),0750,true))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!Is_Link($wadata))
	if(!SymLink($DataFolder,$wadata))
		return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$WhatsAppClient = new WhatsProt($Settings['Login'], $Settings['Params']['Sender'],FALSE,TRUE,$DataFolder);
if(Is_Error($WhatsAppClient))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$WhatsAppClient->connect();
#-------------------------------------------------------------------------------
$WhatsAppClient->loginWithPassword($Settings['Password']);
#-------------------------------------------------------------------------------
if(!File_Exists(SPrintF('%s/info.update.txt',$DataFolder))){
	#-------------------------------------------------------------------------------
	$WhatsAppClient->sendStatusUpdate($Settings['Params']['StatusMessage']);
	#-------------------------------------------------------------------------------
	$WhatsAppClient->sendSetProfilePicture($Settings['Params']['ProfileImage']);
	#-------------------------------------------------------------------------------
	$IsWrite = IO_Write(SPrintF('%s/info.update.txt',$DataFolder),'user info updated',TRUE);
	if(Is_Error($IsWrite))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsMessage = $WhatsAppClient->sendMessage($Mobile,$Message);
if(Is_Error($IsMessage)){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/WhatsApp]: error sending message, see error file: %s',$LogFile));
	#-------------------------------------------------------------------------------
	return 3600;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Config['Notifies']['Methods']['WhatsApp']['IsEvent'])
	return TRUE;
#-------------------------------------------------------------------------------
$Event = Comp_Load('Events/EventInsert',Array('UserID'=>$ID,'Text'=>SPrintF('Сообщение для (%u) через службу WhatsApp отправлено',$Mobile)));
#-------------------------------------------------------------------------------
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
