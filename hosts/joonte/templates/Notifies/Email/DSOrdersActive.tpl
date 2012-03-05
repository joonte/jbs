{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер подключен к сети" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер был установлен.
Номер заказа №{$Params.OrderID|string_format:"%05u"}, IP адрес {$Params.IP|default:'$Params.IP'}.
Дополнительная информация будет предоставлена в центре поддержки биллинговой панели.

{$Params.From.Sign|default:'$Params.From.Sign'}