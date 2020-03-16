{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на виртуальный сервер #{$VPSOrder.OrderID|string_format:"%05u"}/[{$VPSOrder.IP|default:'$VPSOrder.IP'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
До окончания заказа:	{$VPSOrder.DaysRemainded|default:'$VPSOrder.DaysRemainded'} дн.
Баланс договора:	{$VPSOrder.Balance|default:'$VPSOrder.Balance'}
Тарифный план:		"{$VPSOrder.SchemeName|default:'$VPSOrder.SchemeName'}"
Стоимость продления:	{$VPSOrder.Cost|default:'$VPSOrder.Cost'}
IP адрес:		{$VPSOrder.IP|default:'$VPSOrder.IP'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

