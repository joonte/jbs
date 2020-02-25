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
$ProxyOrder = DB_Select('ProxyOrdersOwners',Array('*','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `ServerID`','(SELECT `Params` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `Params`'),Array('UNIQ','ID'=>$ProxyOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyOrder)){
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
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ProxyScheme = DB_Select('ProxySchemes','*',Array('UNIQ','ID'=>$ProxyOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyScheme)){
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
// надо достать количество дней на которое заказывается услуга
$Consider = DB_Select('OrdersConsider','*',Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$ProxyOrder['OrderID']),'IsDesc'=>TRUE,'SortOn'=>'ID','Limits'=>Array(0,1)));
#-------------------------------------------------------------------------------
switch(ValueOf($Consider)){
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
$Args = Array($ProxyScheme,$ProxyOrder,IntVal($Consider['DaysRemainded']));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsCreate = Call_User_Func_Array(Array($ClassProxyServer,'Create'),$Args);
#-------------------------------------------------------------------------------
switch(ValueOf($IsCreate)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsCreate;
case 'array':
	#-------------------------------------------------------------------------------
	// прописываем данные от прокси сервера
	$IsUpdate = DB_Update('ProxyOrders',Array('Login'=>$IsCreate['user'],'Password'=>$IsCreate['pass'],'Host'=>$IsCreate['host'],'Port'=>$IsCreate['port'],'ProtocolType'=>(($IsCreate['type'] == 'http')?'https':'socks5'),'IP'=>$IsCreate['ip']),Array('ID'=>$ProxyOrderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ProxyOrders','StatusID'=>'Active','RowsIDs'=>$ProxyOrder['ID'],'Comment'=>'Заказ создан на сервере'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $ProxyOrder['UserID'],
				'PriorityID'	=> 'Hosting',
				'Text'		=> SPrintF('Заказ прокси-сервера [%s] создан, с тарифным планом (%s), идентификатор пакета (%s)',$ProxyOrder['OrderID'],$ProxyScheme['Name'],$ProxyScheme['PackageID'])
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array(($ClassProxyServer->Settings['Address'])=>Array($ProxyOrder['Login'],$ProxyScheme['Name']));
		#-------------------------------------------------------------------------------
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
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
