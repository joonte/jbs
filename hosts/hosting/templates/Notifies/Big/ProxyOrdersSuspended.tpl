{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}
{assign var=Theme value="Окончился срок действия заказа на прокси-сервер [{$Host|default:'$Host'}:{$Port|default:'$Port'}]" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на прокси-сервер {$Host|default:'$Host'}:{$Port|default:'$Port'}, был заблокирован.
---
Тарифный план:		"{$ProxyScheme.Name|default:'$ProxyScheme.Name'}"
Стоимость продления:	{$ProxyScheme.CostMonth|default:'$ProxyScheme.CostMonth'}

