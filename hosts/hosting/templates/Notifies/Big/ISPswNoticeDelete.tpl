{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на программное обеспечение" scope=global}
{assign var=ExpDate value=$ISPswOrder.StatusDate + $Config.Tasks.Types.ISPswForDelete.DeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$ISPswOrder.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$ISPswOrder.IP|default:'$ISPswOrder.IP'}.
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$ISPswOrder.Balance|default:'$ISPswOrder.Balance'}
Тарифный план:        "{$ISPswOrder.SchemeName|default:'$ISPswOrder.SchemeName'}"
Стоимость продления:  {$ISPswOrder.Cost|default:'$ISPswOrder.Cost'}

