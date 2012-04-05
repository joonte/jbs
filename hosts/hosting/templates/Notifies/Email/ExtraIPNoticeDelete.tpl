{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается оплаченный срок Вашего заказа №{$ExtraIPOrder.OrderID|string_format:"%05u"} на выделенный IP адрес {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}
До удаления заказа {$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дней.

{$From.Sign|default:'$From.Sign'}
