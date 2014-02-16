<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ServiceOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Service = DB_Select('Services',Array('Name','Params'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Service)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	if(!IsSet($Service['Params']['Statuses']['Suspended']['IsNoAction']) || !$Service['Params']['Statuses']['Suspended']['IsNoAction']){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ServiceOrder['UserID'],'TypeID'=>'ServiceSuspend','Params'=>Array($Service['Name'],$ServiceOrder['ID'])));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Comp)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return ERROR | @Trigger_Error(400);
		case 'array':
			break;
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	return TRUE;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
