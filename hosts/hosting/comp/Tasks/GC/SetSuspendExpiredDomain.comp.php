<?php

#-------------------------------------------------------------------------------
/** @author Sergey Sedov (for www.host-food.ru) from Tasks/DomainForSuspend*/
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = "`StatusID` = 'Active' AND `ExpirationDate` - UNIX_TIMESTAMP() <= 0 AND UNIX_TIMESTAMP() - `StatusDate` > 86400";
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainOrdersOwners',Array('ID','OrderID','UserID','CONCAT(`DomainName`,".",(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`)) AS `DomainName`'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($DomainOrders as $DomainOrder){
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[Tasks/GC/SetSuspendExpiredDomain]: Блокировка домена %s; #%d.',$DomainOrder['DomainName'],$DomainOrder['OrderID']) );
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/SetSuspendExpiredDomain'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'Suspended','RowsIDs'=>$DomainOrder['ID'],'Comment'=>'Заказ не был продлен до окончания срока регистрации'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $DomainOrder['UserID'],
				'PriorityID'	=> 'Billing',
				'Text'		=> SPrintF('Заказ домена #%d (%s) не был продлен до окончания срока регистрации. Заказ заблокирован.',$DomainOrder['OrderID'],$DomainOrder['DomainName'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
