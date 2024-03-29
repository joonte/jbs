<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
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
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array('ID','ServiceID','ContractID','OrderID','Summ','Amount','Comment','(SELECT `Measure` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `Measure`','(SELECT `Code` FROM `Services` WHERE `Services`.`ID` = `ServiceID`) as `ServiceCode`','(SELECT `Customer` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `Customer`','(SELECT `TypeID` FROM `Contracts` WHERE `Contracts`.`ID` = `ContractID`) as `TypeID`');
#-------------------------------------------------------------------------------
$Items = DB_Select('BasketOwners',$Columns,Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID']),'SortOn'=>'ContractID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Items)){
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
#-------------------------------------------------------------------------------
foreach($Items as $Item){
	#-------------------------------------------------------------------------------
	// провреяем есть ли этот договор в выхлопе
	if(!IsSet($Out[$Item['ContractID']]))
		$Out[$Item['ContractID']] = Array();
	#-------------------------------------------------------------------------------
	$Out[$Item['ContractID']][] = $Item;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
