{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на прокси-сервер [{$Host|default:'$Host'}:{$Port|default:'$Port'}]" scope=global}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.ProxyForDelete.ProxyDeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$OrderID|string_format:"%05u"} на прокси-сервер, {$Host|default:'$Host'}:{$Port|default:'$Port'}.
Дата удаления заказа: {$ExpDate|date_format:"%d.%m.%Y"}
Баланс договора:      {$ProxyOrder.Balance|default:'$ProxyOrder.Balance'}
Тарифный план:        "{$ProxyOrder.SchemeName|default:'$ProxyOrder.SchemeName'}"
Стоимость продления:  {$ProxyOrder.Cost|default:'$ProxyOrder.Cost'}


{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}


