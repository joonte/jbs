<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ServiceID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if($ServiceID){
	#-------------------------------------------------------------------------------
	$Services = DB_Select('Services',Array('ID','Params'),Array('ID'=>$ServiceID));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Services)){
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
}else{
	#-------------------------------------------------------------------------------
	$Services = DB_Select('Services',Array('ID','Params'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Services)){
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
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($Services as $Service){
	#-------------------------------------------------------------------------------
	$Params = $Service['Params'];
	#-------------------------------------------------------------------------------
	if(!$Params)
		$Service['Params'] = Array('Tags'=>Array(),'Statuses'=>Array());
	#-------------------------------------------------------------------------------
	if(!IsSet($Params['Tags']))
		$Params['Tags'] = Array();
	#-------------------------------------------------------------------------------
	if(!IsSet($Params['Statuses']))
		$Params['Statuses'] = Array();
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('Services',Array('Params'=>$Params),Array('ID'=>$Service['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(SizeOf($Services) > 1){
	#-------------------------------------------------------------------------------
	$Event = Array('UserID'=>100,'PriorityID'=>'Billing','Text'=>SPrintF('Успешно восстановлено %u сервисов',SizeOf($Services)));
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$GLOBALS['TaskReturnInfo'] = SPrintF('Recovered: %u services',SizeOf($Services));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
