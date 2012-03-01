{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается оплаченный срок Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на выделенный IP адрес {$Params.Item.Login|default:'$Params.Item.Login'}
До удаления заказа {$Params.Item.DaysRemainded|default:'$Params.Item.DaysRemainded'} дней.

{$Params.From.Sign|default:'$Params.From.Sign'}
