<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Params');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Where = Array(
               "(`StatusID` = 'ForTransfer' OR `StatusID` = 'OnTransfer')",	// статус на переносе
	       "`StatusDate` < UNIX_TIMESTAMP() - 60 * 24 * 3600"		// от статуса - больше 180 дней
	       );
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','UserID','DomainName','Name','StatusID',
                 '(SELECT `Params` FROM `Servers` WHERE `DomainOrdersOwners`.`ServerID` = `Servers`.`ID`) AS `Params`'
                );
#-------------------------------------------------------------------------------
$DomainOrders = DB_Select('DomainOrdersOwners',$Columns,Array('Where'=>$Where));
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
	if(!($DomainOrder['StatusID'] == 'ForTransfer' || ($DomainOrder['StatusID'] == 'OnTransfer' && In_Array($DomainOrder['Name'],Array('ru','su','рф'))))){
		#-------------------------------------------------------------------------------
		Debug(SPrintF("[Tasks/GC/DeleteDomainForTransfer]: Домен не попал в условие: '%s.%s', статус: '%s'",$DomainOrder['DomainName'],$DomainOrder['Name'],$DomainOrder['StatusID']));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	Debug(SPrintF("[Tasks/GC/DeleteDomainForTransfer]: Удаление домена '%s.%s', статус '%s'",$DomainOrder['DomainName'],$DomainOrder['Name'],$DomainOrder['StatusID']));
	#----------------------------------TRANSACTION----------------------------------
	if(Is_Error(DB_Transaction($TransactionID = UniqID('comp/Tasks/GC/DeleteDomainForTransfer'))))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>'Deleted','RowsIDs'=>$DomainOrder['ID'],'Comment'=>SPrintF('Заказ домена не был перенесён к регистратору %s, более 180 дней',$DomainOrder['Params']['Name'])));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'    => $DomainOrder['UserID'],
				'PriorityID'=> 'Hosting',
				'Text'      => SPrintF('Автоматическое удаление домена (%s.%s), находится в статусе "%s" более 180 дней',$DomainOrder['DomainName'],$DomainOrder['Name'],$DomainOrder['StatusID'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		#-------------------------------------------------------------------------------
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		break;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(500);
	}
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	if(Is_Error(DB_Commit($TransactionID)))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Count = DB_Count('DomainOrdersOwners',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return ($Count?$Count:TRUE);
#-------------------------------------------------------------------------------
?>
