{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=Theme value="Окончился срок действия заказа на хостинг [{$Login|default:'$Login'}]" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш заказ №{$OrderID|string_format:"%05u"} на хостинг, логин {$Login|default:'$Login'}, c паркованным доменом {$Domain|default:'$Domain'}, был заблокирован.

--
Тарифный план:		"{$HostingScheme.SchemeName|default:'$HostingScheme.SchemeName'}"
Стоимость продления:	{$HostingScheme.Cost|default:'$HostingScheme.Cost'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

