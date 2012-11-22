{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$ExtraIPOrder.OrderID|string_format:"%05u"} на выделенный IP адрес.
До окончания заказа {$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дн.
IP адрес: {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}

{$From.Sign|default:'$From.Sign'}
