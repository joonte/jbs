{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Новое сообщение от службы поддержки пользователей, запрос #{$TicketID|string_format:"%08u"}
{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

