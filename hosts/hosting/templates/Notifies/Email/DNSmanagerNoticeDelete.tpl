{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на вторичный DNS [{$Login|default:'$Login'}]" scope=global}
{assign var=ExpDate value=$StatusDate + $Config.Tasks.Types.DNSmanagerForDelete.DNSmanagerDeleteTimeout * 24 * 3600}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок блокировки Вашего заказа №{$OrderID|string_format:"%05u"} на вторичный DNS, логин {$Login|default:'$Login'}.
Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{$From.Sign|default:'$From.Sign'}


