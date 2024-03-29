{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на программное обеспечение" scope=global}
{assign var=ExpDate value=$ISPswOrder.StatusDate + $Config.Tasks.Types.ISPswForDelete.DeleteTimeout * 24 * 3600}

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$ISPswOrder.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$ISPswOrder.IP|default:'$ISPswOrder.IP'}.
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$ISPswOrder.Balance|default:'$ISPswOrder.Balance'}
Тарифный план:        "{$ISPswOrder.SchemeName|default:'$ISPswOrder.SchemeName'}"
Стоимость продления:  {$ISPswOrder.Cost|default:'$ISPswOrder.Cost'}*
{if $ISPswOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ISPswOrder.ProlongLink|default:'$ISPswOrder.ProlongLink'}
{else}
--
Для продления заказа, необходимо сменить тарифный план "{$ISPswOrder.Name|default:'$ISPswOrder.Name'}" на другой:
{$ISPswOrder.SchemeChangeLink|default:'$ISPswOrder.SchemeChangeLink'}
После чего заказ можно будет продлить:
{$ISPswOrder.ProlongLink|default:'$ISPswOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

