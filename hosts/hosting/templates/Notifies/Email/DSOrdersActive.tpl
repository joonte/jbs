{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Выделенный сервер подключен к сети" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Уведомляем Вас о том, что {$StatusDate|date_format:"%d.%m.%Y"} Ваш выделенный сервер был установлен.
Номер заказа №{$OrderID|string_format:"%05u"}, IP адрес {$IP|default:'$IP'}.
Дополнительная информация будет предоставлена в центре поддержки биллинговой панели.

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

