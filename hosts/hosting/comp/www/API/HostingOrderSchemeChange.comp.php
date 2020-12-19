<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Args');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = IsSet($Args)?$Args:Args();
#-------------------------------------------------------------------------------
$HostingOrderID = (integer) @$Args['HostingOrderID'];
$NewSchemeID    = (integer) @$Args['NewSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['User']['Orders']['Hosting'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','StatusDate','(SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`)) AS `ServersGroupID`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`)) AS `Params`','StatusID');
#-------------------------------------------------------------------------------
$HostingOrder = DB_Select('HostingOrdersOwners',$Columns,Array('UNIQ','ID'=>$HostingOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('HOSTING_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('HostingOrdersSchemeChange',(integer)$__USER['ID'],(integer)$HostingOrder['UserID']);
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
if(!In_Array($HostingOrder['StatusID'],Array('Active','Suspended')))
	return new gException('ORDER_NOT_ACTIVE','Тариф можно изменить только для активного или заблокированного заказа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OldScheme = DB_Select('HostingSchemes',Array('IsSchemeChange','IsReselling','QuotaDisk','Name','IsProlong','ID'),Array('UNIQ','ID'=>$HostingOrder['SchemeID']));
#-------------------------------------------------------------------------------
switch(ValueOf($OldScheme)){
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
if(!$OldScheme['IsSchemeChange'])
	return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа хостинга не позволяет смену тарифа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UniqID = UniqID('HostingSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','HostingSchemes',$HostingOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NewScheme = DB_Select($UniqID,Array('ID','ServersGroupID','IsSchemeChangeable','QuotaDisk','Name'),Array('UNIQ','ID'=>$NewSchemeID));
#-------------------------------------------------------------------------------
switch(ValueOf($NewScheme)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NEW_SCHEME_NOT_FOUND','Новый тарифный план не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$__USER['IsAdmin']){
	#-------------------------------------------------------------------------------
	$LastChange = Time() - $HostingOrder['StatusDate'];
	#-------------------------------------------------------------------------------
	# если снижение тарифа, то заодно проверяем прошло ли разрешённое время
	if($OldScheme['QuotaDisk'] > $NewScheme['QuotaDisk'] && $LastChange < IntVal($Settings['SchemeChangePeriod'])*3600){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Date/Remainder',(IntVal($Settings['SchemeChangePeriod'])*3600 - $LastChange));
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return new gException('TIME_NOT_EXPIRED',SPrintF('В меньшую сторону тариф можно менять только каждые %u час(ов). Вы сможете изменить тарифный план только через %s',$Settings['SchemeChangePeriod'],$Comp),new gException('TIME_NOT_EXPIRED_2','Однако, в случае необходимости, Вы можете обратиться в службу поддержки, и сотрудники сделают это до указанного времени'));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($HostingOrder['SchemeID'] == $NewScheme['ID'])
	return new gException('SCHEMES_MATCHED','Старый и новый тарифные планы совпадают');
#-------------------------------------------------------------------------------
if(!$NewScheme['IsSchemeChangeable'])
	return new gException('SCHEME_NOT_CHANGEABLE','Выбранный тариф не позволяет переход');
#-------------------------------------------------------------------------------
if($OldScheme['QuotaDisk'] > $NewScheme['QuotaDisk']){
	#-------------------------------------------------------------------------------
	if($OldScheme['IsProlong'])
		if(!$__USER['IsAdmin'] && !$Settings['IsAllowSchemeDecrease'])
			return new gException('QUOTA_DISK_ERROR','Дисковое пространство на новом тарифном плане, меньше чем на текущем. Для смены тарифа обратитесь в Центр Поддержки.');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($HostingOrder['ServersGroupID'] != $NewScheme['ServersGroupID'])
	return new gException('NEW_SCHEME_ANOTHER_SERVERS_GROUP','Выбранный тарифный план относиться к другой группе серверов');
#-------------------------------------------------------------------------------
$HostingOrderID = (integer)$HostingOrder['ID'];
#-------------------------------------------------------------------------------
#--------------------------TRANSACTION------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('HostingOrderSchemeChange'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$HostingOrder['UserID'],'TypeID'=>'HostingSchemeChange','Params'=>Array($HostingOrderID,$HostingOrder['SchemeID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($IsAdd)){
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
$IsUpdate = DB_Update('HostingOrders',Array('SchemeID'=>$NewSchemeID,'OldSchemeID'=>$OldScheme['ID']),Array('ID'=>$HostingOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'HostingOrders','StatusID'=>'SchemeChange','RowsIDs'=>$HostingOrderID,'Comment'=>SPrintF('Смена тарифа [%s->%s]',$OldScheme['Name'],$NewScheme['Name'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Comp)){
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
if(Is_Error(DB_Commit($TransactionID)))
	return ERROR | @Trigger_Error(500);
#----------------------END TRANSACTION------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
