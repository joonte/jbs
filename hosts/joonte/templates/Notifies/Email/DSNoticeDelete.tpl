{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки выделенного сервера" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего выделенного сервера, заказ №{$Params.Item.OrderID|string_format:"%05u"}, IP адрес {$Params.Item.IP|default:'$Params.Item.IP'}.
До удаления заказа {$Params.Item.StatusDate+86400-$smarty.now|date_format:"%d.%m.%Y"}.

{$Params.From.Sign|default:'$Params.From.Sign'}

