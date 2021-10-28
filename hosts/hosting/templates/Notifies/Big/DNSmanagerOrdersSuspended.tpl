{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Окончился срок действия заказа на вторичный DNS [{$Login|default:'$Login'}]" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на вторичный DNS, логин {$Login|default:'$Login'} был заблокирован.
--
Тарифный план:		"{$DNSmanagerScheme.Name|default:'$DNSmanagerScheme.Name'}"
Стоимость продления:	{$DNSmanagerScheme.CostMonth|default:'$DNSmanagerScheme.CostMonth'}*
{if $DNSmanagerScheme.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ProlongLink|default:'$ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$DNSmanagerScheme.Name|default:'$DNSmanagerScheme.Name'}" на другой:
{$SchemeChangeLink|default:'$SchemeChangeLink'}
После чего заказ можно будет продлить:
{$ProlongLink|default:'$ProlongLink'}
{/if}
--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.
