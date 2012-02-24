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
Debug("[comp/Services/Orders/SchemeWrapper]: OrderTypeCode = $OrderTypeCode, ID = $ID");
#-------------------------------------------------------------------------------
$CacheID = Md5($__FILE__ . $OrderTypeCode . $ID);
$Result = MemoryCache_Get($CacheID);
if(!Is_Error($Result))
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($OrderTypeCode == 'Default'){
	$Order = DB_Select('OrdersOwners','(SELECT `Item` FROM `Services` WHERE `OrdersOwners`.`ServiceID`=`Services`.`ID`) AS `SchemeName`',Array('UNIQ','Where'=>'ID=' . $ID));
	#-------------------------------------------------------------------------------
}else{
	$Columns = Array(
			'ID',
			SPrintF('(SELECT `Name` FROM `%sSchemes` WHERE `%sOrdersOwners`.`SchemeID` = `%sSchemes`.`ID`) as `SchemeName`',$OrderTypeCode,$OrderTypeCode,$OrderTypeCode)
			);
	$Order = DB_Select(SPrintF('%sOrdersOwners',$OrderTypeCode),$Columns,Array('UNIQ','Where'=>'OrderID=' . $ID));
}
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
$Comp = Comp_Load('Formats/String',$Order['SchemeName'],10);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
MemoryCache_Add($CacheID, $Comp, 24 * 3600);
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------

?>
