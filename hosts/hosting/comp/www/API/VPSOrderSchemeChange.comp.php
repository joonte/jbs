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
$VPSOrderID	= (integer) @$Args['VPSOrderID'];
$NewSchemeID	= (integer) @$Args['NewSchemeID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Columns = Array('ID','UserID','SchemeID','(SELECT `ServersGroupID` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `ServersGroupID`','(SELECT `Params` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `Params`','StatusID','StatusDate');
#-------------------------------------------------------------------------------
$VPSOrder = DB_Select('VPSOrdersOwners',$Columns,Array('UNIQ','ID'=>$VPSOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('VPS_ORDER_NOT_FOUND','Выбранный заказ не найден');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
$IsPermission = Permission_Check('VPSOrdersSchemeChange',(integer)$__USER['ID'],(integer)$VPSOrder['UserID']);
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
if($VPSOrder['StatusID'] != 'Active')
	return new gException('ORDER_NO_ACTIVE','Заказ виртуального сервера не активен');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OldScheme = DB_Select('VPSSchemes',Array('ID','IsSchemeChange','IsReselling','disklimit','Name','CostDay'),Array('UNIQ','ID'=>$VPSOrder['SchemeID']));
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
	return new gException('SCHEME_NOT_ALLOW_SCHEME_CHANGE','Тарифный план заказа виртуального сервера не позволяет смену тарифа');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$UniqID = UniqID('VPSSchemes');
#-------------------------------------------------------------------------------
$Comp = Comp_Load('Services/Schemes','VPSSchemes',$VPSOrder['UserID'],Array('Name','ServersGroupID'),$UniqID);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NewScheme = DB_Select($UniqID,Array('ID','ServersGroupID','IsSchemeChangeable','disklimit','Name','CostDay'),Array('UNIQ','ID'=>$NewSchemeID));
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
	$LastChange = Time() - $VPSOrder['StatusDate'];
	#-------------------------------------------------------------------------------
	// прошло менее суток и цена старого тарифа больше или равна цене нового тарифа
	if($LastChange < 86400 && $OldScheme['CostDay'] >= $NewScheme['CostDay']){
		#-------------------------------------------------------------------------------
		$Comp = Comp_Load('Formats/Date/Remainder',$LastChange);
		if(Is_Error($Comp))
			return ERROR | @Trigger_Error(500);
		#-------------------------------------------------------------------------------
		return new gException('TIME_NOT_EXPIRED',SPrintF('Тарифный план можно менять только 1 раз в сутки, сменить тарифный план можно только через %s, однако, в случае необходимости Вы можете обратиться в службу поддержки',$Comp));
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if($VPSOrder['SchemeID'] == $NewScheme['ID'])
	return new gException('SCHEMES_MATCHED','Старый и новый тарифные планы совпадают');
#-------------------------------------------------------------------------------
if(!$NewScheme['IsSchemeChangeable'])
	return new gException('SCHEME_NOT_CHANGEABLE','Выбранный тариф не позволяет переход');
#-------------------------------------------------------------------------------
if($OldScheme['disklimit'] > $NewScheme['disklimit']){
	#-------------------------------------------------------------------------------
	if(In_Array($VPSOrder['Params']['SystemID'],Array('VmManager5_KVM','VmManager6_Hosting')))
		return new gException('CANNOT_RESIZE_DISK','Система виртуализации KVM не предусматривает уменьшения размера диска. Изменить тариф в меньшую сторону невозможно.');
	#-------------------------------------------------------------------------------
	if(!$__USER['IsAdmin'])
		return new gException('QUOTA_DISK_ERROR','Для смены тарифа обратитесь в Центр Поддержки');
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
if($VPSOrder['ServersGroupID'] != $NewScheme['ServersGroupID'])
	return new gException('NEW_SCHEME_ANOTHER_SERVERS_GROUP','Выбранный тарифный план относиться к другой группе серверов');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$VPSOrderID = (integer)$VPSOrder['ID'];
#-------------------------------------------------------------------------------
#--------------------------TRANSACTION------------------------------------------
if(Is_Error(DB_Transaction($TransactionID = UniqID('VPSOrderSchemeChange'))))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$VPSOrder['UserID'],'TypeID'=>'VPSSchemeChange','Params'=>Array($VPSOrderID,$VPSOrder['SchemeID'])));
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
$IsUpdate = DB_Update('VPSOrders',Array('SchemeID'=>$NewSchemeID),Array('ID'=>$VPSOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Comp = Comp_Load('www/API/StatusSet',Array('ModeID'=>'VPSOrders','StatusID'=>'SchemeChange','RowsIDs'=>$VPSOrderID,'Comment'=>"Смена тарифа [".$OldScheme['Name']."->".$NewScheme['Name']."]"));
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
$IsUpdate = DB_Update('VPSOrders',Array('SchemeID'=>$NewSchemeID,'OldSchemeID'=>$OldScheme['ID']),Array('ID'=>$VPSOrderID));
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
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
