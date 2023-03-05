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
$Users = DB_Select('Users',Array('ID','RegisterDate','Name','Email','Sign','EnterIP','EnterDate','IsActive','IsNotifies','Params','IsConfirmed'),Array('ID'=>$GLOBALS['__USER']['ID']));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
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
foreach($Users as $User){
	#-------------------------------------------------------------------------------
	$Out[$User['ID']] = $User;
	#-------------------------------------------------------------------------------
	// докУменты
	$Files = GetUploadedFilesInfo('Users',$User['ID']);
	#-------------------------------------------------------------------------------
	if(SizeOf($Files)){
		#-------------------------------------------------------------------------------
		$Out[$User['ID']]['Files'] = Array();
		#-------------------------------------------------------------------------------
		foreach($Files as $File)
			$Out[$User['ID']]['Files'][$File['ID']] = $File;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
