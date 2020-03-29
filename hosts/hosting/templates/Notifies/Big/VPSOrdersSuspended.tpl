{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заблокирован заказ виртуального сервера #{$OrderID|string_format:"%05u"} логин ({$Login|default:'$Login'})" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.
Логин:		{$Login|default:'$Login'}
IP адрес:	{$IP|default:'$IP'}
--
Тарифный план:		"{$VPSScheme.Name|default:'$VPSScheme.Name'}"
Стоимость продления:	{$VPSScheme.CostMonth|default:'$VPSScheme.CostMonth'}

