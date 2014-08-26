<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
$Settings = $Config['Tasks']['Types']['DSCalculateNumServers'];
#-------------------------------------------------------------------------------
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecutePeriod'=>$Settings['ExecutePeriod']));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
# если неактивна, то через день запуск
if(!$Settings['IsActive'])
	return 24*3600;
#-------------------------------------------------------------------------------
# select all SchemeID
$UpdateSchemes = DB_Select('DSSchemes',Array('`ID` AS `SchemeID`','NumServers','IsCalculateNumServers'));
#-------------------------------------------------------------------------------
switch(ValueOf($UpdateSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	# no servers ...
	return (Time() + 3600);
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	foreach($UpdateSchemes as $UpdateScheme){
		#-------------------------------------------------------------------------------
		# get UsedServers
		$Where = Array(
				SPrintF('`SchemeID` = %u',$UpdateScheme['SchemeID']),
				"`StatusID` = 'Active' OR `StatusID` = 'Suspended' OR `StatusID` = 'Frozen' OR `StatusID` = 'OnCreate'"
				);
		#-------------------------------------------------------------------------------
		$IsSelect = DB_Select('DSOrders', Array('COUNT(*) AS `Used`'), Array('UNIQ', 'Where'=>$Where));
		if(Is_Error($IsSelect))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#Debug(SPrintF('[comp/Tasks/DSCalculateNumServers]: SchemeID = %s; Used = %s',$UpdateScheme['SchemeID'],$IsSelect['Used']));
		#-------------------------------------------------------------------------------
		# check config for tariff
		if($UpdateScheme['IsCalculateNumServers']){
			#-------------------------------------------------------------------------------
			# compare number servers
			if($UpdateScheme['NumServers'] < $IsSelect['Used'])
				return ERROR | @Trigger_Error(SPrintF("[comp/DSOrders/DSCalculateNumServers]: error, NumServers (%u) < UsedServers (%u)",$UpdateScheme['NumServers'],$IsSelect['Used']));
			#-------------------------------------------------------------------------------
			# update number servers
			$IsUpdate = DB_Update('DSSchemes',Array('RemainServers'=>($UpdateScheme['NumServers'] - $IsSelect['Used'])),Array('ID'=>$UpdateScheme['SchemeID']));
			if(Is_Error($IsUpdate))
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			#-------------------------------------------------------------------------------
			# check RemainServers for this scheme - if it '0' - disable tariff
			$RemainServers = DB_Select('DSSchemes',Array('NumServers','RemainServers','IsCalculateNumServers'),Array('UNIQ','ID'=>$UpdateScheme['SchemeID']));
			#-------------------------------------------------------------------------------
			switch(ValueOf($RemainServers)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				#-------------------------------------------------------------------------------
				if($RemainServers['RemainServers'] < 1){
					#-------------------------------------------------------------------------------
					$IsUpdate = DB_Update('DSSchemes',Array('IsActive'=>FALSE),Array('ID'=>$UpdateScheme['SchemeID']));
					if(Is_Error($IsUpdate))
						return ERROR | @Trigger_Error(500);
					#-------------------------------------------------------------------------------
				}
				#-------------------------------------------------------------------------------
				break;
				#-------------------------------------------------------------------------------
			default:
				return ERROR | @Trigger_Error(101);
			}
			#-------------------------------------------------------------------------------
		}
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
return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
