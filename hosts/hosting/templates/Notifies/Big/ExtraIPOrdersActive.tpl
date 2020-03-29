{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ на выделенный IP адрес [{$Login|default:'$Login'}] активирован" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на выделенный IP адрес был выполнен.
К вашему заказу добавлен адрес {$Login|default:'$Login'}

