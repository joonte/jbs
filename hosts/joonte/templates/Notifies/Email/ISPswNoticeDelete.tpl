{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на программное обеспечение" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$Params.Item.IP|default:'$Params.Item.IP'}.
До удаления заказа {$Params.Item.StatusDate + $Config.Tasks.Types.ISPswForDelete.DeleteTimeout * 24 * 3600 - $smarty.now|date_format:"%d.%m.%Y"}.

{$Params.From.Sign|default:'$Params.From.Sign'}
