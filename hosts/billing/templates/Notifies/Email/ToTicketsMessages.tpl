{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="Ответ на запрос №{$TicketID|string_format:"%08u"} с темой: {$Theme|default:'$Theme'}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

В службу поддержки поступило новое сообщение по запросу №{$TicketID|string_format:"%08u"}, с темой: {$Theme|default:'$Theme'}.

---
{$Message|default:'$Message'}
---

Для просмотра истории запроса или нового ответа, пройдите по ссылке:

http://{$smarty.const.HOST_ID|default:'HOST_ID'}/Administrator/Tickets

{$From.Sign|default:'$From.Sign'}

