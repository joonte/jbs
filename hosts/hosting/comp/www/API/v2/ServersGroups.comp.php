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
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array('Servers'=>Array(),'ServersGroups'=>Array());
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
// достаём все сервера и все группы
$Columns = Array(
		'ID','TemplateID','ServersGroupID','IsActive','IsDefault','Address',
		'(SELECT `SortID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ID`) AS `SortID`',
		'(SELECT `Name` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ID`) AS `ServersGroupsName`',
		'(SELECT `ServiceID` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ID`) AS `ServiceID`',
		'(SELECT `Comment` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ID`) AS `ServersGroupComment`'
		);
#-------------------------------------------------------------------------------
$Servers = DB_Select('Servers',$Columns,Array('Where'=>'(SELECT `Name` FROM `ServersGroups` WHERE `Servers`.`ServersGroupID` = `ID`) IS NOT NULL'));
#-------------------------------------------------------------------------------
switch(ValueOf($Servers)){
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
foreach($Servers as $Server){
	#-------------------------------------------------------------------------------
	// список серверов
	if(!In_Array($Server['ID'],$Out['Servers']))
		$Out['Servers'][$Server['ID']] = $Server;
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	// если группы нет - создаём массив
	if(!IsSet($Out['ServersGroups'][$Server['ServersGroupID']]))
		$Out['ServersGroups'][$Server['ServersGroupID']] = Array('Name'=>$Server['ServersGroupsName'],'ServiceID'=>$Server['ServiceID'],'Comment'=>$Server['ServersGroupComment']);
	#-------------------------------------------------------------------------------
	if(!In_Array($Server['ID'],$Out['ServersGroups'][$Server['ServersGroupID']]))
		$Out['ServersGroups'][$Server['ServersGroupID']][$Server['ID']] = $Server;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

