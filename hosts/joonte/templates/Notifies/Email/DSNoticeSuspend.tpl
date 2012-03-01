{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок аренды выделенного сервера, заказ №{$Params.Item.OrderID|string_format:"%05u"}.
До окончания заказа {$Params.Item.DaysRemainded|default:'$Params.Item.DaysRemainded'} дн.
IP адрес: {$Params.Item.IP|default:'$Params.Item.IP'}

{$Params.From.Sign|default:'$Params.From.Sign'}

