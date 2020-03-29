{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}

У вас есть неоплаченный счёт с номером #{$InvoiceID|default:'$InvoiceID'}.
Данным счётом будут оплачены следующие услуги:
{$Items|default:'$Items'}

Если вы не планируете его оплачивать - установите для него статус 'Отменён'.
Ваши счета на оплату: http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

