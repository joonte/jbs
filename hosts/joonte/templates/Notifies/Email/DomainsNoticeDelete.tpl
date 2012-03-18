{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Оканчивается срок блокировки заказа на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}" scope=global}
{assign var=ExpDate value=$StatusDate + 2678400}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, оканчивается срок блокировки Вашего заказа №{$ID|string_format:"%05u"}, на регистрацию домена [{$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}]. Дата удаления заказа {$ExpDate|date_format:"%d.%m.%Y"}

{$From.Sign|default:'$From.Sign'}

