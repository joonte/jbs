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
$Partition	= (string) @$Args['Partition'];
#-------------------------------------------------------------------------------
if(Is_Error(System_Load('modules/Authorisation.mod')))
	return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Out = Array();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$Clauses = DB_Select('ClausesOwners',Array('*'),Array('Where'=>SPrintF('`IsPublish` = "yes"')));
#-------------------------------------------------------------------------------
switch(ValueOf($Clauses)){
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
foreach($Clauses as $Clause){
	#-------------------------------------------------------------------------------
	UnSet($Clause['UserID']);
	UnSet($Clause['AuthorID']);
	UnSet($Clause['EditorID']);
	#-------------------------------------------------------------------------------
	if($Partition){
		#-------------------------------------------------------------------------------
		// задана конкретная статья
		if($Clause['Partition'] == Trim($Partition))
			$Out[$Clause['ID']] = $Clause;
		#-------------------------------------------------------------------------------
	}else{
		#-------------------------------------------------------------------------------
		$Out[$Clause['ID']] = $Clause;
		#-------------------------------------------------------------------------------
	}
	#-------------------------------------------------------------------------------
}
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
return $Out;
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
