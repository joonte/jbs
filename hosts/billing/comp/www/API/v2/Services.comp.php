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
$Services = DB_Select('Services',Array('ID','Name','NameShort','Code','Item','Measure','ConsiderTypeID','IsActive'),Array('Where'=>"`IsHidden` = 'no'",'SortOn'=>'SortID'));
#-------------------------------------------------------------------------------
switch(ValueOf($Services)){
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
foreach($Services as $Service){
	#-------------------------------------------------------------------------------
	// поля сервиса
	$ServicesFields = DB_Select('ServicesFieldsOwners',Array('*'),Array('Where'=>SPrintF('`ServiceID` = %u',$Service['ID']),'SortOn'=>'SortID'));
	#-------------------------------------------------------------------------------
	switch(ValueOf($ServicesFields)){
	case 'error':
		return ERROR | @Trigger_Error(500);
	case 'exception':
		$ServicesFields = Array();
	case 'array':
		break;
	default:
		return ERROR | @Trigger_Error(101);
	}
	#-------------------------------------------------------------------------------
	$Service['ServicesFields'] = $ServicesFields;
	#-------------------------------------------------------------------------------
	$Out[$Service['ID']] = $Service;
	#-------------------------------------------------------------------------------
	// докУменты
	$Files = GetUploadedFilesInfo('Services',$Service['ID']);
	#-------------------------------------------------------------------------------
	if(SizeOf($Files)){
		#-------------------------------------------------------------------------------
		$Out[$Service['ID']]['Files'] = Array();
		#-------------------------------------------------------------------------------
		foreach($Files as $File)
			$Out[$Service['ID']]['Files'][$File['ID']] = $File;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
