{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера заблокирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что  {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (ExtraIP) был заблокирован.

{$Params.From.Sign|default:'$Params.From.Sign'}

