{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на домен {$Params.Item.DomainName|default:'$Params.Item.DomainName'}.{$Params.Item.Name|default:'$Params.Item.Name'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

ведомляем Вас о том, оканчивается срок блокировки Вашего заказа №{$Params.Item.OrderID|string_format:"%05u"}, на регистрацию домена [{$Params.Item.DomainName|default:'$Params.Item.DomainName'}.{$Params.Item.Name|default:'$Params.Item.Name'}]. До удаления заказа {$Params.Item.StatusDate+2678400-$smarty.now|date_format:"%d.%m.%Y"}

{$Params.From.Sign|default:'$Params.From.Sign'}

