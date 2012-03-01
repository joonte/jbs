{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер заблокирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер, заказ №{$Params.Item.OrderID|string_format:"%05u"}, был отключён.
IP адрес заказа: {$Params.Item.IP|default:'$Params.Item.IP'}

{$Params.From.Sign|default:'$Params.From.Sign'}