{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$User.Name|default:'$User.Name'}!

У вас есть неоплаченный счёт с номером #{$InvoiceID|default:'$InvoiceID'}.
Если вы не планируете его оплачивать - установите для него статус 'Отменён'.
Ваши счета на оплату: http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

{$From.Sign|default:'$From.Sign'}

