{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок действия заказа на хостинг" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на хостинг.
До окончания заказа {$Params.Item.DaysRemainded|default:'$Params.Item.DaysRemainded'} дн.
Паркованный домен: {$Params.Item.Domain|default:'$Params.Item.DaysRemainded'}.

{$Params.From.Sign|default:'$Params.From.Sign'}
