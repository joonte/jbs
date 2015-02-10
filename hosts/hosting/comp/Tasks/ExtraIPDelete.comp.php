<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Task','ExtraIPOrderID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(Is_Error(System_Load('classes/ExtraIPServer.class.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ExtraIPOrder = DB_Select('ExtraIPOrdersOwners',Array('ID','UserID','Login','OrderType','DependOrderID','SchemeID','(SELECT `Name` FROM `ExtraIPSchemes` WHERE `ExtraIPSchemes`.`ID` = `ExtraIPOrdersOwners`.`SchemeID`) as `SchemeName`'),Array('UNIQ','ID'=>$ExtraIPOrderID));
#-------------------------------------------------------------------------------
switch(ValueOf($ExtraIPOrder)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$ExtraIPServer = new ExtraIPServer();
#-------------------------------------------------------------------------------
$IsSelected = $ExtraIPServer->FindSystem((integer)$ExtraIPOrderID,(string)$ExtraIPOrder['OrderType'],(integer)$ExtraIPOrder['DependOrderID']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsSelected)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsDelete = $ExtraIPServer->DeleteIP($ExtraIPOrder['Login']);
#-------------------------------------------------------------------------------
switch(ValueOf($IsDelete)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return $IsDelete;
case 'true':
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Event = Array(
		'UserID'	=> $ExtraIPOrder['UserID'],
		'PriorityID'	=> 'Billing',
		'Text'		=> SPrintF('Заказ выделенного IP (%s), удален с сервера (%s)',$ExtraIPOrder['Login'],$ExtraIPServer->Settings['Address'])
		);
$Event = Comp_Load('Events/EventInsert',$Event);
if(!$Event)
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$GLOBALS['TaskReturnInfo'] = Array(($ExtraIPServer->Settings['Address'])=>Array($ExtraIPOrder['Login']));
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
