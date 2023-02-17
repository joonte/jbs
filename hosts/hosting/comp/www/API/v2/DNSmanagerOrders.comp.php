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
$Where = Array(SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
if($OrderID > 0)
	$Where[] = SPrintF('`OrderID` = %u',$OrderID);
#-------------------------------------------------------------------------------
$Columns = Array('*','(SELECT `Params` FROM `Servers` WHERE `DNSmanagerOrdersOwners`.`ServerID` = `Servers`.`ID`) AS `Params`');
#-------------------------------------------------------------------------------
$DNSmanagerOrders = DB_Select('DNSmanagerOrdersOwners',$Columns,Array('Where'=>$Where));
if(Is_Error($DNSmanagerOrders))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
foreach($DNSmanagerOrders as $DNSmanagerOrder){
	#-------------------------------------------------------------------------------
	if($DNSmanagerOrder['Params']['SystemID'] == 'VmManager6_Hosting')
		$DNSmanagerOrder['Login'] = SPrintF('%s@%s',$DNSmanagerOrder['Login'],$DNSmanagerOrder['Params']['Domain']);
	#-------------------------------------------------------------------------------
	UnSet($DNSmanagerOrder['Params']);
	#-------------------------------------------------------------------------------
	// выпиливаем колонку с примечанием админа
	UnSet($DNSmanagerOrder['AdminNotice']);
	#-------------------------------------------------------------------------------
	$Out[] = $DNSmanagerOrder;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

