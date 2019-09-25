{*
 *  Joonte Billing System
 *  Copyright © 2019 Alex Keda, for www.host-food.ru
 *}

<b>{$Theme|default:'$Theme'}</b>
---
{$Message|default:'$Message'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

