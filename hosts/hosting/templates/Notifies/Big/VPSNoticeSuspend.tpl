{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на виртуальный сервер #{$VPSOrder.OrderID|string_format:"%05u"}/[{$VPSOrder.IP|default:'$VPSOrder.IP'}]" scope=global}

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
До окончания заказа:	{$VPSOrder.DaysRemainded|default:'$VPSOrder.DaysRemainded'} дн.
Баланс договора:	{$VPSOrder.Balance|default:'$VPSOrder.Balance'}
Тарифный план:		"{$VPSOrder.SchemeName|default:'$VPSOrder.SchemeName'}"
Стоимость продления:	{$VPSOrder.Cost|default:'$VPSOrder.Cost'}*
IP адрес:		{$VPSOrder.IP|default:'$VPSOrder.IP'}
{if $VPSOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$VPSOrder.ProlongLink|default:'$VPSOrder.ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$VPSOrder.SchemeName|default:'$VPSOrder.SchemeName'}" на другой:
{$VPSOrder.SchemeChangeLink|default:'$VPSOrder.SchemeChangeLink'}
После чего заказ можно будет продлить:
{$VPSOrder.ProlongLink|default:'$VPSOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

