{*
 *  Joonte Billing System
 *  Copyright © 2013 Alex Keda, for www.host-food.ru
 *}

Заказ на вторичный DNS, логин {$Login|default:'$Login'}, был заблокирован {$StatusDate|date_format:"%d.%m.%Y"}.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

