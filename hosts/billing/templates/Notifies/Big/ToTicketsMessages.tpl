{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=ThemeOrig value="{$Theme|default:'$Theme'}" scope=global}
{assign var=Theme value="Ответ на запрос №{$TicketID|string_format:"%08u"} с темой: {$Theme|default:'$Theme'}" scope=global}

В службу поддержки поступило новое сообщение по запросу №{$TicketID|string_format:"%08u"}, с темой:
[b]{$ThemeOrig|default:'$ThemeOrig'}[/b].

---
{$Message|default:'$Message'}
---
[size=10][color=gray]Для просмотра истории запроса или нового ответа, пройдите по ссылке:
http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Administrator/Tickets[/color][/size]
