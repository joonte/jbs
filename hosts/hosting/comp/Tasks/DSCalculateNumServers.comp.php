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

#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

# select all SchemeID
$UpdateSchemes = DB_Select('DSSchemes',Array('`ID` AS `SchemeID`','NumServers','IsCalculateNumServers'),Array('Where'=>'1',));
switch(ValueOf($UpdateSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# no servers ...
	return (time() + 3600);
	break;
case 'array':
	foreach($UpdateSchemes as $UpdateScheme){
		# get UsedServers
		$Where = "`SchemeID` = " . $UpdateScheme['SchemeID'] . " AND (`StatusID` = 'Active' OR `StatusID` = 'Suspended' OR `StatusID` = 'Frozen' OR `StatusID` = 'OnCreate')";
		$NumUsed = DB_Select('DSOrders', Array('COUNT(*) AS `Used`'), Array('UNIQ', 'Where'=>$Where));
		# check config for tariff
		if($UpdateScheme['IsCalculateNumServers'] == "yes"){
			# compare number servers
			if($UpdateScheme['NumServers'] < $NumUsed['Used'])
				return ERROR | @Trigger_Error("[comp/DSOrders/DSCalculateNumServers]: error, NumServers < UsedServers (" . $UpdateScheme['NumServers'] . " < " . $NumUsed['Used'] . ")");
			# update number servers
			$IsQuery = DB_Query("UPDATE `DSSchemes` SET `RemainServers`=(`NumServers` - " . $NumUsed['Used'] . ") WHERE `ID`='" . $UpdateScheme['SchemeID'] . "'");
			if(Is_Error($IsQuery))
				return ERROR | @Trigger_Error(500);
			# check RemainServers for this scheme - if it '0' - disable tariff
			$RemainServers = DB_Select('DSSchemes',Array('NumServers','RemainServers','IsCalculateNumServers'),Array('UNIQ','ID'=>$UpdateScheme['SchemeID']));
			switch(ValueOf($RemainServers)){
			case 'error':
				return ERROR | @Trigger_Error(500);
			case 'exception':
				return ERROR | @Trigger_Error(400);
			case 'array':
				if($RemainServers['RemainServers'] == "0"){
					$IsQuery = DB_Query("UPDATE `DSSchemes` SET `IsActive`='no' WHERE `ID`='" . $UpdateScheme['SchemeID'] . "'");
					if(Is_Error($IsQuery))
						return ERROR | @Trigger_Error(500);
				}
				break;
			default:
				return ERROR | @Trigger_Error(101);
			}	
		}
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}

$Config = Config();
#-------------------------------------------------------------------------------
return(time() + $Config['Tasks']['Types']['DSCalculateNumServers']['RequestPeriod'] * 60);


?>
