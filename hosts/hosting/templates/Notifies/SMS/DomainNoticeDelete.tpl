{*
 *  Joonte Billing System
 *  Copyright © 2013 Alex Keda, for www.host-food.ru
 *}
{assign var=ExpDate value=$StatusDate + 2678400}
Дата удаления домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'}: {$ExpDate|date_format:"%d.%m.%Y"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

