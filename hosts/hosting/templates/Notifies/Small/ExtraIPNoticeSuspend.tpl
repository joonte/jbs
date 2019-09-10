{*
 *  Joonte Billing System
 *  Copyright © 2015 Alex Keda, for www.host-food.ru
 *}

Оканчивается заказ выделенного IP адреса {$ExtraIPOrder.Login|default:'$ExtraIPOrder.Login'}; до окончания заказа {$ExtraIPOrder.DaysRemainded|default:'$ExtraIPOrder.DaysRemainded'} дн.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

