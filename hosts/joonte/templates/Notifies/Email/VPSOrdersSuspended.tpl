{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера заблокирован" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.Item.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.

{$Params.From.Sign|default:'$Params.From.Sign'}
