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
$ProxyOrderID	= (integer) @$Args['ProxyOrderID'];
$ProtocolType	=  (string) @$Args['ProtocolType'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/Server.php','classes/ProxyServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ProxyOrder = DB_Select('ProxyOrdersOwners',Array('UserID','ContractID','OrderID','ProtocolType','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ProxyOrdersOwners`.`OrderID`) AS `ServerID`','StatusID','SchemeID'),Array('UNIQ','ID'=>$ProxyOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	# No more...
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ProxyOrdersSettingsChange',(integer)$__USER['ID'],(integer)$ProxyOrder['UserID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'false':
	return ERROR | @Trigger_Error(700);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!In_Array($ProxyOrder['StatusID'],Array('Active')))
	return new gException('ORDER_NOT_ACTIVE','Протокол можно изменить только для активного заказа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($ProtocolType == $ProxyOrder['ProtocolType'])
	return new gException('ALREADY_USED_PROTOCOL',SPrintF('Протокол "%s" уже используется для этого заказа',StrToUpper($ProtocolType)));
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ProxyServer = new ProxyServer();
#-------------------------------------------------------------------------------
$IsSelected = $ProxyServer->Select((integer)$ProxyOrder['ServerID']);
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
$IsProto = $ProxyServer->SettingsChange((integer)$ProxyOrder['OrderID'],($ProxyOrder['ProtocolType'] == 'https')?'socks':'http');
switch(ValueOf($IsProto)){
case 'error':
	return new gException('SERVER_QUERY_ERROR','Ошибка запроса на сервер');
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update('ProxyOrders',Array('ProtocolType'=>(($ProxyOrder['ProtocolType'] == 'socks5')?'https':'socks5')),Array('ID'=>$ProxyOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
