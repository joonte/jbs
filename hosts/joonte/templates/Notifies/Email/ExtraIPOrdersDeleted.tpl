{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ выделенного IP адреса удален" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что  {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на выделенный IP [{$Params.Item.Login|default:'$Params.Item.Login'}], под номером  {$Params.Item.OrderID|string_format:"%05u"}, был удален.

{$Params.From.Sign|default:'$Params.From.Sign'}
