{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на хостинг [{$HostingOrder.Login|default:'$HostingOrder.Login'}]" scope=global}
{assign var=ExpDate value=$HostingOrder.StatusDate + $Config.Tasks.Types.HostingForDelete.HostingDeleteTimeout * 24 * 3600}

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$HostingOrder.OrderID|string_format:"%05u"} на хостинг, логин {$HostingOrder.Login|default:'$HostingOrder.Login'}, домен {$HostingOrder.Domain|default:'$HostingOrder.Domain'}.
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$HostingOrder.Balance|default:'$HostingOrder.Balance'}
Тарифный план:        "{$HostingOrder.SchemeName|default:'$HostingOrder.SchemeName'}"
Стоимость продления:  {$HostingOrder.Cost|default:'$HostingOrder.Cost'}

