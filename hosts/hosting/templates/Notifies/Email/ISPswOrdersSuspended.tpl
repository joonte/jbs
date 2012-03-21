{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ программного обеспечения заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$Domain|default:'$IP'}, был заблокирован.

{$From.Sign|default:'$From.Sign'}
