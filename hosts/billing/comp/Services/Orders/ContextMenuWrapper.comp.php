<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('OrderTypeCode', 'ID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug("[comp/Services/Orders/ContextMenuWrapper]: OrderTypeCode = $OrderTypeCode, ID = $ID");
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__ . $OrderTypeCode . $ID);

$Result = CacheManager::get($CacheID);
if($Result) {
    return $Result;
}
#-------------------------------------------------------------------------------
if($OrderTypeCode == 'Default'){
	$OrderTypeCode = 'Services';
}else{
	$Order = DB_Select($OrderTypeCode . 'OrdersOwners',Array('ID'),Array('UNIQ','Where'=>'OrderID=' . $ID));
	#-------------------------------------------------------------------------------
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
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Menus/List', 'Administrator/ListMenu/' . $OrderTypeCode . 'Order.xml', $ID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
CacheManager::add($CacheID, $Comp, 24 * 3600);
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------

?>
