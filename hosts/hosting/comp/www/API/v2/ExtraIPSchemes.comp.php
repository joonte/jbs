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
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# составляем список серверов на которых можно добавлять IP адреса
$ExtraIPSchemes = DB_Select('ExtraIPSchemes',Array('ID','Params'),Array('Where'=>"`IsActive` = 'yes'"));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('NO_IP_SCHEMES','Нет ни одного тарифа на выделенные IP адреса');
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}       
#-------------------------------------------------------------------------------
$ServerIDs = Array();
#-------------------------------------------------------------------------------
foreach($ExtraIPSchemes as $ExtraIPScheme)
	foreach($ExtraIPScheme['Params']['Servers'] as $iServerID)
		if(!In_Array($iServerID,$ServerIDs))
			$ServerIDs[] = $iServerID;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список заказов к которым можно заказать IP адрес
$DependOrders = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$__USER = $GLOBALS['__USER'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# Where общее для Hosting/VPS/DS
$Where = Array(
		SPrintF('`UserID` = %u',$__USER['ID']),
		SPrintF('`ServerID` IN (%s)',Implode(',',$ServerIDs)),
		"`StatusID` = 'Active' OR `StatusID` = 'Waiting' OR `StatusID` = 'Suspended'"
		);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# create select, using UserID for HostingOrders
$Columns = Array('ID','Login','OrderID','ServerID','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `HostingOrdersOwners`.`OrderID`)) AS `Address`');
#-------------------------------------------------------------------------------
$HostingOrders = DB_Select('HostingOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($HostingOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($HostingOrders as $HostingOrder){
		#-------------------------------------------------------------------------------
		if(!IsSet($DependOrders[$HostingOrder['ServerID']]))
			$DependOrders[$HostingOrder['ServerID']] = Array();
		#-------------------------------------------------------------------------------
		$DependOrderID = $HostingOrder['OrderID'];
		#-------------------------------------------------------------------------------
		$DependOrders[$HostingOrder['ServerID']][$DependOrderID] = SPrintF('Хостинг: %s [%s]',$HostingOrder['Login'],$HostingOrder['Address']);
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
# create select, using UserID for VPSOrders
$Columns = Array('ID','Login','OrderID','ServerID','(SELECT `Address` FROM `Servers` WHERE `Servers`.`ID` = (SELECT `ServerID` FROM `OrdersOwners` WHERE `OrdersOwners`.`ID` = `VPSOrdersOwners`.`OrderID`)) AS `Address`');
#-------------------------------------------------------------------------------
$VPSOrders = DB_Select('VPSOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($VPSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($VPSOrders as $VPSOrder){
		#-------------------------------------------------------------------------------
		if(!IsSet($DependOrders[$VPSOrder['ServerID']]))
			$DependOrders[$VPSOrder['ServerID']] = Array();
		#-------------------------------------------------------------------------------
		$DependOrderID = $VPSOrder['OrderID'];
		#-------------------------------------------------------------------------------
		$DependOrders[$VPSOrder['ServerID']][$DependOrderID] = SPrintF('VPS: %s [%s]',$VPSOrder['Login'],$VPSOrder['Address']);
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
# create select, using UserID for DSOrders
$Columns = Array('ID','IP','OrderID','ServerID','(SELECT `Name` FROM `DSSchemes` WHERE `DSSchemes`.`ID` = `SchemeID`) as `Name`');
#-------------------------------------------------------------------------------
$DSOrders = DB_Select('DSOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DSOrders)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	# No more...
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($DSOrders as $DSOrder){
		#-------------------------------------------------------------------------------
		if(!IsSet($DependOrders[$DSOrder['ServerID']]))
			$DependOrders[$DSOrder['ServerID']] = Array();
		#-------------------------------------------------------------------------------
		$DependOrderID = $DSOrder['OrderID'];
		#-------------------------------------------------------------------------------
		$DependOrders[$DSOrder['ServerID']][$DependOrderID] = SPrintF('Сервер: %s [%s]',$DSOrder['IP'],$DSOrder['Name']);
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
// выхлоп
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		'(`UserID` = @local.__USER_ID OR FIND_IN_SET(`GroupID`,@local.__USER_GROUPS_PATH))',
		'`IsActive` = "yes"',
		);
#-------------------------------------------------------------------------------
$ExtraIPSchemes = DB_Select('ExtraIPSchemesOwners','*',Array('Where'=>$Where,'SortOn'=>Array('SortID','PackageID')));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPSchemes)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $Out;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
foreach($ExtraIPSchemes as $ExtraIPScheme){
	#-------------------------------------------------------------------------------
	// заказы для которыз можно заказать этот тариф
	$Orders = Array();
	#-------------------------------------------------------------------------------
	// перебираем сервера для которых этот тариф
	foreach($ExtraIPScheme['Params']['Servers'] as $ServerID)
		if(In_Array($ServerID,Array_Keys($DependOrders)))
			foreach(Array_Keys($DependOrders[$ServerID]) as $Key)
				$Orders[$Key] = $DependOrders[$ServerID][$Key];
	#-------------------------------------------------------------------------------
	if(SizeOf($Orders) > 0)
		$ExtraIPScheme['DependOrders'] = $Orders;
	#-------------------------------------------------------------------------------
	$Out[$ExtraIPScheme['ID']] = $ExtraIPScheme;
	#-------------------------------------------------------------------------------
	//Debug(print_r($DependOrders,true));
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

