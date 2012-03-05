{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер заблокирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер, заказ №{$Params.OrderID|string_format:"%05u"}, был отключён.
IP адрес заказа: {$Params.IP|default:'$Params.IP'}

{$Params.From.Sign|default:'$Params.From.Sign'}