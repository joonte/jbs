{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Заказ на регистрацию домена {$DomainName|default:'$DomainName'}.{$Name|default:'$Name'} был отправлен на регистрацию {$StatusDate|date_format:"%d.%m.%Y"}.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

