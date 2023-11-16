<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('IsSearch');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Filters = Array('Подтвержённые аккаунты');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Users = DB_Select('Users',Array('ID'),Array('Where'=>'LENGTH(`ConfirmedWas`) > 6'));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$UsersIDs = Array();
	#-------------------------------------------------------------------------------
	foreach($Users as $User)
		$UsersIDs[] = $User['ID'];
	#-------------------------------------------------------------------------------
        $Filters['ConfirmedWasYes'] = Array('Name'=>'Да','UsersIDs'=>$UsersIDs);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Users = DB_Select('Users',Array('ID'),Array('Where'=>'LENGTH(`ConfirmedWas`) < 6 OR LENGTH(`ConfirmedWas`) IS NULL'));
#-------------------------------------------------------------------------------
switch(ValueOf($Users)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	break;
case 'array':
	#-------------------------------------------------------------------------------
	$UsersIDs = Array();
	#-------------------------------------------------------------------------------
	foreach($Users as $User)
		$UsersIDs[] = $User['ID'];
	#-------------------------------------------------------------------------------
        $Filters['ConfirmedWasNo'] = Array('Name'=>'Нет','UsersIDs'=>$UsersIDs);
	#-------------------------------------------------------------------------------
	break;
	#-------------------------------------------------------------------------------
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Filters;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>