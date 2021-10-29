{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}

Уведомляем Вас о том, что оканчивается оплаченный срок Вашего заказа №{$ExtraIPOrder.OrderID|string_format:"%05u"} на выделенный IP адрес {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}
IP адрес:            {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}
До удаления заказа:  {$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дней.
Баланс договора:     {$ExtraIPOrder.Balance|default:'$ExtraIPOrder.Balance'}
Тарифный план:       "{$ExtraIPOrder.SchemeName|default:'$ExtraIPOrder.SchemeName'}"
Стоимость продления: {$ExtraIPOrder.Cost|default:'$ExtraIPOrder.Cost'}*
{if $ExtraIPOrder.IsProlong}
--
Для продления заказа, воспользуйтесь этой ссылкой:
{$ExtraIPOrder.ProlongLink|default:'$ExtraIPOrder.ProlongLink'}
{/if}

--
* Справочная информация, не является офертой. Стоимость может отличаться, в зависимости от ваших скидок.

