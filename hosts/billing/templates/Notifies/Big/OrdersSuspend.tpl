{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Оканчивается срок действия заказа на услугу ({$Order.Name|default:'$Order.Name'}), заказ #{$Order.ID|string_format:"%05u"}" scope=global}

Уведомляем Вас о том, что оканчивается срок действия Вашего заказа #{$Order.ID|string_format:"%05u"},
на услугу "{$Order.Name|default:'$Order.Name'}".
До окончания заказа {$Order.DaysRemainded|default:'$Order.DaysRemainded'} дн.

