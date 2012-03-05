{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ выделенного IP адреса удален" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что  {$Params.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на выделенный IP [{$Params.Login|default:'$Params.Login'}], под номером  {$Params.Item.OrderID|string_format:"%05u"}, был удален.

{$Params.From.Sign|default:'$Params.From.Sign'}
