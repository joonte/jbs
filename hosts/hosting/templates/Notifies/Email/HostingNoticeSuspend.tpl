{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на хостинг [{$HostingOrder.Login|default:'$HostingOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$HostingOrder.OrderID|string_format:"%05u"} на хостинг.
До окончания заказа {$HostingOrder.DaysRemainded|default:'$HostingOrder.DaysRemainded'} дн.
Логин: {$HostingOrder.Login|default:'$HostingOrder.Login'}
Паркованный домен: {$HostingOrder.Domain|default:'$HostingOrder.DaysRemainded'}.

{$From.Sign|default:'$From.Sign'}
