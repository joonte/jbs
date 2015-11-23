{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="{$Theme|default:'$Theme'}" scope=global}
Здравствуйте, {$User.Name|default:'$User.Name'}!

---
{$Message|default:'$Message'}
---

{if !$MethodSettings.CutSign}
--
{$From.Sign|default:'$From.Sign'}

{/if}

