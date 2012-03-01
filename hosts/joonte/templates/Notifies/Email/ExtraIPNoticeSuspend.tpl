{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (ExtraIP).
До окончания заказа {$Params.Item.DaysRemainded|default:'$Params.Item.DaysRemainded'} дн.
IP адрес: {$Params.Item.Login|default:'$Params.Item.Login'}

{$Params.From.Sign|default:'$Params.From.Sign'}
