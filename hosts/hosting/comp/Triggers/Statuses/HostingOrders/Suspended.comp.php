<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('HostingOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
# added by lissyara 2012-08-29 in 11:27 MSK, for JBS-474
$Where = Array("`TypeID` = 'HostingDelete' OR `TypeID` = 'HostingSuspend' OR `TypeID` = 'HostingActive' OR `TypeID` = 'HostingCreate'","`IsExecuted` = 'no'");      
#-------------------------------------------------------------------------------
$TaskExecuteTime = DB_Select('Tasks','ExecuteDate',Array('UNIQ','Where'=>$Where,'SortOn'=>'ExecuteDate','IsDesc'=>TRUE,'Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($TaskExecuteTime)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	$ExecuteDate = Time();
	break;
case 'array':
	if($TaskExecuteTime['ExecuteDate'] > Time() + 2*3600){
		$ExecuteDate = Time();
	}else{
		$ExecuteDate = $TaskExecuteTime['ExecuteDate'] + 2*60;
	}
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingSuspend','ExecuteDate'=>$ExecuteDate,'Params'=>Array($HostingOrder['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($IsAdd)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    return TRUE;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
