{*
 *  Copyright © 2013 Rootden for Dgrad-host.com
 *  rewritten by Alex Keda, for www.host-food.ru
 *}
Дата окончания домена {$DomainName|default:'$DomainName'}.{$DomainZone|default:'$DomainZone'}: {$ExpirationDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

