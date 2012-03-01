{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.Item.OrderID|string_format:"%05u"} на хостинг, c паркованным доменом {$Params.Item.Domain|default:'$Params.Item.Domain'}, был заблокирован.

{$Params.From.Sign|default:'$Params.From.Sign'}
