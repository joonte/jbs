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
$Edesks = DB_Select('EdesksOwners',Array('ID','CreateDate','UserID','PriorityID','Theme','UpdateDate','StatusID','StatusDate','SeenByUser','Flags','Content','MessageID'),Array('Where'=>'`UserID` = @local.__USER_ID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Edesks)){
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
foreach($Edesks as $Edesk){
	#-------------------------------------------------------------------------------
	// удаляем содержимое сообщение, оно может быть невидимо, в нём может быть скрытый текст и т.п....
	UnSet($Edesk['Content']);
	#-------------------------------------------------------------------------------
	$Out[$Edesk['ID']] = $Edesk;
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
