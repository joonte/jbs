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
$User = DB_Select('Users',Array('ID','RegisterDate','Name','Email','Sign','EnterIP','EnterDate','IsActive','IsNotifies','Params','IsConfirmed'),Array('ID'=>$GLOBALS['__USER']['ID']));
if(Is_Error($User))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array($User);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
