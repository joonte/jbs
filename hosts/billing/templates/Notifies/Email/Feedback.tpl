{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
Здравствуйте, {$User.Name|default:'$User.Name'}!

Поступило новое сообщение с сайта:

---
{$Message|default:'$Message'}
---

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

