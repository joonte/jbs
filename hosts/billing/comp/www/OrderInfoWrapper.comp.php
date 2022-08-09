<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$ServiceOrderID		= (integer) @$Args['ServiceOrderID'];
$ServiceOrderType	=  (string) @$Args['ServiceOrderType'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/IPMI.SuperMicro.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrderType == 'Default'){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/ServiceOrderInfo',$ServiceOrderID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Order = DB_Select(SPrintF('%sOrdersOwners',$ServiceOrderType),Array('ID'),Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ServiceOrderID)));
	#-----------------------------------------------------------------------
	switch(ValueOf($Order)){
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
	$Comp = Comp_Load(SPrintF('www/%sOrderInfo',$ServiceOrderType),Array(SPrintF('%sOrderID',$ServiceOrderType)=>$Order['ID']));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
