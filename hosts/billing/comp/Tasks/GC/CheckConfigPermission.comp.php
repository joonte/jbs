<?php

#-------------------------------------------------------------------------------
/** @author Alex keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$ConfigFile = SPrintF('%s/hosts/%s/config/Config.xml',SYSTEM_PATH,HOST_ID); 
$ConfigPermission = SubStr(SPrintF('%o', FilePerms($ConfigFile)), -4);
#-------------------------------------------------------------------------------
if($ConfigPermission != "0600"){
	Debug("[comp/Tasks/GC/CheckConfigPermission]: Bad config permission: " . $ConfigPermission);
	#-------------------------------------------------------------------------------
	$Event = Array(
			'UserID'        => 100,
			'PriorityID'    => 'Warning',
			'Text'          => SPrintF('Неверные права (%s) на конфигурационный файл (%s). Настоятельно рекомендуется установить права 0600.',$ConfigPermission,$ConfigFile),
			'IsReaded'      => FALSE
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
}else{
	Debug("[comp/Tasks/GC/CheckConfigPermission]: config permission is OK");
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
