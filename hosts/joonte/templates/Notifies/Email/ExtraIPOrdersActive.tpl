{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что {$Params.Item.StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$Params.Item.OrderID|string_format:"%05u"} на выделенный IP адрес был успешно выполнен.
К вашему заказу добавлен адрес {$Params.Item.Login|default:'$Params.Item.Login'}

{$Params.From.Sign|default:'$Params.From.Sign'}
