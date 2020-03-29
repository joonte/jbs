{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на виртуальный сервер #{$VPSOrder.OrderID|string_format:"%05u"}/[{$VPSOrder.Login|default:'$VPSOrder.Login'}]" scope=global}
{assign var=ExpDate value=$VPSOrder.StatusDate + $Config.Tasks.Types.VPSForDelete.DeleteTimeout * 24 * 3600}

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
IP адрес:             {$VPSOrder.IP|default:'$VPSOrder.IP'}
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$VPSOrder.Balance|default:'$VPSOrder.Balance'}
Тарифный план:        "{$VPSOrder.SchemeName|default:'$VPSOrder.SchemeName'}"
Стоимость продления:  {$VPSOrder.Cost|default:'$VPSOrder.Cost'}
Логин:                {$VPSOrder.Login|default:'$VPSOrder.Login'}

