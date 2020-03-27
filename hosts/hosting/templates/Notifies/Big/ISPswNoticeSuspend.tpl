{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на программное обеспечение" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$ISPswOrder.OrderID|string_format:"%05u"} на ПО ISPsystem.
До окончания заказа:	{$ISPswOrder.DaysRemainded|default:'$ISPswOrder.DaysRemainded'} дн.
Баланс договора:	{$ISPswOrder.Balance|default:'$ISPswOrder.Balance'}
Тарифный план:		"{$ISPswOrder.SchemeName|default:'$ISPswOrder.SchemeName'}"
Стоимость продления:	{$ISPswOrder.Cost|default:'$ISPswOrder.Cost'}
IP адрес заказа:	{$ISPswOrder.IP|default:'$ISPswOrder.IP'}.

