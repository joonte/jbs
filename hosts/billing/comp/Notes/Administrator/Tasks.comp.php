<?php
#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Config = Config();
#-------------------------------------------------------------------------------
$Settings = $Config['Interface']['Administrator']['Notes']['Tasks'];
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
if(!$Settings['ShowUnExecuted'])
	return $Result;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Result = Array();
#-------------------------------------------------------------------------------
$Count = DB_Count('Tasks',Array('Where'=>"(`IsActive` = 'no' OR `Errors` > 0) AND `IsExecuted` = 'no'"));
if(Is_Error($Count))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
if($Count){
	#-------------------------------------------------------------------------------
	$Result = Array();
	#-------------------------------------------------------------------------------
	$NoBody = new Tag('NOBODY');
	#-------------------------------------------------------------------------------
	$NoBody->AddHTML(TemplateReplace('Notes.Administrator.Tasks',Array('Tasks'=>$Count)));
	#-------------------------------------------------------------------------------
	$Result = Array($NoBody);
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Result;
#-------------------------------------------------------------------------------

?>
