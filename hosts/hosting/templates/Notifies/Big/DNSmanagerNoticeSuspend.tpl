{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на вторичный DNS [{$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}]" scope=global}

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$DNSmanagerOrder.OrderID|string_format:"%05u"} на вторичный DNS.
До окончания заказа:	{$DNSmanagerOrder.DaysRemainded|default:'$DNSmanagerOrder.DaysRemainded'} дн.
Баланс договора:	{$DNSmanagerOrder.Balance|default:'$DNSmanagerOrder.Balance'}
Тарифный план:		"{$DNSmanagerOrder.SchemeName|default:'$DNSmanagerOrder.SchemeName'}"
Стоимость продления:	{$DNSmanagerOrder.Cost|default:'$DNSmanagerOrder.Cost'}*
Логин:			{$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}
{if $DNSmanagerOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$DNSmanagerOrder.ProlongLink|default:'$DNSmanagerOrder.ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$DNSmanagerOrder.SchemeName|default:'$DNSmanagerOrder.SchemeName'}" на другой:
{$DNSmanagerOrder.SchemeChangeLink|default:'$DNSmanagerOrder.SchemeChangeLink'}
После чего заказ можно будет продлить:
{$DNSmanagerOrder.ProlongLink|default:'$DNSmanagerOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.
