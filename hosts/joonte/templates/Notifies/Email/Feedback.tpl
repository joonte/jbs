{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

Поступило новое сообщение с сайта:

---
{$Params.Message|default:'$Params.Message'}
---

{$Params.From.Sign|default:'$Params.From.Sign'}
