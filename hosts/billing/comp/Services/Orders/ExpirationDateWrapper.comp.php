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
#Debug("[comp/Services/Orders/ExpirationDateWrapper]: OrderTypeCode = $OrderTypeCode, ID = $ID");
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__ . $OrderTypeCode . $ID);
$Result = CacheManager::get($CacheID);
if(!Is_Error($Result))
return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($OrderTypeCode == 'Default'){
	$Table = "OrdersOwners";
	$Where = 'ID = ' . $ID;
	$CompName = "Formats/Order/ExpirationDate";
	$ColumnName = 'ExpirationDate';
}else{
	$Table = $OrderTypeCode . "OrdersOwners";
	$Where = 'OrderID=' . $ID;
	$CompName = "Formats/" . $OrderTypeCode . "Order/ExpirationDate";
	$ColumnName = 'DaysRemainded';
}
#-------------------------------------------------------------------------------
if($OrderTypeCode == 'Domains'){$ColumnName = 'ExpirationDate';}
#-------------------------------------------------------------------------------
$Order = DB_Select($Table,Array($ColumnName),Array('UNIQ','Where'=>$Where));
#-------------------------------------------------------------------------------
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
#-------------------------------------------------------------------------------
$Comp = Comp_Load($CompName, $Order[$ColumnName]);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
CacheManager::add($CacheID, $Comp, 3600);
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------

?>
