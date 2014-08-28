{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заблокирован заказ виртуального сервера #{$OrderID|string_format:"%05u"} логин ({$Login|default:'$Login'})" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.
Логин:		{$Login|default:'$Login'}
IP адрес:	{$IP|default:'$IP'}

{$From.Sign|default:'$From.Sign'}
