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
$Services = DB_Select('Services',Array('ID','Name','NameShort','Code','Item','Measure','ConsiderTypeID'),Array('Where'=>"`IsActive` = 'yes' AND `IsHidden` = 'no'",'SortOn'=>'SortID'));
if(Is_Error($Services))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array($Services);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------