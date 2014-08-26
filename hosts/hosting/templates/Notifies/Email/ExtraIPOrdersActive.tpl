{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на выделенный IP адрес [{$Login|default:'$Login'}] активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на выделенный IP адрес был выполнен.
К вашему заказу добавлен адрес {$Login|default:'$Login'}

{$From.Sign|default:'$From.Sign'}
