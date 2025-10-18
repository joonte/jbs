<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
#-------------------------------------------------------------------------------

function SelectServerErrorMessage($ServiceID){
	#-------------------------------------------------------------------------------
	$Service = DB_Select('Services',Array('ID','Code','Name'),Array('UNIQ','ID'=>IntVal($ServiceID)));
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVICE_NOT_FOUND',SPrintF('Сервис (%s) не существует',$ServiceID));
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Exception =  new gException('SETTINGS_NOT_FOUND_2','Дополнения -> Мастера настройки -> Сервера');
	#-------------------------------------------------------------------------------
	$Message = SPrintF('Для работы (%s) необходимо создать группу серверов для этого сервиса, а также активный сервер входящий в эту группу. Чтобы сделать это, пройдите в следующий раздел биллинговой системы:',$Service['Name'],$Service['Name']);
	return new gException('SETTINGS_NOT_FOUND_1',$Message,$Exception);
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function SelectServerSettingsByService($ServiceID){
	#-------------------------------------------------------------------------------
	$Service = DB_Select('Services',Array('ID','Code','Name'),Array('UNIQ','ID'=>IntVal($ServiceID)));
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVICE_NOT_FOUND',SPrintF('Сервис (%s) не существует',$ServiceID));
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
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
		return ERROR | @Trigger_Error('[Server->SelectServerSettings]: не удалось выбрать сервер');
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
function SelectServerSettingsByTemplate($TemplateID,$Uniq = TRUE){
	#-------------------------------------------------------------------------------
	$Where = Array(SPrintF('`TemplateID` = "%s"',$TemplateID),'`IsActive` = "yes"');
	#-------------------------------------------------------------------------------
	# в зависимости от того требуется ли уникальная запись, разный запрос
	if($Uniq)
		$Where[] = '`IsDefault` = "yes"';
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Array = Array('Where'=>$Where);
	#-------------------------------------------------------------------------------
	# в зависимости от того требуется ли уникальная запись, разный запрос
	if($Uniq)
		$Array[] = 'UNIQ';
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Settings = DB_Select('Servers','*',$Array);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Settings)){
	case 'error':
		return ERROR | @Trigger_Error('[Server->SelectServerSettingsByTemplate]: не удалось выбрать сервер');
	case 'exception':
		return new gException('SERVER_NOT_FOUND','В данной группе нет серверов активных + по-умолчанию');
	case 'array':
		return $Settings;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
function SelectServerSettingsByAddress($Address){
	#-------------------------------------------------------------------------------
	$Where = Array(SPrintF('`Address` = "%s"',$Address),'`IsActive` = "yes"','`IsDefault` = "yes"');
	#-------------------------------------------------------------------------------
	$Settings = DB_Select('Servers','*',Array('UNIQ','Where'=>$Where));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Settings)){
	case 'error':
		return ERROR | @Trigger_Error('[Server->SelectServerSettingsByAddress]: не удалось выбрать сервер');
	case 'exception':
		return new gException('SERVER_NOT_FOUND',SPrintF('Сервер с адресом (%s) не найден',$Address));
	case 'array':
		return $Settings;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}


