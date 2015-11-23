{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Оканчивается срок действия Вашего заказа #{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) IP адрес: {$VPSOrder.IP|default:'$VPSOrder.IP'}
До окончания заказа {$VPSOrder.DaysRemainded|default:'$VPSOrder.DaysRemainded'} дн.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

