<?php

#-------------------------------------------------------------------------------
/** @author Alex Keda, for www.host-food.ru */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('ProxyOrder');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
#-------------------------------------------------------------------------------
$ExecuteDate = Time();
#-------------------------------------------------------------------------------
#-------------------------------------------------------------------------------
$IsAdd = Comp_Load('www/Administrator/API/TaskEdit',Array('UserID'=>$ProxyOrder['UserID'],'TypeID'=>'ProxyCreate','ExecuteDate'=>$ExecuteDate,'Params'=>Array($ProxyOrder['ID'])));
#-------------------------------------------------------------------------------
switch(ValueOf($IsAdd)){
  case 'error':
    return ERROR | @Trigger_Error(500);
  case 'exception':
    return ERROR | @Trigger_Error(400);
  case 'array':
    return TRUE;
  default:
    return ERROR | @Trigger_Error(101);
}
#-------------------------------------------------------------------------------

?>
