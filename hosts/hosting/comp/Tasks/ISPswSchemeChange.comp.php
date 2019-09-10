<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ISPswOrderID','ISPswSchemeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('libs/BillManager.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISPswOrder = DB_Select('ISPswOrdersOwners',Array('*','(SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `ISPswOrdersOwners`.`OrderID`) AS `ServerID`'),Array('UNIQ','ID'=>$ISPswOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswOrder)){
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
# достаём историю статусов, с сортировкой в обратном направлении
$Where = Array(
		'`ModeID` = "ISPswOrders"',
		SPrintF('`RowID` = %u',$ISPswOrderID)
		);
#-------------------------------------------------------------------------------
$StatusesHistory = DB_Select('StatusesHistory','StatusID',Array('Where'=>$Where,'SortOn'=>'ID'));
#-------------------------------------------------------------------------------
switch(ValueOf($StatusesHistory)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$StatusID = 'Active';
	#-------------------------------------------------------------------------------
	$Message  = 'История статусов не найдена, установлен статус по умолчанию';
	#-------------------------------------------------------------------------------
case 'array':
	#-------------------------------------------------------------------------------
	foreach($StatusesHistory as $Status){
		#-------------------------------------------------------------------------------
		Debug(SPrintF('[comp/Tasks/ISPswSchemeChange]: StatusID = %s',$Status['StatusID']));
		#-------------------------------------------------------------------------------
		if(In_Array($Status['StatusID'],Array('Active','Suspended')))
			$StatusID = $Status['StatusID'];
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
	# не найден подходящий статус - ставим активный, и сообщение
	if(!IsSet($StatusID)){
		#-------------------------------------------------------------------------------
		$StatusID = 'Active';
		$Message  = 'Предыдущий статус не найден, установлен статус по умолчанию';
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
$Server = DB_Select('Servers','*',Array('UNIQ','ID'=>$ISPswOrder['ServerID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Server)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	Debug(SPrintF('[comp/Tasks/ISPswSchemeChange]: found server: Address = %s; ID = %s',$Server['Address'],$Server['ID']));
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ISPswOrderID = (integer)$ISPswOrder['ID'];
#-------------------------------------------------------------------------------
$ISPswNewScheme = DB_Select('ISPswSchemes','*',Array('UNIQ','ID'=>$ISPswOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($ISPswNewScheme)){
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
$SchemeChange = BillManager_Scheme_Change($Server,$ISPswNewScheme);
#-------------------------------------------------------------------------------
switch(ValueOf($SchemeChange)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	#-------------------------------------------------------------------------------
	$IsUpdate = DB_Update('ISPswOrders',Array('SchemeID'=>$ISPswSchemeID),Array('ID'=>$ISPswOrderID));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>$StatusID,'RowsIDs'=>$ISPswOrderID,'Comment'=>$SchemeChange->String));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $ISPswOrder['UserID'],
				'PriorityID'	=> 'Error',
				'Text'	=> SPrintF('Не удалось сменить тарифный план заказу ПО ISPsystem (%s) в автоматическом режиме, причина (%s)',$ISPswOrder['IP'],$SchemeChange->String),
				'IsReaded'	=> FALSE
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return TRUE;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
case 'true':
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'ISPswOrders','StatusID'=>$StatusID,'RowsIDs'=>$ISPswOrderID,'Comment'=>'Тарифный план изменен'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($Comp)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'array':
		#-------------------------------------------------------------------------------
		$Event = Array(
				'UserID'	=> $ISPswOrder['UserID'],
				'PriorityID'	=> 'Hosting',
				'Text'	=> SPrintF('Тарифный план заказа ПО ISPsystem (%s) изменён на (%s)',$ISPswOrder['IP'],$ISPswNewScheme['Name']),
				);
		$Event = Comp_Load('Events/EventInsert',$Event);
		if(!$Event)
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		$GLOBALS['TaskReturnInfo'] = Array($ISPswOrder['IP'],$ISPswNewScheme['Name']);
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
#-------------------------------------------------------------------------------
?>
