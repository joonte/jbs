<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/Server.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

return 60;

$HostingServers = DB_Select('HostingServers',Array('ID','Address'));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingServers)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($HostingServers as $HostingServer){
	#-------------------------------------------------------------------------------
	$Server = new Server();
	#-------------------------------------------------------------------------------
	$IsSelected = $Server->Select((integer)$HostingServer['ID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'true':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	# достаём за неделю
	$TFilter = SPrintF('%s - %s',date('Y-m-d',time() - 7*24*3600),date('Y-m-d',time()));
	$Usages = Call_User_Func_Array(Array($Server,'GetCPUUsage'),Array($TFilter));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Usages)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return $Usages;
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
#	$BUsage = Array();
	#-------------------------------------------------------------------------------
#	foreach ($Usages as $Usage)
#		$BUsage[$Usage['account']] = Array('utime'=>$Usage['utime'],'stime'=>$Usage['stime'],'etime'=>$Usage['etime']);
	
#	Debug(SPrintF('[comp/Tasks/HostingCPUUsage]: GetCPUUsage = %s',print_r($Usages,true)));

break;


}



return 60;




?>
