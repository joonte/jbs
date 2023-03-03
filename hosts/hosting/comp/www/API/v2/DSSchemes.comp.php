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
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Where = Array(
		'(`UserID` = @local.__USER_ID OR FIND_IN_SET(`GroupID`,@local.__USER_GROUPS_PATH))',
		'`IsActive` = "yes"',
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','Name','PackageID','CostDay','CostMonth','CostInstall','Discount','ServerID','IsProlong','MinDaysPay','MinDaysProlong',
		'MaxDaysPay','CPU','ram','raid','disks','chrate','trafflimit','OS','UserNotice'
		);
#-------------------------------------------------------------------------------
$DSSchemes = DB_Select('DSSchemesOwners',$Columns,Array('Where'=>$Where,'SortOn'=>Array('SortID','PackageID')));
#-------------------------------------------------------------------------------
switch(ValueOf($DSSchemes)){
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
return $DSSchemes;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

