<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Value');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if($Value){
  #-----------------------------------------------------------------------------
  $Value = Crypt_Encode($Value);
  if(Is_Error($Value))
    return ERROR | @Trigger_Error(500);
}
#-------------------------------------------------------------------------------
return $Value;
#-------------------------------------------------------------------------------

?>
