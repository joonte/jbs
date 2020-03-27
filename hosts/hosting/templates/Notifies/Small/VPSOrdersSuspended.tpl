{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Ваш заказ #{$OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS) был заблокирован {$StatusDate|date_format:"%d.%m.%Y"}
IP адрес: {$IP|default:'$IP'}

