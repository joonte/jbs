{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на вторичный DNS [{$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}]" scope=global}
{assign var=ExpDate value=$DNSmanagerOrder.StatusDate + $Config.Tasks.Types.DNSmanagerForDelete.DNSmanagerDeleteTimeout * 24 * 3600}

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$DNSmanagerOrder.OrderID|string_format:"%05u"} на вторичный DNS, логин {$DNSmanagerOrder.Login|default:'$DNSmanagerOrder.Login'}, домен {$DNSmanagerOrder.Domain|default:'$DNSmanagerOrder.Domain'}.
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$DNSmanagerOrder.Balance|default:'$DNSmanagerOrder.Balance'}
Тарифный план:        "{$DNSmanagerOrder.SchemeName|default:'$DNSmanagerOrder.SchemeName'}"
Стоимость продления:  {$DNSmanagerOrder.Cost|default:'$DNSmanagerOrder.Cost'}*
{if $DNSmanagerOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ProlongLink|default:'$ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$DNSmanagerOrder.Name|default:'$DNSmanagerOrder.Name'}" на другой:
{$DNSmanagerOrder.SchemeChangeLink|default:'$DNSmanagerOrder.SchemeChangeLink'}
После чего заказ можно будет продлить:
{$DNSmanagerOrder.ProlongLink|default:'$DNSmanagerOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

