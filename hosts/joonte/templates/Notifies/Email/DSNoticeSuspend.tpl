{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок аренды выделенного сервера, заказ №{$Item.OrderID|string_format:"%05u"}.
До окончания заказа {$Item.DaysRemainded|default:'$Item.DaysRemainded'} дн.
IP адрес: {$Item.IP|default:'$Item.IP'}

{$From.Sign|default:'$From.Sign'}

