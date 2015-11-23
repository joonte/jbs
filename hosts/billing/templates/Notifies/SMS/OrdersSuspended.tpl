{*
 *  Joonte Billing System
 *  Copyright © 2014 Alex Keda, for www.host-food.ru
 *}

Заказ на ({$Service.Name|default:'$Service.Name'}), номер заказа #{$ID|string_format:"%05u"}, был заблокирован {$StatusDate|date_format:"%d.%m.%Y"}.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

