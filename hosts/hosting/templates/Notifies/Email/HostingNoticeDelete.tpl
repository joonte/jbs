{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на хостинг [{$Login|default:'$Login'}]" scope=global}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.HostingForDelete.DeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, домен {$Domain|default:'$Domain'}.
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{$From.Sign|default:'$From.Sign'}


