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
$ISPswSchemeID	= (integer) @$Args['ISPswSchemeID'];
$IP		=  (string) @$Args['IP'];
$Comment	=  (string) @$Args['Comment'];
$DependOrderID	= (integer) @$Args['DependOrderID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','libs/BillManager.php','libs/Server.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ContractID)
	return new gException('CONTRACT_NOT_DEFINED','Не выбран договор');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// новый интерфейс не присывлает IP, только номер зависимого заказа.
// и это правильно, надо по нему определять IP
if($DependOrderID && !Is_Null($DependOrderID)){
	#-------------------------------------------------------------------------------
	// находим тип услуги
	$DependService = DB_Select('OrdersOwners','(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `OrdersOwners`.`ServiceID`) AS `Code`',Array('UNIQ','Where'=>SPrintF('`ID` = %u',$DependOrderID)));
	switch(ValueOf($DependService)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('SERVICE_NOT_FOUND',SPrintF('Не удалось найти сервис для зависимого зaказа #%u',$DependOrderID));
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	// достаём IP услуги
	$Order = DB_Select(SPrintF('%sOrdersOwners',$DependService['Code']),'IP',Array('UNIQ','Where'=>SPrintF('`OrderID` = %u',$DependOrderID)));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Order)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return new gException('ORDER_NOT_FOUND',SPrintF('Не удалось найти IP для зависимого зaказа #%u',$DependOrderID));
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$IP = $Order['IP'];
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$IP)
	return new gException('NO_IP_OR_ORDER','Не выбран заказ или не задан IP адрес');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ServerSettings = SelectServerSettingsByService(51000);
#-------------------------------------------------------------------------------
if(!Is_Array($ServerSettings))
	return SelectServerErrorMessage(51000);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Contract = Comp_Load('Contracts/Fetch',$ContractID);
if(Is_Error($Contract))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Regulars = Regulars();
#-------------------------------------------------------------------------------
if(!$ISPswSchemeID)
	return new gException('ISPsw_SCHEME_NOT_DEFINED','Тарифный план не выбран');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISPswScheme = DB_Select('ISPswSchemes',Array('*'),Array('UNIQ','ID'=>$ISPswSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('SCHEME_NOT_FOUND','Выбранный тарифный план заказа ПО ISPsystem не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$ISPswScheme['IsActive'])
	return new gException('SCHEME_NOT_ACTIVE','Выбранный тарифный план заказа ПО ISPsystem не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем IP адрес который нам воткнули, если это внешний заказ
if(!$ISPswScheme['IsInternal']){
	#-------------------------------------------------------------------------------
	$ISPswInfo = Array(
				#-------------------------------------------------------------------------------
				'IP'		=> $IP,
				'pricelist_id'	=> $ISPswScheme['pricelist_id'],
				'period'	=> $ISPswScheme['period']
				#-------------------------------------------------------------------------------
			);
	#-------------------------------------------------------------------------------
	if(!BillManager_Check_ISPsystem_IP($ServerSettings, $ISPswInfo))
		return new gException('ISPsw_IP_ADDRESS_IN_USE',SPrintF('Для указанного IP адреса [%s] уже есть лицензия такого типа. За более подробной информацией, обратитесь в службу поддержки пользователей',$IP));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверка - есть ли заказ от этого пользователя на этот IP
if($ISPswScheme['IsInternal']){
	#-------------------------------------------------------------------------------
	$Where = Array(
			'`StatusID` != "Deleted"',
			SPrintF('`IP` = "%s"',$IP),
			SPrintF('`UserID` = %u',$Contract['UserID']),
			SPrintF('(SELECT `SoftWareGroup` FROM `ISPswSchemes` WHERE `ISPswOrdersOwners`.`SchemeID` = `ISPswSchemes`.`ID`) = %u',$ISPswScheme['SoftWareGroup'])
			);
	#-------------------------------------------------------------------------------
	$Count = DB_Count('ISPswOrdersOwners',Array('Where'=>$Where));
	if(Is_Error($Count))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	if($Count)
		return new gException('SOFTWARE_EXISTS','Для данного заказа VPS/DS уже существует заказ такого программного обеспечения ISPsystem. Для продления пробной лицензии - просто смените тарифный план, для активации заказа - оплатите его.');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# проверяем, если тарифный плане не поддерживает продление, то заказывать его повторно для
# этого же IP юзер не может, т.к. это не имеет смысла. тем самым срезаются попытки
# повторного заказа триальных и вечных лицензий - т.к. и те и другие не продлеваются
$Where = Array(
		'`StatusID` = "Deleted" OR `StatusID` = "Suspended"',
		'(SELECT `IsProlong` FROM `ISPswSchemesOwners` WHERE `ISPswSchemesOwners`.`ID` = `ISPswOrdersOwners`.`SchemeID`) = "no"',
		SPrintF('`IP` = "%s"',$IP),
		SPrintF('`SchemeID` = %u',$ISPswSchemeID),
		SPrintF('`UserID` = %u',$Contract['UserID']),
		);
#-------------------------------------------------------------------------------
$Count = DB_Count('ISPswOrdersOwners',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count)
	return new gException('SOFTWARE_EXISTS','Нельзя заказать вечную или триальную лицензию дважды для одного и того же IP адреса.');
#-------------------------------------------------------------------------------
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
#-------------------------TRANSACTION-------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('ISPswOrder'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Where = SPrintF("`ContractID` = %u AND `TypeID` = 'ISPswRules'",$Contract['ID']);
#-------------------------------------------------------------------------------
$Count = DB_Count('ContractsEnclosures',Array('Where'=>$Where));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count < 1){
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/ContractEnclosureMake',Array('ContractID'=>$Contract['ID'],'TypeID'=>'ISPswRules'));
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
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderID = DB_Insert('Orders',Array('ContractID'=>$Contract['ID'],'ServiceID'=>51000,'ServerID'=>$ServerSettings['ID'],'Params'=>'','DependOrderID'=>$DependOrderID));
if(Is_Error($OrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IISPswOrder = Array(
			#-------------------------------------------------------------------------------
			'OrderID'	=> $OrderID,
			'SchemeID'	=> $ISPswScheme['ID'],
			'IP'		=> $IP,
			);
#-------------------------------------------------------------------------------
$ISPswOrderID = DB_Insert('ISPswOrders',$IISPswOrder);
if(Is_Error($ISPswOrderID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>'Waiting','RowsIDs'=>$ISPswOrderID,'Comment'=>($Comment)?$Comment:'Заказ создан и ожидает оплаты'));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#return ERROR | @Trigger_Error(400);
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
		'Text'		=> SPrintF('Сформирована заявка на заказ ПО, тариф (%s)',$ISPswScheme['Name'])
		);
#-------------------------------------------------------------------------------
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#----------------------END TRANSACTION------------------------------------------
return Array('Status'=>'Ok','ISPswOrderID'=>$ISPswOrderID,'ServiceOrderID'=>$ISPswOrderID,'OrderID'=>$OrderID);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
