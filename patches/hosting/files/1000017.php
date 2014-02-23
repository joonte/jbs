<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$ConfigPath = SPrintF('%s/hosts/%s/config/Config.xml',SYSTEM_PATH,HOST_ID);
#-------------------------------------------------------------------------------
if(File_Exists($ConfigPath)){
	#-------------------------------------------------------------------------------
	$File = IO_Read($ConfigPath);
	if(Is_Error($File))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$XML = String_XML_Parse($File);
	if(Is_Exception($XML))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Config = $XML->ToArray();
	#-------------------------------------------------------------------------------
	$Config = $Config['XML'];
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Config = Array();
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(IsSet($Config['Notifies']['Settings']['SMSGateway'])){
	#-------------------------------------------------------------------------------
	$SMSGateway = $Config['Notifies']['Settings']['SMSGateway'];
	Debug(SPrintF('[patches/hosting/files/1000016.php]: SMSGateway = %s',print_r($SMSGateway,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsDelete = DB_Delete('Servers',Array('Where'=>'`TemplateID` = "SMS"'));
	if(Is_Error($IsDelete))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Server = Array(
			'TemplateID'	=> 'SMS',
			'IsActive'	=> TRUE,
			'IsDefault'	=> TRUE,
			'Protocol'	=> 'tcp',
			'Address'	=> 'smspilot.ru',
			'Port'		=> 80,
			'PrefixAPI'	=> '/api.php',
			'Login'		=> (IsSet($SMSGateway['SMSLogin'])?$SMSGateway['SMSLogin']:''),
			'Password'	=> (IsSet($SMSGateway['SMSPassword'])?$SMSGateway['SMSPassword']:''),
			'Params'	=> Array(
						'Provider'	=> (IsSet($SMSGateway['SMSProvider'])?$SMSGateway['SMSProvider']:'SMSpilot'),
						'ApiKey'	=> (IsSet($SMSGateway['SMSKey'])?$SMSGateway['SMSKey']:''),
						'Sender'	=> (IsSet($SMSGateway['SMSSender'])?$SMSGateway['SMSSender']:''),
						'BalanceLowLimit'=>(IsSet($SMSGateway['BalanceLowLimit'])?$SMSGateway['BalanceLowLimit']:''),
						),
			'SortID'	=> 100000,
			'Monitoring'	=> 'HTTP=80'
			);
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Servers',$Server);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	UnSet($Config['Notifies']['Settings']['SMSGateway']['SMSLogin']);
	UnSet($Config['Notifies']['Settings']['SMSGateway']['SMSPassword']);
	UnSet($Config['Notifies']['Settings']['SMSGateway']['SMSProvider']);
	UnSet($Config['Notifies']['Settings']['SMSGateway']['SMSKey']);
	UnSet($Config['Notifies']['Settings']['SMSGateway']['SMSSender']);
	UnSet($Config['Notifies']['Settings']['SMSGateway']['BalanceLowLimit']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$File = IO_Write($ConfigPath,To_XML_String($Config),TRUE);
if(Is_Error($File))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsFlush = CacheManager::flush();
if(!$IsFlush)
	@Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
