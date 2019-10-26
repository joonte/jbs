{*
 *  Joonte Billing System
 *  Copyright © 2019 Alex Keda, for www.host-food.ru
 *}
{$Theme|default:'$Theme'}.
---
{$Message|default:'$Message'}
---

Для просмотра истории запроса или нового ответа, пройдите по ссылке:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Administrator/Tickets

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

