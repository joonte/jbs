{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Окончился срок действия заказа на вторичный DNS [{$Login|default:'$Login'}]" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на вторичный DNS, логин {$Login|default:'$Login'} был заблокирован.
--
Тарифный план:		"{$DNSmanagerScheme.Name|default:'$DNSmanagerScheme.Name'}"
Стоимость продления:	{$DNSmanagerScheme.CostMonth|default:'$DNSmanagerScheme.CostMonth'}

