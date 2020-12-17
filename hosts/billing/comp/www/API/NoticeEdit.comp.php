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
$TableID 	=  (string) @$Args['TableID'];
$RowID   	= (integer) @$Args['RowID'];
$UserNotice	=  (string) @$Args['UserNotice'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Notice = DB_Select(SPrintF('%sOwners',$TableID),Array('ID','UserNotice','UserID'),Array('UNIQ','ID'=>$RowID));
#-------------------------------------------------------------------------------
switch(ValueOf($Notice)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	if($GLOBALS['__USER']['ID'] != $Notice['UserID'])
		if(!$GLOBALS['__USER']['IsAdmin'])
			return ERROR | @Trigger_Error(700);
	#-------------------------------------------------------------------------------
	break;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsUpdate = DB_Update($TableID,Array('UserNotice'=>Mb_SubStr(Mb_Convert_Encoding(Trim($UserNotice),'UTF-8'),0,32000)),Array('ID'=>$RowID));
#-------------------------------------------------------------------------------
if(Is_Error($IsUpdate))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return Array('Status'=>'Ok');
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------

?>
