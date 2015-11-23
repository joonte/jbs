{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Окончился срок действия заказа на хостинг [{$Login|default:'$Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, c паркованным доменом {$Domain|default:'$Domain'}, был заблокирован.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

