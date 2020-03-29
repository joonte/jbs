{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$ExtraIPOrder.OrderID|string_format:"%05u"} на выделенный IP адрес.
До окончания заказа:	{$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дн.
Баланс договора:	{$ExtraIPOrder.Balance|default:'$ExtraIPOrder.Balance'}
Тарифный план:		"{$ExtraIPOrder.SchemeName|default:'$ExtraIPOrder.SchemeName'}"
Стоимость продления:	{$ExtraIPOrder.Cost|default:'$ExtraIPOrder.Cost'}
IP адрес:		{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}

