{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ выделенного IP адреса удален #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на выделенный IP [{$Login|default:'$Login'}], под номером {$Item.OrderID|string_format:"%05u"}, был удален.

Для нового заказа, воспользуйтесь этой ссылкой:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/ExtraIPSchemes

