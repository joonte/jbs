<?php
#-------------------------------------------------------------------------------
/*<JBsDOC>
 <Target>file</Target>
 <Org>Eximius, LLC</Org>
 <Author>Alex Keda</Author>
</JBsDOC>*/
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
# Ищем максимальный UserID
$MaxUserID = DB_Select('Users','MAX(`ID`) AS `ID`',Array('UNIQ'));
switch(ValueOf($MaxUserID)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
# перебираем все идентификаторы котоыре > 300 и < 2001
$OldUserIDs = DB_Select('Users','`ID`',Array('Where'=>SPrintF('`ID` > 300 AND `ID` < 2001')));
switch(ValueOf($OldUserIDs)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return TRUE;
case 'array':
	break;
default:
	return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$NewUserID = ($MaxUserID['ID'] < 2001)?'2001':$MaxUserID['ID'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
foreach($OldUserIDs as $OldUserID){
	#-------------------------------------------------------------------------------
	$NewUserID++;
	#-------------------------------------------------------------------------------
	# меняем ID юзера
	$IsUpdate = DB_Update('Users',Array('ID'=>$NewUserID),Array('ID'=>$OldUserID['ID']));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# Events
	$IsUpdate = DB_Update('Events',Array('UserID'=>$NewUserID),Array('Where'=>SPrintF('`UserID` = %u',$OldUserID['ID'])));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# RequestLog
	$IsUpdate = DB_Update('RequestLog',Array('UserID'=>$NewUserID),Array('Where'=>SPrintF('`UserID` = %u',$OldUserID['ID'])));
	if(Is_Error($IsUpdate))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
$MaxID = DB_Query(SPrintF('ALTER TABLE `Users` AUTO_INCREMENT=%u;',$NewUserID+2));
if(Is_Error($MaxID))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
?>
