{*
 *  Joonte Billing System
 *  Copyright © 2014 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Закончился срок действия заказа на услугу ({$Service.Name|default:'$Service.Name'}), номер заказа #{$ID|string_format:"%05u"}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ на услугу ({$Service.Name|default:'$Service.Name'}), номер заказа #{$ID|string_format:"%05u"}, был заблокирован.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

