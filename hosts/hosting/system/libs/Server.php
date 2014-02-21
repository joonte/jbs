<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------

function SelectServerSettingsByService($ServiceID){
	#-------------------------------------------------------------------------------
	$ServersGroup = DB_Select('ServersGroups','*',Array('UNIQ','Where'=>SPrintF('`ServiceID` = %u',$ServiceID),'Limits'=>Array(0,1),'SortOn'=>'SortID'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ServersGroup)){
	case 'error':
		return ERROR | @Trigger_Error('[Server->SelectServerByService]: не удалось выбрать группу серверов');
	case 'exception':
		return new gException('SERVICE_ServersGroups_NOT_FOUND','Для данного сервиса нет групп серверов');
	case 'array':
		#-------------------------------------------------------------------------------
		return SelectServerSettings($ServersGroup['ID']);
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function SelectServerSettings($ServersGroupID){
	#-------------------------------------------------------------------------------
	$Where = Array(SPrintF('`ServersGroupID` = %u',$ServersGroupID),'`IsActive` = "yes"','`IsDefault` = "yes"');
	#-------------------------------------------------------------------------------
	$Settings = DB_Select('Servers','*',Array('UNIQ','Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Settings)){
	case 'error':
		return ERROR | @Trigger_Error('[Server->SelectServer]: не удалось выбрать сервер');
	case 'exception':
		return new gException('SERVER_NOT_FOUND','В данной группе нет серверов активных + по-умолчанию');
	case 'array':
		#-------------------------------------------------------------------------------
		#$this->SystemID = $Settings['SystemID'];
		#-------------------------------------------------------------------------------
		#$this->Settings = $Settings;
		#-------------------------------------------------------------------------------
		#if(Is_Error(System_Load(SPrintF('libs/%s.php',$this->SystemID))))
		#	@Trigger_Error('[HostingServer->Select]: не удалось загрузить целевую библиотеку');
		#-------------------------------------------------------------------------------
		return $Settings;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------


