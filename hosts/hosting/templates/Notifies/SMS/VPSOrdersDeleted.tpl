{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Ваш заказ на виртуальный выделенный сервер (VPS) #{$OrderID|string_format:"%05u"} был удален {$StatusDate|date_format:"%d.%m.%Y"}
IP адрес: {$IP|default:'$IP'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

