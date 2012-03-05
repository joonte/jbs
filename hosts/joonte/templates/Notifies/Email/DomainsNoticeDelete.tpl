{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на домен {$Params.DomainName|default:'$Params.DomainName'}.{$Params.Name|default:'$Params.Name'}" scope=global}
{assign var=ExpDate value=$Params.StatusDate + 2678400}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Уведомляем Вас о том, оканчивается срок блокировки Вашего заказа №{$Params.ID|string_format:"%05u"}, на регистрацию домена [{$Params.DomainName|default:'$Params.DomainName'}.{$Params.Name|default:'$Params.Name'}]. Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{$Params.From.Sign|default:'$Params.From.Sign'}

