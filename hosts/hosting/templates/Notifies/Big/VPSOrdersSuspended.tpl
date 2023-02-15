{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заблокирован заказ виртуального сервера #{$OrderID|string_format:"%05u"} IP ({$IP|default:'$IP'})" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован.
Логин:		{$Login|default:'$Login'} ({$Login|default:'$Login'}@{$Server.Params.Domain|default:'$Server.Params.Domain'})
IP адрес:	{$IP|default:'$IP'}
--
Тарифный план:		"{$VPSScheme.Name|default:'$VPSScheme.Name'}"
Стоимость продления:	{$VPSScheme.CostMonth|default:'$VPSScheme.CostMonth'}*
{if $VPSScheme.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ProlongLink|default:'$ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$VPSScheme.Name|default:'$VPSScheme.Name'}" на другой:
{$SchemeChangeLink|default:'$SchemeChangeLink'}
После чего заказ можно будет продлить:
{$ProlongLink|default:'$ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

