<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('UserID','Length');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

Debug(SPrintF('[comp/Users/Smart]: UserID = %s; $Length = %s',$UserID,$Length));

$User = DB_Select('Users',Array('ID','Name','Email','AdminNotice','IsActive','Params'),Array('UNIQ','ID'=>$UserID));
#-------------------------------------------------------------------------------
switch(ValueOf($User)){
case 'error':
	return ERROR | @Trigger_Error(500);
case 'exception':
	return ERROR | @Trigger_Error(400);
case 'array':
	#-------------------------------------------------------------------------------
	$Span = new Tag('SPAN',Array('style'=>'display;'));
	#-------------------------------------------------------------------------------
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Menus/List','Administrator/ListMenu/User.xml', $User);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Span->AddChild(new Tag('SPAN',Array('style'=>'display: inline-block; vertical-align: middle;'),$Comp));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Notice','Users',$User['ID'],$User['AdminNotice']);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	$Span->AddChild(new Tag('SPAN',Array('style'=>'display: inline-block; vertical-align: middle;'),$Comp));
	#-------------------------------------------------------------------------------
	$Comp = Comp_Load('Formats/String',$User['Email'],$Length + 15); /* разобраться почему раньше хватало? */
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	#-------------------------------------------------------------------------------
	# системный юзер
	$Color = ($User['ID'] < 2000)?'D00000':'000000';
	# автозареганый
	$Color = ($User['Params']['IsAutoRegistered'])?'FFA500':$Color;
	# неактивный юзер
	$Color = $User['IsActive']?$Color:'848484';
	#-------------------------------------------------------------------------------
	$Span->AddChild(new Tag('SPAN',Array('style'=>SPrintF('display: inline-block; vertical-align: middle; white-space: nowrap; color:#%s;',$Color)),$Comp));
	#-------------------------------------------------------------------------------
	return $Span;
default:
	return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
