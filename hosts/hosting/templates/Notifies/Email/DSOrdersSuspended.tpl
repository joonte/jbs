{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер заблокирован" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер, заказ №{$OrderID|string_format:"%05u"}, был отключён.
IP адрес заказа: {$IP|default:'$IP'}

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

