<?php


#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Order');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
if(!In_Array($Order['StatusID'],Array('Waiting','Deleted'))){
  #-----------------------------------------------------------------------------
  $Comp = Comp_Load('Formats/Order/Number',$Order['ID']);
  if(Is_Error($Comp))
    return ERROR | @Trigger_Error(500);
  #-----------------------------------------------------------------------------
  return new gException('ORDER_CAN_NOT_DELETED',SPrintF('Заказ №%s не может быть удален',$Comp));
}
#-------------------------------------------------------------------------------
return TRUE;
#-------------------------------------------------------------------------------

?>
