{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера #{$VPSOrder.OrderID|string_format:"%05u"}/[{$Login|default:'$Login'}] заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.
IP адрес: {$VPSOrder.Login|default:'$VPSOrder.Login'}

{$From.Sign|default:'$From.Sign'}
