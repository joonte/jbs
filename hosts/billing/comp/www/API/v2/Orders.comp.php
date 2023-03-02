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
// все колонки кроме AdminNotice
$Orders = DB_Select('OrdersOwners',Array('ID','OrderDate','ContractID','ServiceID','ServerID','IsAutoProlong','ExpirationDate','Keys','IsPayed','DaysRemainded','StatusID','StatusDate','Params','UserNotice','UserID'),Array('Where'=>SPrintF("`UserID` = %u",$GLOBALS['__USER']['ID']),'SortOn'=>Array('ServiceID','ID')));
if(Is_Error($Orders))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Orders;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

