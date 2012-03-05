{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Ответ на запрос №{$Params.TicketID|string_format:"%08u"} с темой: {$Params.Theme|default:'$Params.Theme'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Для Вас, от службы поддержки поступило новое сообщение, в запросе №{$Params.TicketID|string_format:"%08u"}, с темой: {$Params.Theme|default:'$Params.Theme'}.

---
{$Params.Message|default:'$Params.Message'}
---

Для просмотра истории запроса или нового ответа, пройдите по ссылке:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Tickets?Email={$Params.User.Email|default:'$Params.User.Email'}&Password={$Params.User.UniqID|default:'$Params.User.UniqID'}

{$Params.From.Sign|default:'$Params.From.Sign'}

