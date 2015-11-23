{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Оканчивается срок блокировки Вашего выделенного сервера, заказ #{$DSOrder.OrderID|string_format:"%05u"}, IP адрес {$DSOrder.IP|default:'$DSOrder.IP'}
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

