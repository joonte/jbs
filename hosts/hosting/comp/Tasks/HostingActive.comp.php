<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','HostingOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/HostingServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
// проверяем, нет ли невыполеннных задач для этого заказа
// select * from Tasks where TypeID IN ('HostingSuspend','HostingSchemeChange') AND IsExecuted = 'no' AND IsActive = 'yes';
$Where = Array(
		"`TypeID` IN ('HostingSuspend','HostingSchemeChange')",
		"`IsExecuted` = 'no'","`IsActive` = 'yes'"
		);
$Tasks = DB_Select('Tasks',Array('ID','Params'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Tasks)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Tasks as $Task){
		#-------------------------------------------------------------------------------
		if(IsSet($Task['Params']['ID']) && $Task['Params']['ID'] == $HostingOrderID){
			#-------------------------------------------------------------------------------
			$CacheID = SPrintF('HostingActive-%u',$Task['ID']);
			#-------------------------------------------------------------------------------
			$Cache = CacheManager::get($CacheID);
			if(!$Cache)
				$Cache = 1;
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = SPrintF('Есть невыполненные задачи, перенос выполнения на более поздний срок, попытка #%u',$Cache);
			#-------------------------------------------------------------------------------
			Debug(SPrintF('[comp/Tasks/HostingActive]: невыполненная задача %u, перенос на более поздний срок, попытка #%u',$Task['ID'],$Cache));
			#-------------------------------------------------------------------------------
			# Сохраняем количество выполнений
			CacheManager::add($CacheID,$Cache+1,7200);
			#-------------------------------------------------------------------------------
			return Time() + 60 * $Cache;
			#-------------------------------------------------------------------------------
		}
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
$Columns = Array(
		'ID','UserID','Login','Domain',
		'(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`) AS `ServerID`',
		'(SELECT `IsReselling` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `IsReselling`',
		'(SELECT `Name` FROM `HostingSchemes` WHERE `HostingSchemes`.`ID` = `HostingOrdersOwners`.`SchemeID`) as `SchemeName`'
		);
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$ClassHostingServer = new HostingServer();
	#-------------------------------------------------------------------------------
	$IsSelected = $ClassHostingServer->Select((integer)$HostingOrder['ServerID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'true':
		#-------------------------------------------------------------------------------
		$IsActive = $ClassHostingServer->Active($HostingOrder['Login'],$HostingOrder['IsReselling']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsActive)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return $IsActive;
		case 'true':
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $HostingOrder['UserID'],
					'PriorityID'	=> 'Hosting',
					'Text'		=> SPrintF('Заказ хостинга логин [%s], домен (%s), тариф (%s) активирован на сервере (%s)',$HostingOrder['Login'],$HostingOrder['Domain'],$HostingOrder['SchemeName'],$ClassHostingServer->Settings['Address'])
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array(($ClassHostingServer->Settings['Address'])=>Array($HostingOrder['Login'],$HostingOrder['SchemeName']));
			#-------------------------------------------------------------------------------
			return TRUE;
			#-------------------------------------------------------------------------------
		default:
			return ERROR | @Trigger_Error(101);
		}
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
