{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}

Уведомляем Вас о том, что оканчивается срок аренды выделенного сервера, заказ №{$DSOrder.OrderID|string_format:"%05u"}.
До окончания заказа:	{$DSOrder.DaysRemainded|default:'$DSOrder.DaysRemainded'} дн.
Баланс договора:	{$DSOrder.Balance|default:'$DSOrder.Balance'}
Тарифный план:		"{$DSOrder.SchemeName|default:'$DSOrder.SchemeName'}"
Стоимость продления:	{$DSOrder.Cost|default:'$DSOrder.Cost'}*
IP адрес:		{$DSOrder.IP|default:'$DSOrder.IP'}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

