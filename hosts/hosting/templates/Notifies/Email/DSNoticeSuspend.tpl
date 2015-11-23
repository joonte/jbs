{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа выделенного сервера" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок аренды выделенного сервера, заказ №{$DSOrder.OrderID|string_format:"%05u"}.
До окончания заказа {$DSOrder.DaysRemainded|default:'$DSOrder.DaysRemainded'} дн.
IP адрес: {$DSOrder.IP|default:'$DSOrder.IP'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

