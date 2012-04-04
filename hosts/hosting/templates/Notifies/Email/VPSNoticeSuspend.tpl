{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на виртуальный сервер" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$VPSOrder.OrderID|string_format:"%05u"} на виртуальный выделенный сервер (VPS).
До окончания заказа {$VPSOrder.DaysRemainded|default:'$VPSOrder.DaysRemainded'} дн.
IP адрес: {$VPSOrder.Login|default:'$VPSOrder.Login'}

{$From.Sign|default:'$From.Sign'}

