{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается заказ на выделенный IP адрес #{$ExtraIPOrder.OrderID|string_format:"%05u"}/[{$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается оплаченный срок Вашего заказа №{$ExtraIPOrder.OrderID|string_format:"%05u"} на выделенный IP адрес {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}
IP адрес: {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}
До удаления заказа {$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дней.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

