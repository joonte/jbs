<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Adding','TypeID');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Td = new Tag('TD',Array('style'=>'padding:5px;'));
#-------------------------------------------------------------------------------
$Td->{Is_Object($Adding)?'AddChild':'AddText'}($Adding);
#-------------------------------------------------------------------------------
$Tr = new Tag('TR',$Td);
#-------------------------------------------------------------------------------
$Table = new Tag('TABLE',Array('class'=>$TypeID,'cellspacing'=>5,'align'=>'center'),$Tr);
#-------------------------------------------------------------------------------
return $Table;
#-------------------------------------------------------------------------------

?>
