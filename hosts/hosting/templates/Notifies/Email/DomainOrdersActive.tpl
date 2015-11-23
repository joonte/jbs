{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Заказ на домен {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} активирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ домена №{$OrderID|string_format:"%05u"} [{$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}] был активирован.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

