<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Replace');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$Comp = Comp_Load('Notifies/Email/ConditionallyPayedInvoice',$Replace);
if(Is_Error($Comp))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
return Implode("\r\n-\r\n",$Comp);
#-------------------------------------------------------------------------------

?>
