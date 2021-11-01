{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}

У вас есть неоплаченный счёт с номером #{$InvoiceID|default:'$InvoiceID'}.
Данным счётом будут оплачены следующие услуги:
{$Items|default:'$Items'}

Для оплаты счёта, воспользуйтесь этой ссылкой:
{$PaymentLink|default:'$PaymentLink'}

Если вы не планируете его оплачивать, то установите для него статус "Отменён".

