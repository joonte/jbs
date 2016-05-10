{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на виртуальный сервер #{$VPSOrder.OrderID|string_format:"%05u"}/[{$VPSOrder.Login|default:'$VPSOrder.Login'}]" scope=global}
{assign var=ExpDate value=$VPSOrder.StatusDate + $Config.Tasks.Types.VPSForDelete.DeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
IP адрес: {$VPSOrder.IP|default:'$VPSOrder.IP'}
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

