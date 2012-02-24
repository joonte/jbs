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
$DSOrder = &$Replace['DSOrder'];
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$DSOrder['OrderID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DSOrder['Number'] = $Number;
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

Уведомляем Вас о том, что оканчивается срок аренды выделенного сервера, заказ №%DSOrder.Number%.
До окончания заказа %DSOrder.DaysRemainded% дн.
IP адрес: %DSOrder.IP%

%From.Sign%
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>'Оканчивается срок действия заказа выделенного сервера','Message'=>$Message);
#-------------------------------------------------------------------------------

?>
