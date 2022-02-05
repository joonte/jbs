<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Tasks']['Types']['ISPswForDelete'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# достаём время выполнения
$ExecuteTime = Comp_Load('Formats/Task/ExecuteTime',Array('ExecuteTime'=>$Settings['ExecuteTime'],'ExecuteDays'=>@$Settings['ExecuteDays'],'DefaultTime'=>MkTime(1,10,0,Date('n'),Date('j')+1,Date('Y'))));
if(Is_Error($ExecuteTime))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if(!$Settings['IsActive'])
	return $ExecuteTime;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DeleteTimeout = $Config['Tasks']['Types']['ISPswForDelete']['DeleteTimeout'] * 24 * 3600;
#-------------------------------------------------------------------------------
$Where = "`StatusID` = 'Suspended' AND `StatusDate` + $DeleteTimeout - UNIX_TIMESTAMP() <= 0";
#-------------------------------------------------------------------------------
$ISPswOrders = DB_Select('ISPswOrders','ID',Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = SPrintF('Deleted %s accounts',SizeOf($ISPswOrders));
	#-------------------------------------------------------------------------------
	foreach($ISPswOrders as $ISPswOrder){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Deleted','RowsIDs'=>$ISPswOrder['ID'],'Comment'=>'Срок блокировки заказа окончен'));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Comp)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			# No more...
			break;
		default:
			return ERROR | @Trigger_Error(101);
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
