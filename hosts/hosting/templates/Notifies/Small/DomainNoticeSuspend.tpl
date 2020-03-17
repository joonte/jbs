{*
 *  Copyright © 2013 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
Дата окончания домена {$DomainOrder.DomainName|default:'$DomainOrder.DomainName'}.{$DomainOrder.DomainZone|default:'$DomainOrder.DomainZone'}: {$DomainOrder.ExpirationDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

