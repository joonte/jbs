{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Заказ домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} заблокирован {$StatusDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

