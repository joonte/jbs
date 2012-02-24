<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, wor www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
if(!IsSet($Args)){
	if(Is_Error(System_Load('modules/Authorisation.mod')))
		return ERROR | @Trigger_Error(500);
			$Args = Args();
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
Debug(print_r($Args,true));
$TasksIDs = (array) @$Args['RowsIDs'];
#-------------------------------------------------------------------------------
if(Count($TasksIDs) < 1)
  return new gException('TASKS_NOT_SELECTED','Задачи не выбраны');
#-------------------------------------------------------------------------------
$Array = Array();
#-------------------------------------------------------------------------------
foreach($TasksIDs as $TaskID){
	#-------------------------------------------------------------------------------
	$Config = Config();
	#-------------------------------------------------------------------------------
	$Tasks = $Config['Tasks'];
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Task = DB_Select('Tasks',Array('ID','TypeID'),Array('UNIQ','ID'=>$TaskID));
	#-----------------------------------------------------------------------------
	switch(ValueOf($Task)){
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
	$IsUpdate = DB_Update('Tasks',Array('Errors'=>0,'Result'=>'','IsActive'=>$Tasks['Types'][$Task['TypeID']]['IsActive']),Array('ID'=>$TaskID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------

?>
