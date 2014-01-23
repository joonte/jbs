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
$Comp = Comp_Load('Services/Orders/OrdersHistory',Array('OrderID'=>$ServiceOrder['ID']));
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Comp;
case 'array':
	#-------------------------------------------------------------------------------
	$Service = DB_Select('Services',Array('Name','IsNoActionDelete'),Array('UNIQ','ID'=>$ServiceOrder['ServiceID']));
	switch(ValueOf($Service)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
        	if(!$Service['IsNoActionDelete']){
			#-------------------------------------------------------------------------------
			$Comp = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ServiceOrder['UserID'],'TypeID'=>'ServiceDelete','Params'=>Array($Service['Name'],$ServiceOrder['ID'])));
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
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
