{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ программного обеспечения удален" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.Item.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$Params.Item.Domain|default:'$Params.Item.IP'}, был удален.

{$Params.From.Sign|default:'$Params.From.Sign'}

