{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

В службу поддержки поступило новое сообщение по запросу №{$Params.TicketID|string_format:"%08u"}, с темой: {$Params.Theme|default:'$Params.Theme'}.

---
{$Params.Message|default:'$Params.Message'}
---

Для просмотра истории запроса или нового ответа, пройдите по ссылке:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Administrator/Tickets

{$Params.From.Sign|default:'$Params.From.Sign'}

