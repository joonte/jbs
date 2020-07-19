{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на хостинг [{$HostingOrder.Login|default:'$HostingOrder.Login'}]" scope=global}

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$HostingOrder.OrderID|string_format:"%05u"} на хостинг.
До окончания заказа:	{$HostingOrder.DaysRemainded|default:'$HostingOrder.DaysRemainded'} дн.
Баланс договора:	{$HostingOrder.Balance|default:'$HostingOrder.Balance'}
Тарифный план:		"{$HostingOrder.SchemeName|default:'$HostingOrder.SchemeName'}"
Стоимость продления:	{$HostingOrder.Cost|default:'$HostingOrder.Cost'}*
Логин:			{$HostingOrder.Login|default:'$HostingOrder.Login'}
Паркованный домен:	{$HostingOrder.Domain|default:'$HostingOrder.Domain'}.

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

