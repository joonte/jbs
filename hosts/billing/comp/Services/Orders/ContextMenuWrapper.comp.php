<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('OrderTypeCode','Replace');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if($OrderTypeCode == 'Default'){
	#-------------------------------------------------------------------------------
	$OrderTypeCode = 'Services';
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Order = DB_Select(SPrintF('%sOrdersOwners',$OrderTypeCode),Array('ID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$Replace['ServiceOrderID'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Order)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Replace['ServiceOrderID'] = $Order['ID'];
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Menus/List',SPrintF('%s/ListMenu/%sOrder.xml',$GLOBALS['__USER']['InterfaceID'],$OrderTypeCode),$Replace);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
