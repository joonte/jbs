<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Table','ID','OldComp','Value','Length','AdminNotice','UserNotice','UserID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/

# user notice
$Comp = Comp_Load('UserNotice',$Table,$ID,$UserNotice);
if(Is_Error($Comp))
        return ERROR | @Trigger_Error(500);
$Tr = new Tag('TR',new Tag('TD',$Comp));
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
# admin notes, visible only for personal
$__USER = $GLOBALS['__USER'];
#---------------------------------------------------------------------------
$IsPermission = Permission_Check('AdminNote',(integer)$__USER['ID'],(integer)$UserID);
#---------------------------------------------------------------------------
switch(ValueOf($IsPermission)){
case 'true':
	$Comp = Comp_Load('Notice',$Table,$ID,$AdminNotice);
	if(Is_Error($Comp))
		return ERROR | @Trigger_Error(500);
	$Tr->AddChild(new Tag('TD',$Comp));
}
#---------------------------------------------------------------------------
#---------------------------------------------------------------------------
# original value
$Comp = Comp_Load($OldComp,$Value,$Length);
if(Is_Error($Comp))
	return ERROR | @Trigger_Error(500);
$Tr->AddChild(new Tag('TD',$Comp));
#---------------------------------------------------------------------------
return new Tag('TABLE',Array('cellspacing'=>2,'cellpadding'=>0),$Tr);


?>
