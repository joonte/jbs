{*
 *  Joonte Billing System
 *  Copyright © 2020 Alex Keda, for www.host-food.ru
 *}
{assign var=ThemeOrig value="{$Theme|default:'$Theme'}" scope=global}
{assign var=Theme value="Ответ на запрос №{$TicketID|string_format:"%08u"} с темой: {$Theme|default:'$Theme'}" scope=global}

Для Вас, от службы поддержки поступило новое сообщение, в запросе №{$TicketID|string_format:"%08u"}, с темой:
{$ThemeOrig|default:'$ThemeOrig'}.

---
{$Message|default:'$Message'}
---

Для просмотра истории запроса или нового ответа, пройдите по ссылке:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Tickets?Email={$User.Email|default:'$User.Email'}&Password={$User.UniqID|default:'$User.UniqID'}

