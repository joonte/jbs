{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного IP адреса" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на выделенный IP адрес был успешно выполнен.
К вашему заказу добавлен адрес {$Login|default:'$Login'}

{$From.Sign|default:'$From.Sign'}
