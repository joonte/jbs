{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ программного обеспечения заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$IP|default:'$IP'}, был заблокирован.
Тарифный план:		"{$ISPswScheme.Name|default:'$ISPswScheme.Name'}"
Стоимость продления:	{$ISPswScheme.CostMonth|default:'$ISPswScheme.CostMonth'}

