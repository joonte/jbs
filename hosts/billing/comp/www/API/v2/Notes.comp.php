<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
        return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Interface = 'User';
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!IsSet($GLOBALS['__USER']))
	return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$HostsIDs = $GLOBALS['HOST_CONF']['HostsIDs'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostsIDs as $HostID){
	#-------------------------------------------------------------------------------
	$Folder = SPrintF('%s/hosts/%s/comp/Notes/%s',SYSTEM_PATH,$HostID,$Interface);
	#-------------------------------------------------------------------------------
	if(!File_Exists($Folder))
		continue;
	#-------------------------------------------------------------------------------
	$Files = IO_Scan($Folder);
	if(Is_Error($Files))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	foreach($Files as $File){
		#-------------------------------------------------------------------------------
		$Path = SPrintF('Notes/%s/%s',$Interface,SubStr($File,0,StriPos($File,'.')));
		#-------------------------------------------------------------------------------
		//Debug(SPrintF('[www/API/v2/Notes]: Path = %s',$Path));
		#-------------------------------------------------------------------------------
		$CacheID = Md5($Path . $GLOBALS['__USER']['ID']);
		#-------------------------------------------------------------------------------
		$Result = CacheManager::get($CacheID);
		#-------------------------------------------------------------------------------
		if($Result){
			#-------------------------------------------------------------------------------
			$Notes = $Result;
			#-------------------------------------------------------------------------------
		}else{
			#-------------------------------------------------------------------------------
			$Notes = Comp_Load($Path);
			if(Is_Error($Notes))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			CacheManager::add($CacheID,$Notes,60);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		foreach($Notes as $Note){
			#-------------------------------------------------------------------------------
			$MessageID = SPrintF('note_%s_%s',$GLOBALS['__USER']['ID'],SubStr(Md5(JSON_Encode($Note)),0,6));
			#-------------------------------------------------------------------------------
			if(IsSet($_COOKIE[$MessageID]))
				continue;
			#-------------------------------------------------------------------------------
			//Debug(SPrintF('[www/API/v2/Notes]: Note = %s',print_r($Note,true)));
			$Out[$MessageID] = $Note->ToXMLString();
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
