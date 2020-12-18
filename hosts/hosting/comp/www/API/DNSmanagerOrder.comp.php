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
$ContractID		= (integer) @$Args['ContractID'];
$DNSmanagerSchemeID	= (integer) @$Args['DNSmanagerSchemeID'];
$ViewArea		=  (string) @$Args['ViewArea'];
$Comment		=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DNSmanagerSchemeID)
	return new gException('DNS_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$DNSmanagerScheme = DB_Select('DNSmanagerSchemes',Array('*'),Array('UNIQ','ID'=>$DNSmanagerSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа DNSmanager не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$DNSmanagerScheme['IsActive'])
	return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа DNSmanager не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID','Params','IsActive','(SELECT `ServiceID` FROM `ServersGroups` WHERE `ServersGroups`.`ID` = `Servers`.`ServersGroupID`) AS `ServiceID`'),Array('UNIQ','ID'=>$DNSmanagerScheme['HardServerID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVERS_NOT_FOUND','Серверы для вторичного DNS не настроены');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if($Server['Params']['DefaultView'] != $DNSmanagerScheme['ViewArea'])
	if(!$ViewArea)
		return new gException('VIEW_NOT_SET','При выборе тарифа с собственным ДНС сервером, вы должны указать имя области');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, задан ли реселлер. если задан - используем дефолтовую вьюху
if($DNSmanagerScheme['Reseller'])
	$ViewArea = $DNSmanagerScheme['ViewArea'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!Preg_Match($Regulars['DnsDomain'],$ViewArea))
	return new gException('WRONG_VIEW_NAME','Неверное имя области, укажите домен третьего уровня');
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
#-------------------------------------------------------------------------------
$Password = Comp_Load('Passwords/Generator');
if(Is_Error($Password))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('DNSmanagerOrder'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = SPrintF("`ContractID` = %u AND `TypeID` = 'DNSmanagerRules'",$Contract['ID']);
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count < 1){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'DNSmanagerRules'));
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
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>$Server['ServiceID'],'ServerID'=>$Server['ID'],'Params'=>Array('ViewArea'=>$ViewArea)));
if(Is_Error($OrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Login = SPrintF('%s%s',$Server['Params']['Prefix'],$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IDNSmanagerOrder = Array(
			'OrderID'	=> $OrderID,
			'SchemeID'	=> $DNSmanagerScheme['ID'],
			'Login'		=> $Login,
			'Password'	=> $Password,
			);
#-------------------------------------------------------------------------------
$DNSmanagerOrderID = DB_Insert('DNSmanagerOrders',$IDNSmanagerOrder);
if(Is_Error($DNSmanagerOrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'DNSmanagerOrders','StatusID'=>'Waiting','RowsIDs'=>$DNSmanagerOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
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
		'Text'		=> SPrintF('Сформирована заявка на заказ вторичного DNS логин (%s), тариф (%s)',$Login,$DNSmanagerScheme['Name'])
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
return Array('Status'=>'Ok','DNSmanagerOrderID'=>$DNSmanagerOrderID,'ServiceOrderID'=>$DNSmanagerOrderID,'OrderID'=>$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
