{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}

Заказ на прокси-сервер {$Host|default:'$Host'}:{$Port|default:'$Port'}, был заблокирован {$StatusDate|date_format:"%d.%m.%Y"}.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

