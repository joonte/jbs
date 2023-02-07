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
$Where = Array(
		'(`UserID` = @local.__USER_ID OR FIND_IN_SET(`GroupID`,@local.__USER_GROUPS_PATH))',
		'`IsActive` = "yes"',
		);
#-------------------------------------------------------------------------------
$Columns = Array(
		'ID','Name','PackageID','CostOrder','CostProlong','CostTransfer','IsProlong','IsTransfer','DaysToProlong','DaysBeforeTransfer','DaysAfterTransfer'
		);
$DomainSchemes = DB_Select('DomainSchemesOwners',$Columns,Array('Where'=>$Where,'SortOn'=>Array('SortID','PackageID')));
if(Is_Error($DomainSchemes))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array($DomainSchemes);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

