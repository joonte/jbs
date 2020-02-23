{*
 *  Joonte Billing System
 *  Copyright © 2020, Alex Keda for www.host-food.ru
 *}

Оканчивается срок заказа на прокси-сервер {$Host|default:'$Host'}:{$Port|default:'$Port'}, осталось {$ProxyOrder.DaysRemainded|default:'$ProxyOrder.DaysRemainded'} дн

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

