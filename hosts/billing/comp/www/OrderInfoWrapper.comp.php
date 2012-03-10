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
#-------------------------------------------------------------------------------
#$CacheID = Md5($__FILE__ . $ServiceOrderType . $ServiceOrderID);
#$Result = CacheManager::get($CacheID);
#if(!Is_Error($Result))
#  return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ServiceOrderType == 'Default'){
	$Comp = Comp_Load('www/ServiceOrderInfo',$ServiceOrderID);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
}else{
	$Order = DB_Select($ServiceOrderType . 'OrdersOwners',Array('ID'),Array('UNIQ','Where'=>'OrderID=' . $ServiceOrderID));
	#-----------------------------------------------------------------------
	switch(ValueOf($Order)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		$ID = $Order['ID'];
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-----------------------------------------------------------------------
	$Comp = Comp_Load('www/' . $ServiceOrderType . 'OrderInfo',Array(SPrintF('%sOrderID',$ServiceOrderType)=>$ID));
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
#CacheManager::add($CacheID, $Comp, 24 * 3600);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------

?>
