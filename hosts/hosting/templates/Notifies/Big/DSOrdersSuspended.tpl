{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Выделенный сервер заблокирован" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер, заказ №{$OrderID|string_format:"%05u"}, был отключён.
{if $DSScheme.IPaddr || $IP != 'noassign'}
{if $IP != 'noassign'}
IP адрес заказа:	{$IP}
{else}
IP адрес заказа:	{$DSScheme.IPaddr}
{/if}
{/if}
Тарифный план:		"{$DSScheme.Name|default:'$DSScheme.Name'}"
Стоимость продления:	{$DSScheme.CostMonth|default:'$DSScheme.CostMonth'}*

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

