{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ хостинга [{$Login|default:'$Login'}] удален" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, c паркованным доменом {$Domain|default:'$Domain'}, был удален.

Если этот заказ вам всё ещё необходим, обратитесь в техническую поддержку, мы восстановим заказ из бэкапа (по возможности).

Для нового заказа, воспользуйтесь этой ссылкой:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/HostingSchemes

