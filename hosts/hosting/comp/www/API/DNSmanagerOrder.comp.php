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
$Comment		=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DNSmanagerSchemeID)
	return new gException('DNS_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$DNSmanagerScheme = DB_Select('DNSmanagerSchemes',Array('ID','Name','ServersGroupID','HardServerID','IsActive'),Array('UNIQ','ID'=>$DNSmanagerSchemeID));
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
$Contract = DB_Select('Contracts',Array('ID','UserID'),Array('UNIQ','ID'=>$ContractID));
#---------------------------------------------------------------------------
switch(ValueOf($Contract)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('CONTRACT_NOT_FOUND','Договор не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('ContractRead',(integer)$__USER['ID'],(integer)$Contract['UserID']);
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
if($DNSmanagerScheme['HardServerID']){
	#-------------------------------------------------------------------------------
	$Where = SPrintF("`ID` = %u",$DNSmanagerScheme['HardServerID']);
	#-------------------------------------------------------------------------------
}else{
	#-------------------------------------------------------------------------------
	$Where = SPrintF("`ServersGroupID` = %u AND `IsDefault` = 'yes'",$DNSmanagerScheme['ServersGroupID']);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$Server = DB_Select('Servers',Array('ID','Params'),Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SERVER_NOT_DEFINED','Сервер размещения не определён');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
$Server = Current($Server);
#-------------------------------------------------------------------------------
$Password = SubStr(Md5(UniqID()),0,12);
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
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>52000,'ServerID'=>$Server['ID']));
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
return Array('Status'=>'Ok','DNSmanagerOrderID'=>$DNSmanagerOrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
