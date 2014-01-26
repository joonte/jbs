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
if(IsSet($Config['Tasks']['Types']['GC']['ExcludeServerAccounts'])){
	#-------------------------------------------------------------------------------
	$ExcludeServerAccounts = $Config['Tasks']['Types']['GC']['ExcludeServerAccounts'];
	Debug(SPrintF('[patches/hosting/files/1000011.php]: ExcludeServerAccounts = %s',$ExcludeServerAccounts));
	#-------------------------------------------------------------------------------
	UnSet($Config['Tasks']['Types']['GC']['ExcludeServerAccounts']);
	#-------------------------------------------------------------------------------
	if(!IsSet($Config['Tasks']['Types']['GC']['CheckUsersOnHostingServersSettings']) || !Is_Array($Config['Tasks']['Types']['GC']['CheckUsersOnHostingServersSettings']))
		$Config['Tasks']['Types']['GC']['CheckUsersOnHostingServersSettings'] = Array('ExcludeServerAccounts'=>$ExcludeServerAccounts);
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
