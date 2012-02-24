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
$DomainOrder = &$Replace['DomainOrder'];
#-------------------------------------------------------------------------------
$TimeRemainded = Comp_Load('Formats/Date/Remainder',$DomainOrder['ExpirationDate'] - Time());
if(Is_Error($TimeRemainded))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder['TimeRemainded'] = $TimeRemainded;
#-------------------------------------------------------------------------------
$Number = Comp_Load('Formats/Order/Number',$DomainOrder['OrderID']);
if(Is_Error($Number))
  return ERROR | @Trigger_Error(500);
#-------------------------------------------------------------------------------
$DomainOrder['Number'] = $Number;
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №%DomainOrder.Number% %DomainOrder.DomainName%.%DomainOrder.DomainZone% на регистрацию домена.
Пожалуйста, не забудьте своевременно продлить Ваш заказ, иначе он будет заблокирован и аннулирован, а Ваше доменное имя смогут занять другие люди.
До окончание заказа %DomainOrder.TimeRemainded%.

{$Replace['From']['Sign']}
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>'Оканчивается срок действия заказа на домен','Message'=>$Message);
#-------------------------------------------------------------------------------

?>
