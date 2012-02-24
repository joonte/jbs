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
$Replace['HostID'] = HOST_ID;
$Theme = $Replace['Theme'];
#-------------------------------------------------------------------------------
$Message = <<<EOT
Здравствуйте, %User.Name%!

У вас есть счёт, номер #%InvoiceID%, в статусе "Условно оплачен".
Рекомендуем в ближайшее время его оплатить, в противном случае будем вынуждены
приостановить оказание услуг оплаченных данным счётом - заблокировать заказ
хостинга, VPS, или снять домен с делегирования.
Ваши счета на оплату: http://%HostID%/Invoices

%From.Sign%
EOT;
#-------------------------------------------------------------------------------
$Replace = Array_ToLine($Replace,'%');
#-------------------------------------------------------------------------------
foreach(Array_Keys($Replace) as $Key)
  $Message = Str_Replace($Key,$Replace[$Key],$Message);
#-------------------------------------------------------------------------------
return Array('Theme'=>$Theme,'Message'=>$Message);
#-------------------------------------------------------------------------------

?>
