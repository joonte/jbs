{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Условно оплаченный счет #{$Params.ID|default:'$Params.ID'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

У вас есть счёт, номер #{$Params.ID|default:'$Params.ID'}, в статусе "Условно оплачен".
Рекомендуем в ближайшее время его оплатить, в противном случае будем вынуждены
приостановить оказание услуг оплаченных данным счётом - заблокировать заказ
хостинга, VPS, или снять домен с делегирования.
Ваши счета на оплату: http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Invoices

{$Params.From.Sign|default:'$Params.From.Sign'}
