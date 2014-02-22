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
if(IsSet($Config['Tasks']['Types']['CheckEmail']['CheckEmailLogin'])){
	#-------------------------------------------------------------------------------
	$CheckEmail = $Config['Tasks']['Types']['CheckEmail'];
	Debug(SPrintF('[patches/billing/files/1000062.php]: CheckEmail = %s',print_r($CheckEmail,true)));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsDelete = DB_Delete('Servers',Array('Where'=>'`TemplateID` = "EmailClient"'));
	if(Is_Error($IsDelete))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Server = Array(
			'TemplateID'	=> 'EmailClient',
			'IsActive'	=> TRUE,
			'IsDefault'	=> TRUE,
			'Protocol'	=> IsSet($CheckEmail['UseSSL'])?(($CheckEmail['UseSSL'])?'ssl':'tcp'):'ssl',
			'Address'	=> IsSet($CheckEmail['CheckEmailServer'])?$CheckEmail['CheckEmailServer']:'pop.yandex.ru',
			'Port'		=> 110,
			'Login'		=> (IsSet($CheckEmail['CheckEmailLogin'])?$CheckEmail['CheckEmailLogin']:''),
			'Password'	=> (IsSet($CheckEmail['CheckEmailPassword'])?$CheckEmail['CheckEmailPassword']:''),
			'Params'	=> Array(
						'Method'=>(IsSet($CheckEmail['CheckEmailProtocol'])?$CheckEmail['CheckEmailProtocol']:'pop3'),
						'Monitoring'=>"POP3=110\nPOP3S=995\nIMAP4=143\nIMAP4S=993"
						),
			'Notice'	=> 'Используется учётная запись от которой шлёт сообщения биллинг (пользователь с идентификатором 100)',
			'SortID'	=> 100000
			);
	#-------------------------------------------------------------------------------
	$IsInsert = DB_Insert('Servers',$Server);
	if(Is_Error($IsInsert))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	UnSet($Config['Tasks']['Types']['CheckEmail']['UseSSL']);
	UnSet($Config['Tasks']['Types']['CheckEmail']['CheckEmailServer']);
	UnSet($Config['Tasks']['Types']['CheckEmail']['CheckEmailLogin']);
	UnSet($Config['Tasks']['Types']['CheckEmail']['CheckEmailPassword']);
	UnSet($Config['Tasks']['Types']['CheckEmail']['CheckEmailProtocol']);
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
