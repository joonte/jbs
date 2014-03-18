{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ виртуального сервера #{$OrderID|string_format:"%05u"} логин ({$Login|default:'$Login'}) заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.
IP адрес: {$Login|default:'$Login'}

{$From.Sign|default:'$From.Sign'}
