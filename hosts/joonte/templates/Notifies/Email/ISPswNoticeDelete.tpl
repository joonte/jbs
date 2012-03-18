{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на программное обеспечение" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$Item.OrderID|string_format:"%05u"} на ПО ISPsystem, IP адрес {$Item.IP|default:'$Item.IP'}.
До удаления заказа {$Item.StatusDate + $Config.Tasks.Types.ISPswForDelete.DeleteTimeout * 24 * 3600 - $smarty.now|date_format:"%d.%m.%Y"}.

{$From.Sign|default:'$From.Sign'}
