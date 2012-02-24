<?php

#-------------------------------------------------------------------------------
/** @author Великодный В.В. (Joonte Ltd.) */
/******************************************************************************/
/******************************************************************************/
$__args_list = Array('Replace');
/******************************************************************************/
Eval(COMP_INIT);
/******************************************************************************/
/******************************************************************************/
$ISPswOrder = &$Replace['ISPswOrder'];
#-------------------------------------------------------------------------------
$Config = Config();
$DeleteTimeout = $ISPswOrder['StatusDate'] + $Config['Tasks']['Types']['ISPswForDelete']['DeleteTimeout'] * 24 * 3600 - Time();
$TimeRemainder = Comp_Load('Formats/Date/Remainder',$DeleteTimeout);
if(Is_Error($TimeRemainder))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ISPswOrder['TimeRemainder'] = $TimeRemainder;
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$ISPswOrder['OrderID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$ISPswOrder['Number'] = $Number;
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №%ISPswOrder.Number% на ПО ISPsystem, IP адрес %ISPswOrder.IP%.
До удаления заказа %ISPswOrder.TimeRemainder%.

%From.Sign%
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>'Оканчивается срок блокировки заказа на программное обеспечение','Message'=>$Message);
#-------------------------------------------------------------------------------

?>
