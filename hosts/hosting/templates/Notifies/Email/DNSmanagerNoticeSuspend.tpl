{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на вторичный DNS [{$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$DNSmanagerOrder.OrderID|string_format:"%05u"} на вторичный DNS.
До окончания заказа {$DNSmanagerOrder.DaysRemainded|default:'$DNSmanagerOrder.DaysRemainded'} дн.
Логин: {$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}

{$From.Sign|default:'$From.Sign'}
