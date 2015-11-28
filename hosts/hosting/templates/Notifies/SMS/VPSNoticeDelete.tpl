{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

{assign var=ExpDate value=$VPSOrder.StatusDate + $Config.Tasks.Types.VPSForDelete.DeleteTimeout * 24 * 3600}
Оканчивается срок блокировки Вашего заказа #{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) IP адрес: {$VPSOrder.IP|default:'$VPSOrder.IP'}
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

