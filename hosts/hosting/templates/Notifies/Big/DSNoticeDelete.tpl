{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки выделенного сервера" scope=global}
{assign var=ExpDate value=$DSOrder.StatusDate + $Config.Tasks.Types.DSForDelete.DeleteTimeout * 24 * 3600}

Уведомляем Вас о том, что оканчивается срок блокировки Вашего выделенного сервера, заказ №{$DSOrder.OrderID|string_format:"%05u"}, IP адрес {$DSOrder.IP|default:'$DSOrder.IP'}.
Дата удаления заказа:  {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:       {$DSOrder.Balance|default:'$DSOrder.Balance'}
Тарифный план:         "{$DSOrder.SchemeName|default:'$DSOrder.SchemeName'}"
Стоимость продления:   {$DSOrder.Cost|default:'$DSOrder.Cost'}*
{if $DSOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$DSOrder.ProlongLink|default:'$DSOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

