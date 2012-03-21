{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается оплаченный срок Вашего заказа №{$Item.OrderID|string_format:"%05u"} на выделенный IP адрес {$Item.Login|default:'$Item.Login'}
До удаления заказа {$Item.DaysRemainded|default:'$Item.DaysRemainded'} дней.

{$From.Sign|default:'$From.Sign'}
