<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ContractID	= (integer) @$Args['ContractID'];
$ProxySchemeID	= (integer) @$Args['ProxySchemeID'];
$Comment	=  (string) @$Args['Comment'];
$DependOrderID	= (integer) @$Args['DependOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ProxySchemeID)
	return new gException('PROXY_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$ProxyScheme = DB_Select('ProxySchemes',Array('*'),Array('UNIQ','ID'=>$ProxySchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ProxyScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа Proxy не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$ProxyScheme['IsActive'])
	return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа Proxy не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID','Params','IsActive','(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) AS `ServiceID`'),Array('UNIQ','ID'=>$ProxyScheme['HardServerID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('PROXY_SERVERS_NOT_FOUND','Прокси-сервера не настроены');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = Comp_Load('Contracts/Fetch',$ContractID);
if(Is_Error($Contract))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ContractsRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ProxyOrder'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = SPrintF("`ContractID` = %u AND `TypeID` = 'ProxyRules'",$Contract['ID']);
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count < 1){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'ProxyRules'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'integer':
		# No more...
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
}
#-------------------------------------------------------------------------------
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>$Server['ServiceID'],'ServerID'=>$Server['ID'],'Params'=>'','DependOrderID'=>$DependOrderID));
if(Is_Error($OrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IProxyOrder = Array(
			'ProtocolType'	=> 'https',
			'OrderID'	=> $OrderID,
			'SchemeID'	=> $ProxyScheme['ID'],
			);
#-------------------------------------------------------------------------------
$ProxyOrderID = DB_Insert('ProxyOrders',$IProxyOrder);
if(Is_Error($ProxyOrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ProxyOrders','StatusID'=>'Waiting','RowsIDs'=>$ProxyOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Comp;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $Contract['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF('Сформирована заявка на прокси-сервер, номер заказа (%s), тариф (%s)',$OrderID,$ProxyScheme['Name'])
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#----------------------END TRANSACTION------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok','ProxyOrderID'=>$ProxyOrderID,'ServiceOrderID'=>$ProxyOrderID,'OrderID'=>$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
