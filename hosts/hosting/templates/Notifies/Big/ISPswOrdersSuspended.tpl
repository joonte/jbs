{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ программного обеспечения заблокирован" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$IP|default:'$IP'}, был заблокирован.
Тарифный план:		"{$ISPswScheme.Name|default:'$ISPswScheme.Name'}"
Стоимость продления:	{$ISPswScheme.CostMonth|default:'$ISPswScheme.CostMonth'}*
{if $ISPswScheme.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ProlongLink|default:'$ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$ISPswScheme.Name|default:'$ISPswScheme.Name'}" на другой:
{$SchemeChangeLink|default:'$SchemeChangeLink'}
После чего заказ можно будет продлить:
{$ProlongLink|default:'$ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

