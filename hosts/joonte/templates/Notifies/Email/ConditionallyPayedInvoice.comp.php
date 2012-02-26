{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

У вас есть счёт, номер #{$Params.InvoiceID|default:'$Params.InvoiceID'}, в статусе "Условно оплачен".
Рекомендуем в ближайшее время его оплатить, в противном случае будем вынуждены
приостановить оказание услуг оплаченных данным счётом - заблокировать заказ
хостинга, VPS, или снять домен с делегирования.
Ваши счета на оплату: http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

{$Params.From.Sign|default:'$Params.From.Sign'}
