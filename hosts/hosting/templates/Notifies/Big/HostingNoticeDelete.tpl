{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на хостинг [{$HostingOrder.Login|default:'$HostingOrder.Login'}]" scope=global}
{assign var=ExpDate value=$HostingOrder.StatusDate + $Config.Tasks.Types.HostingForDelete.HostingDeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$HostingOrder.OrderID|string_format:"%05u"} на хостинг, логин {$HostingOrder.Login|default:'$HostingOrder.Login'}, домен {$HostingOrder.Domain|default:'$HostingOrder.Domain'}.
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}


