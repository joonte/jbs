{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Удалён заказ на выделенный сервер" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на выделенный сервер был удален.
{if $DSScheme.IPaddr || $IP != 'noassign'}
{if $IP != 'noassign'}
IP адрес заказа:	{$IP}
{else}
IP адрес заказа:	{$DSScheme.IPaddr}
{/if}
{/if}
С этого момента сервер больше не закреплен за Вами и будет восстановлен в исходное состояние. Вся информация при этом будет удалена.

---
Вы всегда можете заказать другой сервер:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/DSSchemes

