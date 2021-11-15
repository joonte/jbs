{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Удалён заказ виртуального сервера #{$OrderID|string_format:"%05u"} логин ({$Login|default:'$Login'})" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на виртуальный выделенный сервер (VPS) №{$OrderID|string_format:"%05u"} был удален.
Логин:		{$Login|default:'$Login'}
IP адрес:	{$IP|default:'$IP'}

Если этот заказ вам всё ещё необходим, обратитесь в техническую поддержку, мы восстановим заказ из бэкапа (по возможности).

Для нового заказа, воспользуйтесь этой ссылкой:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/VPSSchemes

