<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Args = Args();
#-------------------------------------------------------------------------------
$OrderFieldID = (integer) @$Args['OrderFieldID'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod','classes/DOM.class.php','libs/HTMLDoc.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$OrderField = DB_Select('OrdersFieldsOwners',Array('UserID','Value','FileName'),Array('UNIQ','ID'=>$OrderFieldID));
#-------------------------------------------------------------------------------
switch(ValueOf($OrderField)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return new gException('ACCOUNT_NOT_FOUND','Счёт не найден');
case 'array':
	#-------------------------------------------------------------------------------
	$Permission = Permission_Check('OrdersFieldsRead',(integer)$GLOBALS['__USER']['ID'],(integer)$OrderField['UserID']);
	#-------------------------------------------------------------------------------
	switch(ValueOf($Permission)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		return ERROR | @Trigger_Error(400);
	case 'false':
		return ERROR | @Trigger_Error(700);
	case 'true':
		#-------------------------------------------------------------------------------
		$File = Base64_Decode($OrderField['Value']);
		#-------------------------------------------------------------------------------
		$Length =  MB_StrLen($File,'ASCII');
		#-------------------------------------------------------------------------------
		Header('Content-Type: application; charset=utf-8');
		Header(SPrintF('Content-Length: %u',$Length));
		Header(SPrintF('Content-Disposition: attachment; filename="%s";',$OrderField['FileName']));
		Header('Pragma: nocache');
		#-------------------------------------------------------------------------------
		return $File;
		#-------------------------------------------------------------------------------
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
