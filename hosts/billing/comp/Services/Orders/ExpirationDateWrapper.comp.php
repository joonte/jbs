<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Code', 'ID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#Debug("[comp/Services/Orders/ExpirationDateWrapper]: Code = $Code, ID = $ID");
#-------------------------------------------------------------------------------
$CacheID = Md5(SPrintF('%s-%s-%s',$__FILE__,$Code,$ID));
#-------------------------------------------------------------------------------
$Result = CacheManager::get($CacheID);
#-------------------------------------------------------------------------------
if($Result)
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($Code == 'Default'){
	#-------------------------------------------------------------------------------
	$Table = "OrdersOwners";
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`ID` = %u',$ID);
	#-------------------------------------------------------------------------------
	$CompName = "Formats/Order/ExpirationDate";
	#-------------------------------------------------------------------------------
	$ColumnName = 'ExpirationDate';
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Table = SPrintF('%sOrdersOwners',$Code);
	#-------------------------------------------------------------------------------
	$Where = SPrintF('`OrderID` = %u',$ID);
	#-------------------------------------------------------------------------------
	if($Code == 'Domain'){
		#-------------------------------------------------------------------------------
		$CompName = SPrintF('Formats/%sOrder/ExpirationDate',$Code);
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$CompName = SPrintF('Formats/ExpirationDate');
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$ColumnName = 'DaysRemainded';
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($Code == 'Domain')
	$ColumnName = 'ExpirationDate';
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
CacheManager::add($CacheID, $Comp, 3600);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Comp;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
