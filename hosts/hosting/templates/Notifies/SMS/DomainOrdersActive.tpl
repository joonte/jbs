{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Ваш заказ домена #{$OrderID|string_format:"%05u"} [{$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}] был активирован {$StatusDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

