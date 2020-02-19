{*
 *  Joonte Billing System
 *  Copyright © 2013 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на хостинг [{$HostingOrder.Login|default:'$HostingOrder.Login'}]" scope=global}
Оканчивается срок действия заказа хостинга ({$HostingOrder.Login|default:'$HostingOrder.Login'}), осталось {$HostingOrder.DaysRemainded|default:'$HostingOrder.DaysRemainded'} дн

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

