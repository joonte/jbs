{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ выделенного IP адреса удален" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что  {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на выделенный IP [{$Login|default:'$Login'}], под номером  {$Item.OrderID|string_format:"%05u"}, был удален.

{$From.Sign|default:'$From.Sign'}
