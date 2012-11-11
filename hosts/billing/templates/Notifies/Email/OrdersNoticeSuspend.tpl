{*
 *  Joonte Billing System
 *  Copyright © 2012 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на услугу ({$Order.Name|default:'$Order.Name'}), заказ #{$Order.ID|default:'$Order.ID'}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа #{$Order.ID|string_format:"%05u"}
на услугу "{$Order.Name|default:'$Order.Name'}".
До окончания заказа {$Order.DaysRemainded|default:'$Order.DaysRemainded'} дн.

{$From.Sign|default:'$From.Sign'}
