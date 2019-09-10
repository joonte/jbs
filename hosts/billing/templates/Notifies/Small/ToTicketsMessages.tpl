{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

В службу поддержки поступило новое сообщение по запросу #{$TicketID|string_format:"%08u"}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

