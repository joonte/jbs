{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на хостинг" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$Item.OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, домен {$Domain|default:'$Domain'}.
До удаления заказа {$StatusDate + $Config.Tasks.Types.HostingForDelete.DeleteTimeout * 24 * 3600 - $smarty.now|date_format:"%d.%m.%Y"}

{$From.Sign|default:'$From.Sign'}


