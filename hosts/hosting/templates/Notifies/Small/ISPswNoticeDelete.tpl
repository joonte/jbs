{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}
{assign var=ExpDate value=$ISPswOrder.StatusDate + $Config.Tasks.Types.ISPswForDelete.DeleteTimeout * 24 * 3600}
Оканчивается срок блокировки Вашего заказа #{$ISPswOrder.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$ISPswOrder.IP|default:'$ISPswOrder.IP'}.
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

