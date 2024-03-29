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
//$ContractID     = (integer) @$Args['ContractID'];
//$IsUponConsider = (boolean) @$Args['IsUponConsider'];
$OrderID	= (integer) @$Args[3];

//Debug(print_r($Args,true));
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// список колонок которые юзеру не показываем
$Config = Config();
#-------------------------------------------------------------------------------
$Exclude = Array_Keys($Config['APIv2ExcludeColumns']);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
if($OrderID > 0)
	$Where[] = SPrintF('`OrderID` = %u',$OrderID);
#-------------------------------------------------------------------------------
$Columns = Array('*','(SELECT `Params` FROM `Servers` WHERE `DNSmanagerOrdersOwners`.`ServerID` = `Servers`.`ID`) AS `Params`','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `DNSmanagerOrdersOwners`.`ContractID`) AS `Customer`');
#-------------------------------------------------------------------------------
$DNSmanagerOrders = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('Where'=>$Where));
#-------------------------------------------------------------------------------
switch(ValueOf($DNSmanagerOrders)){
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
foreach($DNSmanagerOrders as $DNSmanagerOrder){
	#-------------------------------------------------------------------------------
	if($DNSmanagerOrder['Params']['SystemID'] == 'VmManager6_Hosting')
		$DNSmanagerOrder['Login'] = SPrintF('%s@%s',$DNSmanagerOrder['Login'],$DNSmanagerOrder['Params']['Domain']);
	#-------------------------------------------------------------------------------
	UnSet($DNSmanagerOrder['Params']);
	#-------------------------------------------------------------------------------
	// выпиливаем колонки
	foreach(Array_Keys($DNSmanagerOrder) as $Column)
		if(In_Array($Column,$Exclude))
			UnSet($DNSmanagerOrder[$Column]);
	#-------------------------------------------------------------------------------
	$Out[$DNSmanagerOrder['ID']] = $DNSmanagerOrder;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

