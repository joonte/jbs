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
$DomainOrderID = (integer) @$Args['DomainOrderID'];
$AuthInfo      =  (string) @$Args['AuthInfo'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainsOrdersOwners','*',Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('DOMAIN_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	#-------------------------------------------------------------------------------
	$__USER = $GLOBALS['__USER'];
	#-------------------------------------------------------------------------------
	$IsPermission = Permission_Check('DomainsOrdersChangeContactData',(integer)$__USER['ID'],(integer)$DomainOrder['UserID']);
	#---------------------------------------------------------------------------
	switch(ValueOf($IsPermission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		#-------------------------------------------------------------------------------
		$DomainOrderID = (integer)$DomainOrder['ID'];
		#-------------------------------------------------------------------------------
		if(!In_Array($DomainOrder['StatusID'],Array('ForTransfer','OnTransfer')))
			return new gException('ORDER_IS_NOT_IN_TRANSFER','Домен должен быть в статусе "На переносе"/"Для переноса"');
		#-------------------------------------------------------------------------------
		$IsUpdate = DB_Update('DomainsOrders',Array('AuthInfo'=>$AuthInfo),Array('ID'=>$DomainOrder['ID']));
		if(Is_Error($IsUpdate))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		# реализация JBS-605 - активизируем задачи по регистрации доменов этого юзера
		$Where = Array(
				"`IsExecuted` = 'no'",
				"`IsActive` = 'no'",
				"`TypeID` = 'DomainTransfer'",
				SPrintF('`UserID` = %u',$DomainOrder['UserID'])
				);
		$Tasks = DB_Select('Tasks',Array('ID'),Array('Where'=>$Where));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Tasks)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			# no tasks
			break;
		case 'array':
			#-------------------------------------------------------------------------------
			# активируем задачи
			foreach($Tasks as $Task){
				#-------------------------------------------------------------------------------
				$IsUpdate = DB_Update('Tasks',Array('ExecuteDate'=>Time(),'Errors'=>0,'Result'=>'','IsActive'=>TRUE),Array('ID'=>$Task['ID']));
				if(Is_Error($IsUpdate))
					return ERROR | @Trigger_Error(500);
				#-------------------------------------------------------------------------------
			}
			#-------------------------------------------------------------------------------
			break;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return Array('Status'=>'Ok','DomainOrderID'=>$DomainOrderID);
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
