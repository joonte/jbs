{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Удалён заказ виртуального сервера #{$OrderID|string_format:"%05u"} логин ({$Login|default:'$Login'})" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на виртуальный выделенный сервер (VPS) №{$OrderID|string_format:"%05u"} был удален.
Логин:		{$Login|default:'$Login'}
IP адрес:	{$IP|default:'$IP'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

