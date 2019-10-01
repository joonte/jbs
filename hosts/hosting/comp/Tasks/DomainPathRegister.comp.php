<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','DomainOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Columns = Array('ID','UserID','ServerID','DomainName','ProfileID','PersonID','StatusID','(SELECT `Name` FROM `DomainSchemes` WHERE `DomainSchemes`.`ID` = `DomainOrdersOwners`.`SchemeID`) as `DomainZone`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = `DomainOrdersOwners`.`ServerID`) as `Params`');
#-------------------------------------------------------------------------------
$DomainOrder = DB_Select('DomainOrdersOwners',$Columns,Array('UNIQ','ID'=>$DomainOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($DomainOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	// могли удалить даже удалённый заказ
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# JBS-663 - домен может быть уже удалён
if($DomainOrder['StatusID'] == 'Deleted')
	return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($DomainOrder['Params']['Name'])=>Array(SPrintF('%s.%s',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])));
#-------------------------------------------------------------------------------
$IsDefined = ($DomainOrder['ProfileID'] || $DomainOrder['PersonID']);
#-------------------------------------------------------------------------------
if(!$IsDefined){
	#-------------------------------------------------------------------------------
	if(Time() - $Task['CreateDate'] > 86400){
		#-------------------------------------------------------------------------------
		# add ticket to user, about it's exception
		$Clause = DB_Select('Clauses','*',Array('UNIQ','Where'=>"`Partition`='CreateTicket/DOMAIN_OWNER_NOT_DEFINED'"));
		#-------------------------------------------------------------------------------
		switch(ValueOf($Clause)){
		case 'array':
			#-------------------------------------------------------------------------------
			$CompParameters = Array(
						'Theme'		=> SPrintF('%s %s.%s',$Clause['Title'],$DomainOrder['DomainName'],$DomainOrder['DomainZone']),
						'TargetGroupID'	=> 3100000,
						'TargetUserID'	=> 100,
						'PriorityID'	=> 'Low',
						'Message'	=> Trim(Strip_Tags($Clause['Text'])),
						'UserID'	=> $DomainOrder['UserID'],
						'Flags'		=> 'CloseOnSee'
						);
			#-------------------------------------------------------------------------------
			# set variable, for post-executing task
			$GLOBALS['TaskReturnArray'] = Array('CompName' => 'www/API/TicketEdit', 'CompParameters' => $CompParameters);
			#-------------------------------------------------------------------------------
		}
		#-------------------------------------------------------------------------------
		#Debug("[comp/Tasks/DomainPathRegister]: " . print_r($GLOBALS['TaskReturnArray'],true));
		#-------------------------------------------------------------------------------
		#return new gException('DOMAIN_OWNER_NOT_DEFINED','Владелец домена не определён более 24 часов');
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $DomainOrder['UserID'],
				'PriorityID'	=> 'Warning',
				'Text'		=> SPrintF('Владелец для заказа домена (%s.%s) не определён более 24 часов. Ожидание перед повтором проверки 31 сутки',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		# откладываем на месяц, через 30 дней будет событие администратору - на ручной разбор
		return 31 * 24 * 3600;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	$Event = Array(
			'UserID'	=> $DomainOrder['UserID'],
			'PriorityID'	=> 'Warning',
			'Text'		=> SPrintF('Владелец для заказа домена (%s.%s) не определён. Ожидание перед повтором проверки 1 сутки',$DomainOrder['DomainName'],$DomainOrder['DomainZone'])
			);
	$Event = Comp_Load('Events/EventInsert',$Event);
	if(!$Event)
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	return 24 * 3600;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers','Params',Array('UNIQ','ID'=>$DomainOrder['ServerID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
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
$Config = Config();
#-------------------------------------------------------------------------------
$StatusID = ($Server['Params']['IsSupportContracts'] && $Server['Params']['UseContractRegister'] && $DomainOrder['ProfileID']?'ForContractRegister':'ForRegister');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DomainOrders','StatusID'=>$StatusID,'RowsIDs'=>$DomainOrder['ID'],'Comment'=>'Алгоритм регистрации доменного имени выбран'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	return TRUE;
default:
	return ERROR | @Trigger_Error(101);

}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
