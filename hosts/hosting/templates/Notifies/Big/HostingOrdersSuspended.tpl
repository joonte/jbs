{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Окончился срок действия заказа на хостинг [{$Login|default:'$Login'}]" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, c паркованным доменом {$Domain|default:'$Domain'}, был заблокирован.

---
Тарифный план:		"{$HostingScheme.Name|default:'$HostingScheme.Name'}"
Стоимость продления:	{$HostingScheme.CostMonth|default:'$HostingScheme.CostMonth'}*

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

