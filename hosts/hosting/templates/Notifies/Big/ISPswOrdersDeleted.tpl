{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Заказ программного обеспечения удален" scope=global}

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$IP|default:'$IP'}, был удален.

Для нового заказа, воспользуйтесь этой ссылкой:
{$smarty.const.URL_SCHEME|default:'URL_SCHEME'}://{$smarty.const.HOST_ID|default:'HOST_ID'}/ISPswSchemes

