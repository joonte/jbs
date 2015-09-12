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
$ExtraIPSchemeID= (integer) @$Args['ExtraIPSchemeID'];
$OrderType	=  (string) @$Args['OrderType'];	# тип заказа к которому цепляем IP
$DependOrderID	= (integer) @$Args['DependOrderID'];	# номер заказа к которому цепляем IP
$Comment	=  (string) @$Args['Comment'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$ExtraIPSchemeID)
	return new gException('ExtraIP_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
$ExtraIPScheme = DB_Select('ExtraIPSchemes',Array('ID','Name','IsActive','Params'),Array('UNIQ','ID'=>$ExtraIPSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план на выделенный IP адрес не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!$ExtraIPScheme['IsActive'])
	return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план на выделенный IP адрес не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$DependOrderID)
	return new gException('DEPEND_ORDER_NOT_FOUND','Выбранный заказ услуги, для добавления IP адреса, не найден');
#-------------------------------------------------------------------------------
# выбираем ServerID заказа
$Order = DB_Select('OrdersOwners',Array('ID','ServerID'),Array('UNIQ','ID'=>$DependOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($Order)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('ORDER_NOT_FOUND',SPrintF('Заказ (%s/%u) не найден. Обратитесь в службу поддержки пользователей.',$OrderType,$DependOrderID));
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
if(!In_Array($Order['ServerID'],$ExtraIPScheme['Params']['Servers']))
	return new gException('SCHEME_DOES_NOT_MATCH_WITH_ORDER','Выбранный тарифный план не подходит для ранее выбранной услуги');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = Comp_Load('Contracts/Fetch',$ContractID);
if(Is_Error($Contract))
	return ERROR | @Trigger_Error(500);
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
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ExtraIPOrder'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = SPrintF("`ContractID` = %u AND `TypeID` = 'ExtraIPRules'",$Contract['ID']);
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count < 1){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'ExtraIPRules'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'integer':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>50000,'ServerID'=>$Order['ServerID']));
if(Is_Error($OrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IExtraIPOrder = Array('OrderID'=>$OrderID,'SchemeID'=>$ExtraIPScheme['ID'],'DependOrderID'=>$DependOrderID);
#-------------------------------------------------------------------------------
$ExtraIPOrderID = DB_Insert('ExtraIPOrders',$IExtraIPOrder);
if(Is_Error($ExtraIPOrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ExtraIPOrders','StatusID'=>'Waiting','RowsIDs'=>$ExtraIPOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
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
		'Text'		=> SPrintF('Сформирована заявка на заказ выделенного IP адреса, по тарифу (%s)',$ExtraIPScheme['Name'])
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
return Array('Status'=>'Ok','ExtraIPOrderID'=>$ExtraIPOrderID,'ServiceOrderID'=>$ExtraIPOrderID,'OrderID'=>$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
