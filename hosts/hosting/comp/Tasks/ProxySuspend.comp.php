<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ProxyOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ProxyServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ProxyOrder = DB_Select('ProxyOrdersOwners',Array('ID','UserID','SchemeID','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `ServerID`','Login','(SELECT `Name` FROM `ProxySchemes` WHERE `ProxySchemes`.`ID` = `ProxyOrdersOwners`.`SchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$ProxyOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$ClassProxyServer = new ProxyServer();
	#-------------------------------------------------------------------------------
	$IsSelected = $ClassProxyServer->Select((integer)$ProxyOrder['ServerID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($IsSelected)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'true':
		#-------------------------------------------------------------------------------
		$IsSuspend = $ClassProxyServer->Suspend($ProxyOrder['Login'],$ProxyOrder['IsReselling']);
		#-------------------------------------------------------------------------------
		switch(ValueOf($IsSuspend)){
		case 'error':
			return ERROR | @Trigger_Error(500);
		case 'exception':
			return $IsSuspend;
		case 'true':
			#-------------------------------------------------------------------------------
			$Event = Array(
					'UserID'	=> $ProxyOrder['UserID'],
					'PriorityID'	=> 'Hosting',
					'Text'		=> SPrintF('Заказ прокси-сервера [%s:%u], тариф (%s) заблокирован',$ProxyOrder['Host'],$ProxyOrder['Port'],$ProxyOrder['SchemeName'])
					);
			$Event = Comp_Load('Events/EventInsert',$Event);
			if(!$Event)
				return ERROR | @Trigger_Error(500);
			#-------------------------------------------------------------------------------
			$GLOBALS['TaskReturnInfo'] = Array(($ClassProxyServer->Settings['Address'])=>Array($ProxyOrder['Login'],$ProxyOrder['SchemeName']));
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
