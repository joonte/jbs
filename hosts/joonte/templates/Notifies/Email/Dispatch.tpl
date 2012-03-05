{*
 *  Joonte Billing System
 *  Copyright © 2012 Vitaly Velikodnyy
 *}
{assign var=Theme value="{$Params.Theme|default:'$Params.Theme'}" scope=global}
Здравствуйте, {$Params.User.Name|default:'$Params.User.Name'}!

---
{$Params.Message|default:'$Params.Message'}
---

{$Params.From.Sign|default:'$Params.From.Sign'}

