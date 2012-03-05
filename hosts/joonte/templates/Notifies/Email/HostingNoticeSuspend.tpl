{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на хостинг" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$Params.OrderID|string_format:"%05u"} на хостинг.
До окончания заказа {$Params.DaysRemainded|default:'$Params.DaysRemainded'} дн.
Паркованный домен: {$Params.Domain|default:'$Params.DaysRemainded'}.

{$Params.From.Sign|default:'$Params.From.Sign'}
