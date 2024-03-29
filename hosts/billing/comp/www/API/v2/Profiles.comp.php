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
if(Is_Error(System_Load('modules/Authorisation.mod','libs/Upload.php')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Profiles = DB_Select('ProfilesOwners',Array('*'),Array('Where'=>SPrintF('`UserID` = %u',$GLOBALS['__USER']['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($Profiles)){
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
foreach($Profiles as $Profile){
	#-------------------------------------------------------------------------------
	$Out[$Profile['ID']] = $Profile;
	#-------------------------------------------------------------------------------
	// докУменты
	$Files = GetUploadedFilesInfo('Profiles',$Profile['ID']);
	#-------------------------------------------------------------------------------
	if(SizeOf($Files)){
		#-------------------------------------------------------------------------------
		$Out[$Profile['ID']]['Files'] = Array();
		#-------------------------------------------------------------------------------
		foreach($Files as $File)
			$Out[$Profile['ID']]['Files'][$File['ID']] = $File;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
