<?php


#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','RegisterDate','Name','EnterDate','IsManaged',
		'(SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID` = `Users`.`ID` AND `InvoicesOwners`.`StatusID` = "Payed") AS `aInvoices`',
		'(SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID` = `Users`.`ID` AND `InvoicesOwners`.`StatusID` = "Payed" AND BEGIN_MONTH() > `InvoicesOwners`.`StatusDate` AND `InvoicesOwners`.`StatusDate` >= BEGIN_PREVIOS_MONTH()) AS `pInvoices`',
		'(SELECT SUM(`Summ`) FROM `InvoicesOwners` WHERE `InvoicesOwners`.`UserID` = `Users`.`ID` AND `InvoicesOwners`.`StatusID` = "Payed" AND `InvoicesOwners`.`StatusDate` >= BEGIN_MONTH()) AS `cInvoices`'
		);
#-------------------------------------------------------------------------------
$Users = DB_Select('Users',$Columns,Array('SortOn'=>'RegisterDate','IsDesc'=>TRUE,'Where'=>Array('`OwnerID` = @local.__USER_ID','`OwnerID` != `ID`')));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	foreach($Users as $User)
		$Out[$User['ID']] = $User;
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
